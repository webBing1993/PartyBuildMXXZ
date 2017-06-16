<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/9
 * Time: 15:04
 */

namespace app\admin\model;


use think\Model;

class Notice extends Base {
    public $insert = [
        'likes' => 0,
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
    
    public function name(){
        return $this->hasOne('WechatUser','userid','userid');
    }
}