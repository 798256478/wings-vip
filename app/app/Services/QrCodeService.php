<?php

namespace App\Services;

use App\Models\User;
use QrCode;

class QrCodeService
{
    public function createCommodityQrCode($commodityId)
    {
//        dd(is_file('./robots.txt'));
        $qrCodePath = './upload/' . user_domain() . '/qrCode';
        if(!is_dir($qrCodePath)){
            mkdir($qrCodePath,0777,true);
        }
        $qrCodePath = './upload/' . user_domain() . '/qrCode/commodity_' . $commodityId . '.png';
        if(!file_exists($qrCodePath)){
            if(file_exists('./upload/' . user_domain() . '/logo/logo.png')){
                //这里merge()目录一定要加上public
                QrCode::format('png')->size(200)->merge('./public/upload/' . user_domain() . '/logo/logo.png',0.15)
                    ->generate('http://vip.goldwings.cn/wechat/mall/item/' . $commodityId,public_path($qrCodePath));
            }else{
                QrCode::format('png')->size(200)->generate('http://vip.goldwings.cn/wechat/mall/item/' . $commodityId,public_path($qrCodePath));
            }
        }
        return substr($qrCodePath,1);
    }
}
