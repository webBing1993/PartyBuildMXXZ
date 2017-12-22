<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/1/16
 * Time: 15:10
 */
namespace app\admin\model;


class Special extends Base {
    protected $insert = [
        'views' => 0,
        'likes' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
    //推送
    public function push($info,$idArr=[]) {
        if(empty($idArr)) {
            $arr = $this ->where($info)->where('type',2)->select();
        } elseif(!empty($idArr) && $idArr[0] == 6) {
            $arr = $this ->where($info) ->where(['type'=>2, 'id'=>['neq',$idArr[1]]])->select();
        } else {
            $arr = $this ->where($info)->where('type',2)->select();
        }

        foreach($arr as  $value){
            $value['title'] = '【十九大专题】'.$value['title'];
            $value['id'] = '6-'.$value->id;
        }
        return $arr;
    }

}