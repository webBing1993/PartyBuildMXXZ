<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/28
 * Time: 14:37
 */
namespace app\home\model;
use think\Model;
class Years extends Model{
    protected  $insert = array(
        'create_time' => NOW_TIME
    );
}