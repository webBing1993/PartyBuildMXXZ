<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/11/21
 * Time: 10:27
 */

namespace app\home\controller;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Opinion;
use app\home\model\Picture;
use think\Db;

/**
 * Class Feedback
 * @package app\home\controller
 * 党员之声
 */
class Feedback extends Base {
    /**
     * 主页..
     */
    public function index() {
        $userId = session('userId');
        $map = array(
            'status' => array('eq',0),
        );
        $optionModel = new Opinion();
        $list = $optionModel->where($map)->order('id desc')->limit(7)->select();
        foreach ($list as $value) {
            //获取用户信息
            $value['images'] = json_decode($value['images']);
            $value['username'] = get_name($userId);
            $value['header'] = get_header($userId);
            //获取相关意见反馈评论
            $map1 = array(
                'aid' => $value['id'],
                'status' => 0,
                'type' => 2,
            );
            $comment = Comment::where($map1)->select();
            foreach ($comment as $k => $val){
                $val['username'] = get_name($val['uid']);
            }
            $value['comment'] = $comment;
            //是否点赞
            $map2 = array(
                'aid' => $value['id'],
                'status' => 0,
                'type' => 2,
                'uid' => $userId
            );
            $msg = Like::where($map2)->find();
            if($msg) {
                $value['is_like'] = 1;
            }else{
                $value['is_like'] = 0;
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 反馈提交页
     */
    public function publish() {
        if(IS_POST) {
            $userId = session('userId');
            $data = input('post.');
            $department = Db::table('pb_wechat_user')
                ->alias('a')
                ->join('pb_wechat_department b','a.department = b.id','LEFT')
                ->field('b.name')
                ->where('a.userid',$userId)
                ->find();
            $data['department_name'] = $department['name'];
            $data['images'] = json_encode($data['images']);
            $data['create_user'] = $userId;
            $opinionModel = new Opinion();
            $model = $opinionModel->create($data);
            if($model) {
                $map['status'] = 0;
                return $this->success("提交成功");
            }else{
                return $this->error("提交失败");
            }
        }else{
            return $this->fetch();
        }
    }

    /**
     * 加载更多
     */
    public function more(){

        $len = input('length');
        $map = array(
            'status' => array('eq',0)
        );
        $list = Opinion::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            //获取用户信息
            $value['images'] = json_decode($value['images']);
            $image =array();
            foreach ($value['images'] as $k=>$val){
                $img = Picture::get($val);
                $image[$k] = $img['path'];
            }
            $value['images'] = $image;
            $value['username'] = get_name($value['create_user']);
            $value['header'] = get_header($value['create_user']);
            //获取相关意见反馈评论
            $map1 = array(
                'aid' => $value['id'],
                'status' => 0,
                'type' => 2,
            );
            $comment = Comment::where($map1)->select();
            foreach ($comment as $k => $val){
                $val['username'] = get_name($val['uid']);
            }
            $value['comment'] = $comment;

            //是否点赞
            $map2 = array(
                'aid' => $value['id'],
                'status' => 0,
                'type' => 2,
            );
            $msg = Like::where($map2)->find();
            if($msg) {
                $value['is_like'] = 1;
            }else{
                $value['is_like'] = 0;
            }
            $value['time'] = date("Y.m.d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }
}