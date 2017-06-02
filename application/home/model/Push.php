<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/2
 * Time: 13:40
 */
namespace app\home\model;
use think\Model;

class Push extends Model{
    public $insert = array(
        'create_time' => NOW_TIME,
    );
}