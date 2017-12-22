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
    //推送
    public function push($info,$idArr=[]) {
        if(empty($idArr)) {
            $arr = $this ->where($info)->where('type',2)->select();
        } elseif(!empty($idArr) && $idArr[0] == 2) {
            $arr = $this ->where($info) ->where(['type'=>2, 'id'=>['neq',$idArr[1]]])->select();
        } else {
            $arr = $this ->where($info)->where('type',2)->select();
        }

        foreach($arr as  $value){
            $value['title'] = '【梦想快讯】'.$value['title'];
            $value['id'] = '2-'.$value->id;
        }
        return $arr;
    }
}