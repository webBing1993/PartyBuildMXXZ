<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/3/28
 * Time: 12:58
 */
namespace app\home\model;
use think\Model;

class Feedback extends Model{
    protected $insert = [
        'create_time' => NOW_TIME
    ];
}