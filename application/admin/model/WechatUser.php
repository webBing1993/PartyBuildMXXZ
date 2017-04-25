<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/5/19
 * Time: 9:43
 */

namespace app\admin\model;


use think\Model;

class WechatUser extends Base
{
    public function WechatMessage() {
        return $this->belongsTo('WechatMessage');
    }

    public function Wechat_department() {
        return $this->belongsToMany('Wechat_department','Wechat_department_user');
    }

    public function Wechat_tag() {
        return $this->belongsToMany('Wechat_tag','Wechat_user_tag');
    }
}