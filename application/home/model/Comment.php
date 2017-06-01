<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/14
 * Time: 14:50
 */

namespace app\home\model;


use think\Model;

class Comment extends Model {
    protected $insert = [
        'comments' => 0,
        'likes' => 0,
        'create_time' => NOW_TIME,
        'status' => 0,
        'score' => 1,
    ];

    /**
     * 获取评论
     * @param $type
     * @param $aid
     * @param $uid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getComment($type,$aid,$uid) {
        $map = array(
            'type' => $type,
            'aid' => $aid,
            'status' => 0
        );
        $comment = $this->where($map)->order('likes desc,create_time desc')->limit(7)->select();
        foreach ($comment as $value) {
            $user = WechatUser::where('userid',$value['uid'])->find();
            $value['nickname'] = $user['name'];
            $value['header'] = $user['headimgurl'];
            $map1 = array(
                'type' => 0,
                'aid' => $value['id'],
                'uid' => $uid,
                'status' => 0,
            );
            $like = Like::where($map1)->find();
            ($like) ? $value['is_like'] = 1 : $value['is_like'] = 0;
        }
        return $comment;
    }
}