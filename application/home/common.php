<?php
/**
 * 截取字符串长度
 * @author：linben
 */
function subtext($text, $length) {
    if(mb_strlen($text, 'utf8') > $length)
        return mb_substr($text, 0, $length, 'utf8').'...';
    return $text;
}

/**
 * json对象转数组，支持多维
 */
function ob2ar($obj) {
    if(is_object($obj)) {
        $obj = (array)$obj;
        $obj = ob2ar($obj);
    } elseif(is_array($obj)) {
        foreach($obj as $key => $value) {
            $obj[$key] = ob2ar($value);
        }
    }
    return $obj;
}
/**
 * 判断微信内置浏览器
 */
function is_weixin(){
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }
    return false;
}
/**
 * 格式图片地址
 */
function get_image_path($data, $field='front_cover') {
    foreach($data as $key=>$value) {
        $data[$key][$field] = get_cover($value[$field], 'path');
    }

    return $data;
}
/**
 * 隐藏手机号码中间部分内容
 */
function hide_name($name) {
    if(preg_match('/^1[34578]\d{9}$/', $name)){
        $name = mb_substr($name, 0, 3)."****".mb_substr($name, 7, 4);
    }

    return $name;
}

/**
 * @param $num
 * @return string
 * 把阿拉伯数字转成中文数字
 */
function to_chinase_num($num) {
    $char = array("零","一","二","三","四","五","六","七","八","九");
    $dw = array("","十","百","千","万","亿","兆");
    $retval = "";
    $proZero = false;
    for($i = 0;$i < strlen($num);$i++) {
        if($i > 0)
            $temp = (int)(($num % pow (10,$i+1)) / pow (10,$i));
        else
            $temp = (int)($num % pow (10,1));

        if($proZero == true && $temp == 0) continue;

        if($temp == 0)
            $proZero = true;
        else
            $proZero = false;

        if($proZero) {
            if($retval == "") continue;
            $retval = $char[$temp].$retval;
        } else
            $retval = $char[$temp].$dw[$i].$retval;
    }
    if(strpos($retval,"一十") === 0) $retval = mb_substr($retval,1, mb_strlen($retval), 'UTF-8');

    return $retval;
}
/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 */
function time_format($time = NULL,$format='Y-m-d H:i'){
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

function unlike_dir($path) {
    //先删除目录下的文件：
    $dh = opendir($path);
    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$path."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                unlike_dir($fullpath);
            }
        }
    }
}

function object2array(&$object) {
    $object =  json_decode( json_encode($object),true);
    return  $object;
}

function object2array_pre(&$object) {
    if(is_object($object)) {
        $object = (array)$object;
    } if(is_array($object)) {
        foreach($object as $key=>$value) {
            $object[$key] = object_array($value);
        }
    }
    return $object;
}

/**
 * 获取用户头像
 * @param varchar $userid
 */
function get_header($userid){
    if(empty($userid)){
        return false;
    }
    $map = array(
        'userid' => $userid,
    );
    $user = \app\home\model\WechatUser::where($map)->find();
    if(empty($user['headimgurl'])){
        $header = '/home/images/common/vistor.jpg';
    }else{
        $header = $user['headimgurl'];
    }
    return $header;
}
/**
 * 获取用户名称
 */
function get_name($userid){
    if(empty($userid)){
        return false;
    }
    $map = array(
        'userid' => $userid,
    );
    $user = \app\home\model\WechatUser::where($map)->find();
    if(empty($user['name'])){
        $name = '未定义';
    }else{
        $name = $user['name'];
    }
    return $name;
}

