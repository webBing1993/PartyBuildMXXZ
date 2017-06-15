<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/10
 * Time: 17:36
 */
namespace app\admin\model;
use think\Model;
class Opinion extends Model{
    //获取后台用户名称
    public function username(){
        return $this->hasOne('WechatUser','userid','create_user');
    }
}