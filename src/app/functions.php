<?php
namespace App;

/**
 * @param    string      $string 加密内容
 * @param    string      $operation 加密动作
 * @param    string      $key 私钥
 * @param    int         $expiry 有效时间秒
 * @return   string      加密串
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length = 4;
    $key = md5($key);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for($i = 0; $i <= 255; $i++)
    {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for($j = $i = 0; $i < 256; $i++)
    {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for($a = $j = $i = 0; $i < $string_length; $i++)
    {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE')
    {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16))
        {
            return substr($result, 26);
        }else{
            return '';
        }
    }else{
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}   


function genToken($string = 'username=张三&userid=34&sign=1539772519'){        
    return authcode($string, $operation = 'ENCODE', $key = '123456', $expiry = 0);
}

/**
 * [tokenCheck token验证]
 * token生成规则
 * username=张三&userid=34&timestamp=1539772519
 * @param  string $token [token]
 * @return bool        [返回token是否有效]
 */
function tokenCheck($token){
    $toekn_str = \App\authcode($token, $operation = 'DECODE', $key = '123456', $expiry = 0);
    if (empty($toekn_str)) {
        throw new BadRequestException('需token',1);
    } 
    parse_str($toekn_str, $toekn_arr);
    // print_r($toekn_arr);
    $GLOBALS['userInfo'] = $toekn_arr;
    if ( $toekn_arr['sign'] < time()-604800 ) {
        throw new BadRequestException('token已过期',1);
    }
} 
