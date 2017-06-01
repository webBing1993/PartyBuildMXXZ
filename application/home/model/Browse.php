<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/14
 * Time: 16:00
 */

namespace app\home\model;


use think\Model;

class Browse extends Model {
    protected $insert = [
        'status' => 0,
        'create_time' => NOW_TIME,
        'score' => 1,
    ];
}