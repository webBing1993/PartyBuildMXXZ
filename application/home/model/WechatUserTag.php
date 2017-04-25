<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/5/23
 * Time: 14:52
 */

namespace app\home\model;


use think\Model;

class WechatUserTag extends Model {

    public function user(){
        return $this->hasOne('WechatUser','userid','userid');
    }
}