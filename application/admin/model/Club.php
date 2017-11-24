<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\admin\model;


use think\Model;

class Club extends Model {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 0,
    ];
}