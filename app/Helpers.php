<?php

use App\Exceptions\WingException;
/**
	* API默认错误处理
	*
	* @param  Exception  $e
	* @return response
	*/
function json_exception_response(\Exception $e)
{
	$message = method_exists($e, 'getMessage') ? $e->getMessage() : 'could_not_login';
	$statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
    $errors = method_exists($e, 'getErrors') ? $e->getErrors() : [];
    $arr = [
        'message' => $message,
        'status_code' => $statusCode,
        'errors' => $errors,
    ];
	return response()->json($arr, $statusCode);
}

function user_domain()
{
    //return 'wings-vip';
    if (!isset($GLOBALS['user_domain'])){
        if (isset($_SERVER['HTTP_HOST'])){
            $host =  $_SERVER['HTTP_HOST'];
            $userDomain = substr($host, 0, stripos($host, '.'));
            $GLOBALS['user_domain'] = ($userDomain == 'vip' ? 'wings-vip' : $userDomain);
        }
        else{
            $args = $_SERVER['argv'];

            $domainArg = array_first($args, function ($key, $value){
               return strpos($value,'domain:') === 0;
            });

            $GLOBALS['user_domain'] = substr($domainArg, stripos($domainArg, ':') + 1);
        }
    }

    return $GLOBALS['user_domain'];
}

function convart_timestamp_to_display($show_time)
{
    $now_time = time();
    $dur = $now_time - $show_time;
    $dur_abs=abs($dur);
    $now_day=date('d');
    $show_day=substr(date("Y/m/d", $show_time), 8,2);

    //前后超过两天
    if($dur_abs > 2*24*60*60)
        return convart_timestamp_to_date($show_time);

    //前后两天
    if($now_day - $show_day == -1)
            return '明天';
    if($now_day - $show_day == 1)
            return '昨天';
    if($now_day - $show_day == -2)
            return '后天';
    if($now_day - $show_day == 2)
            return '前天';

    //当天
    $str = $dur < 0 ? '后' : '前';
    if ($dur_abs < 60)
        return $dur_abs.'秒'.$str;
    else if ($dur_abs < 3600 )
        return floor($dur_abs / 60) . '分钟'.$str;
    else
        return floor($dur_abs / 3600) . '小时'.$str;
}

function convart_timestamp_to_date($show_time)
{
    $now_time = time();
    $show_date = date("Y.m.d", $show_time);
    $now_year = date('Y');
    $show_year = substr($show_date, 0,4);
    if($show_year == $now_year)
        return date("m.d", $show_time);
    else
        return substr($show_date, 2, strlen($show_date)-2);
}

function current_domain()
{
    return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'vip.goldwings.cn';
}

/**
 * 从javascript时间转换为php用的时间
 * @method getTimeFromW3CTime
 * @param  string             $time javascript时间的字符串
 * @return string
 */
function getTimeFromW3CTime($time){
    $time = str_replace('T', ' ', $time);
    $time = str_replace('.000Z', ' +0000', $time);
    $date= \DateTime::createFromFormat("Y-m-d H:i:s T", $time);
    $date->setTimeZone(new \DateTimeZone('+0800'));
    return $date->format('Y-m-d H:i:s');
}

//得到guid
function getGuid() {
    if (function_exists('com_create_guid')) {
        return com_create_guid();
    } else {
        mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid = '{'
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12)
            . '}';
        return $uuid;
    }
}

//加密
function passwordEncrypt($str)
{
    $block_size = 32;
    $text_length = strlen($str);
    //计算需要填充的位数
    $amount_to_pad = $block_size - ($text_length % $block_size);
    if ($amount_to_pad == 0) {
        $amount_to_pad = $block_size;
    }
    //获得补位所用的字符
    $pad_chr = chr($amount_to_pad);
    $tmp = "";
    for ($index = 0; $index < $amount_to_pad; $index++) {
        $tmp .= $pad_chr;
    }
    $str .= $tmp;
    $a = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,config('yuda.mcrypt.key'),$str,MCRYPT_MODE_CBC,config('yuda.mcrypt.iv')));
    return $a;
}
