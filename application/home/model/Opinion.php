<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/11/21
 * Time: 10:57
 */

namespace app\home\model;
use think\Model;

/**
 * Class Opinion
 * @package app\home\model
 * 意见反馈表
 */
class Opinion extends Model {
    public $insert = [
        'likes' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
    ];

    public function user() {
        return $this->hasOne('WechatUser','userid','create_user');
    }
}