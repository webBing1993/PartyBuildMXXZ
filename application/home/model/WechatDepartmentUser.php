<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/28
 * Time: 15:15
 */

namespace app\home\model;
use think\Model;

class WechatDepartmentUser extends Model {
    public function user(){
        return $this->hasOne('WechatUser','userid','userid');
    }
}