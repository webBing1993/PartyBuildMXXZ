<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/5/19
 * Time: 14:07
 */

namespace app\admin\model;


use think\Model;

class WechatDepartment extends Base
{
    public function Wechat_user() {
        return $this->belongsToMany('WechatUser','WechatDepartmentUser');
    }
}