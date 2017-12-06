<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\home\model;


use think\Model;

class StyleVote extends Model {
    protected $insert = [
        'status' => 1,
        'create_time' => NOW_TIME,
    ];
    //  获取 是否 同意
    public function getLike($pid,$uid) {
        $map = array(
            'pid' => $pid,
            'userid' => $uid,
        );
        $res = WishVote::where($map)->field('status')->find();  // 0 是未投票 1 是赞成 2 反对
        if (empty($res)){
            $res['status'] = 0;
        }
        return $res['status'];
    }
}