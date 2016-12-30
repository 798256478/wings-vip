<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Notice;
use App;
use TopClient;
use AlibabaAliqinFcSmsNumSendRequest;

class NoticeService
{

    /**
    * 发送通知
    *
    * @param array[string|Card] $cards 接受人id或Card对象列表,如果发送给所有人则投递'ALL'
    * @param array[array] $data 发送方式及内容
    *        [
    *            'APP' => ['content' => '', 'image' => '', 'expires_date' => ''],
    *            'WECHAT' => ['content' => '', 'mediaId' => ''],
    *            'SMS' => ['content1' => '', 'content2' => ''],
    *        ]
    * @return array 发送失败集合
    *        [
    *            'WECHAT' => [],
    *            'SMS' => [],
    *        ]
    */
    public function send($cards, $data)
    {
        $cards = $this->convertToCardArray($cards);

        $results = [];
        foreach ($data as $key => $value) {
            if ($key == 'APP') {
                $this->sendByApp($cards, $value['content'], $value['expires_date'], $value['image']);
            }
            elseif ($key == 'SMS') {
                $results['SMS'] = $this->sendBySMS($cards, $value['content1'], $value['content2']);
            }
            elseif ($key == 'WECHAT') {
                $results['WECHAT'] = $this->sendByWechat($cards, $value['content'], $value['mediaId']);
            }
            else {
                throw new \Exception('参数错误');
            }
        }
    }

    public function sendByApp($cards, $content, $expiresDate, $image = null)
    {
        $cardIds = [];
        if ($cards == 'ALL') {
            $cardIds = $cards;
        }
        else {
            $cards = $this->convertToCardArray($cards);
            foreach ($cards as $card) {
                $cardIds[] = $card->id;
            }
        }

        $notice = new Notice;
        $notice->cards = $cardIds;
        $notice->receivedCards = [];
        $notice->content = $content;
        $notice->image = $image;
        $notice->expires_date = $expiresDate;
        $notice->save();
    }

    public function sendBySMS($cards, $content1, $content2)
    {
        $cards = $this->convertToCardArray($cards);
        if ($cards == 'ALL') {
            $cards = Card::get();
        }
        $unableIds = [];
        $mobileStr = '';
        $count = 0;
        $config=config('customer.'.user_domain().'.sms');
        $tmpMsg = '{"name":"'.$config['signName'].'","event":"'.$content1.'","assets":"'.$content2.'"}';
        if (!defined("TOP_SDK_WORK_DIR")) {
            define("TOP_SDK_WORK_DIR", "/tmp/");
        }
        $c = new TopClient;
        $c->appkey = $config['appkey'];
        $c->secretKey = $config['secretKey'];
        foreach ($cards as $card) {
            if (isset($card['mobile']) && $card['mobile'] != '') {
                $mobileStr .= $card['mobile'] . ',';
                $count++;
            }
            if ($count == 200) {
                $mobileStr = substr($mobileStr, 0, strlen($mobileStr) - 1);
                $req = new AlibabaAliqinFcSmsNumSendRequest;
                $req->setExtend("00001");
                $req->setSmsType("normal");
                $req->setSmsFreeSignName($config['signName']);
                $req->setSmsParam($tmpMsg);
                $req->setRecNum($mobileStr);
                $req->setSmsTemplateCode('SMS_17330005');
                $resp = $c->execute($req);
                $mobileStr = '';
                $count = 0;
            }
        }
        if ($count != 0) {
            $mobileStr = substr($mobileStr, 0, strlen($mobileStr) - 1);
            $req = new AlibabaAliqinFcSmsNumSendRequest;
            $req->setExtend("");
            $req->setSmsType("normal");
            $req->setSmsFreeSignName($config['signName']);
            $req->setSmsParam($tmpMsg);
            $req->setRecNum($mobileStr);
            $req->setSmsTemplateCode('SMS_17330005');
            $resp = $c->execute($req);
        }
        return true;
    }

