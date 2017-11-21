<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/4/27
 * Time: 11:22
 */
/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function think_ucenter_md5($str, $key = 'ThinkUCenter'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 (单位:秒)
 * @return string
 */
function think_ucenter_encrypt($data, $key, $expire = 0) {
    $key  = md5($key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char =  '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x=0;
        $char  .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data,$i,1)) + (ord(substr($char,$i,1)))%256);
    }
    return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法
 * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key  加密密钥
 * @return string
 */
function think_ucenter_decrypt($data, $key){
    $key    = md5($key);
    $x      = 0;
    $data   = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data   = substr($data, 10);
    if($expire > 0 && $expire < time()) {
        return '';
    }
    $len  = strlen($data);
    $l    = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char  .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }else if (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 */
function get_cover($cover_id, $field = null){
    if(empty($cover_id)){
        return false;
    }
    $map = array(
        'status' => array('egt',0),
        'id' => $cover_id,
    );
    $picture = \app\home\model\Picture::where($map)->find();
    if($field == 'path'){
        if(!empty($picture['url'])){
            $picture['path'] = $picture['url'];
        }else{
            $picture['path'] = $picture['path'];
        }
    }
    return empty($field) ? $picture : $picture[$field];
}

/**
 * 统计所有数据
 * @param int $userId 用户ID3
 * @param string $event 相关统计表模型名字
 * @param int $source 相关目标的ID
 */
function save_log($userId, $event, $source = 0) {
    $department = \app\admin\model\WechatUser::where(['userid' => $userId])->value('department');
    $data = array(
        'userid' => $userId,
        'event' => $event,
        'source' => $source,
        'department_id' => json_decode($department)[0]  //TODO 获取多个部门
    );
    \app\admin\model\Log::create($data);
}
/*
 * 获取月份
 * @param str $time  时间格式
 */
function getMonth($time){
    $month = substr($time,5);
    $map = array('0','1');
    if(in_array(substr($month,0,1),$map)){
        //去掉前导零
        if(substr($month,0,1) == 0){
            return substr($month,1,1);
        }else{
            return substr($month,0,2);
        }
    }else{
        return substr($month,0,1);
    }
}
/*
 * 获取日
 */
function getDay($time){
    $num1 = strrpos($time,'.');
    if($num1 === false){
        $num2 = strrpos($time,'-');
        return substr($time,$num2+1);
    }elseif(substr($time,$num1+1)!='' && $num1 > 4){
        return substr($time,$num1+1);
    }
}
/**
 * 隐藏手机号码中间部分内容
 */
function hide_mobile($mobile) {
    return mb_substr($mobile, 0, 3)."****".mb_substr($mobile, 7, 4);

}
