<?php
/**
 * Created by PhpStorm.
 * User: stiff
 * Date: 24/11/17
 * Time: 下午1:16
 */

namespace app\home\model;
use think\Model;

class SmsCode extends Model
{
    protected $insert = [
        'status' => 0,
        'create_time' => NOW_TIME
    ];
}