<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/8
 * Time: 13:52
 */

namespace app\admin\model;
use think\Model;
class Wish extends Model {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 0,
    ];

    public function user() {
        return $this->hasOne('Member','id','create_user');
    }
    //推送
    public function push($info,$idArr=[]) {
        if(empty($idArr)) {
            $arr = $this ->where($info)->where('type',1)->select();
        } elseif(!empty($idArr) && $idArr[0] == 1) {
            $arr = $this ->where($info) ->where(['type'=>1,'id'=>['neq',$idArr[1]]])->select();
        } else {
            $arr = $this ->where($info)->where('type',1)->select();
        }
        foreach($arr as  $value){
            $value['title'] = '【活动发布】'.$value['title'];
            $value['id'] = '1-'.$value->id;
        }
        return $arr;
    }

}