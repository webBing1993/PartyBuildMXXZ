<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/2
 * Time: 10:33
 */
namespace app\home\controller;
use app\home\model\News;
use app\home\model\Push;
use app\home\model\PushReview;

class Review extends Base{
    public function index(){
        return $this ->fetch();
    }
    //推送审核
    public function reviewlist(){
        $news = new News();
        $lists = $news ->where(['status' => 0,'push' => 1]) ->order('create_time desc') ->select();
        $this ->assign('list',$lists);
        return $this ->fetch();
    }
    //新闻推送审核
    public function review(){
        $data = input('post.');
        $push = new Push();
        $pushR = new PushReview();
        $userid = session('userId');
        if(!empty($data)){
            $record = $push ->data([
                'class' => 1,
                'focus_main' => $data['id'],
                'create_user' => $userid,
                'status' => $data['status']
            ]) ->save();
            if($record){
                $pushR ->data([
                    'push_id' => $record,
                    'user_id' => $userid,
                    'username' => get_name($userid),
                    'status' => $data['status']
                ]) ->save();
                //把状态修改为已审核
                $news = new News();
                $news ->save(['status' => 1],['id' => $data['id']]);
                return $this ->success('操作成功');
            }else{
                return $this ->error('操作失败,请刷新后重试!');
            }
        }else{
            return $this ->error('参数错误!');
        }
    }
    //新闻审核结果
    public function passlist(){
        $push = new PushReview();
        $list = $push ->order('review_time desc') ->select();
        $this ->assign('list',$list);
        return $this ->fetch();
    }
}