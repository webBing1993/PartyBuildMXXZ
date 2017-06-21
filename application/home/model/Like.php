<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/14
 * Time: 14:51
 */

namespace app\home\model;


use think\Model;

class Like extends Model {
    protected $insert = [
        'create_time' => NOW_TIME,
        'score' => 1,
        'status' => 0,
    ];

    public function getLike($type,$aid,$uid) {
        $map = array(
            'type' => $type,
            'aid' => $aid,
            'uid' => $uid,
        );
        $like = Like::where($map)->find();
        ($like) ? $res = 1 : $res = 0;
        return $res;
    }

    /**
     * 先锋引领 导师点赞判断
     * @param $type
     * @param $aid
     * @param $uid
     * @return int
     */
    public function checkLike($type,$aid,$uid){
        $dateStr = date('Y-m-d', time());
        //获取当天0点的时间戳
        $timestamp0=strtotime($dateStr);
        $map = array(
            'type' => $type,
            'aid' => $aid,
            'uid' => $uid,
            'create_time' => ['egt',$timestamp0]
        );

        $like = Like::where($map)->find();
        ($like) ? $res = 1 : $res = 0;
        return $res;
    }
}