    public function sendByWechat($cards, $content, $mediaId)
    {
        $cards = $this->convertToCardArray($cards);
        $wechat = App::make('WechatApplication');
        $broadcast = $wechat->broadcast;
        if ($cards === 'ALL') {
            $unable = Card::where('month_receive', '>=', 4)->get()->toArray();
            if ($mediaId != '') {
                $broadcast->sendNews($mediaId);
            }else {
                $broadcast->sendText($content);
            }
            Card::increment('month_receive', 1);
            if (count($unable) == 0) {
                return [];
            }else {
                return array_column($unable, 'id');
            }
        }
        //查找发送次数达到4的人
        $unableIds = [];
        $enableIds = [];
        $enableOpenIds = [];
        foreach ($cards as $card) {
            if ($card->month_receive >= 4)
                $unableIds[] = $card->id;
            else {
                $enableIds[] = $card->id;
                $enableOpenIds[] = $card->openid;
            }
        }
        //分组发送信息
        if (count($enableOpenIds) > 0) {
            if (count($enableOpenIds) > 10000) {
                //当用户数为 n * 10000 + 1时需特殊处理，避免剩余单人无法推送
                if (count($enableOpenIds) % 10000 == 1) {
                    $tmpOpenIds = [$enableOpenIds[0], $enableOpenIds[1]];
                    if ($mediaId != '') {
                        $broadcast->sendNews($mediaId, $tmpOpenIds);
                    }else {
                        $broadcast->sendText($content, $tmpOpenIds);
                    }
                    array_splice($enableOpenIds, 0, 2);
                }
                //分组推送
                $partOpenIds = [];
                for ($i=0; $i < count($enableOpenIds); $i++) {
                    if($i % 10000 === 0){
                        if ($mediaId != '') {
                            $broadcast->sendNews($mediaId, $partOpenIds);
                        }else {
                            $broadcast->sendText($content, $partOpenIds);
                        }
                        $partOpenIds = [];
                    }
                    $partOpenIds[] = $enableOpenIds[$i];
                }

                if(count($partOpenIds) > 0){
                    if ($mediaId != '') {
                        $broadcast->sendNews($mediaId, $partOpenIds);
                    }else {
                        $broadcast->sendText($content, $partOpenIds);
                    }
                }
            }else {
                if ($mediaId != '') {
                    $broadcast->sendNews($mediaId, $enableOpenIds);
                }else {
                    $broadcast->sendText($content, $enableOpenIds);
                }
            }
            //更新月发送次数
            Card::whereIn('id', $enableIds)->increment('month_receive', 1);
        }
        //返回发送失败集合
        return $unableIds;
    }

    /**
    * 获取根据会员ID获取待阅读通知
    *
    * @param int $id 收件人会员卡id
    * @return array[Notice] 通知集合
    */
    public function getNoticesByCardId($id)
    {
        // return Notice::where('expires_date', '>', date('Y-m-d H:i:s', time()))
        //             ->whereIn('cards', ['ALL', $id])
        //             ->get();
        return Notice::where('expires_date', '>', date('Y-m-d H:i:s', time()))
                    ->where(function($query) use($id){
                        $query->where('cards', $id)
                              ->orWhere(function($q) use($id){
                                  $q->where('cards', 'ALL')
                                    ->whereNotIn('receivedCards', [$id]);
                              });
                    })
                    ->get();
    }

    /**
    * 收件（记录通知阅读）
    *
    * @param int $cardId 收件人会员卡id
    * @param string $id 通知id,空则表示将所有消息标记为已读
    * @return bool 成功
    */
    public function receive($cardId, $noticeId = null)
    {
        if ($noticeId === null) {
            $notices = $this->getNoticesByCardId($noticeId);
            foreach ($notices as $notice) {
                $this->doReceive($cardId, $notice);
            }
        }
        else {
            $this->doReceive($cardId, Notice::find($noticeId));
        }

        return true;
    }

    private function doReceive($cardId, $notice)
    {
        if ($notice->cards != 'ALL') {
            $offset = -1;
            foreach ($notice->cards as $key => $item) {
                if ($item == $cardId) {
                    $offset = $key;
                }
            }
            if ($offset != -1) {
                $cards = $notice->cards;
                array_splice($cards, $offset, 1);
                $notice->cards = $cards;
            }
        }

        $receivedCards = $notice->receivedCards;
        $receivedCards[] = $cardId;
        $notice->receivedCards = $receivedCards;

        $notice->save();
    }

    private function convertToCardArray($cards)
    {
        if ($cards == 'ALL'){
            return $cards;
        }elseif (is_array($cards) || is_object($cards[0])) {
            if (is_object($cards[0])) {
                return $cards;
            }elseif (is_int($cards[0])) {
                return Card::whereIn('id', $cards)->get();
            }else {
                throw new \Exception('参数错误');
            }
        }else {
            if (is_object($cards)) {
                return [$cards];
            }elseif (is_int($cards)) {
                return Card::where('id', $cards)->get();
            }else {
                throw new \Exception('参数错误');
            }
        }
    }
}
