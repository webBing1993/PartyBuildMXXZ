<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/5/20
 * Time: 16:03
 */

namespace app\admin\model;


use think\Model;

class WechatTag extends Base {
    public function Wechat_user() {
        return $this->belongsToMany('WechatUser','WechatTag');
    }
}