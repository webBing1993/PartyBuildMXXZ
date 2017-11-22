<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/18
 * Time: 14:22
 */
namespace app\admin\model;
use think\Model;

class DreamFlash extends Model{
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 0,
    ];
}