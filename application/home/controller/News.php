<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/1
 * Time: 16:20
 */
namespace app\home\controller;
use app\home\model\News as NewsModel;
use app\home\model\Picture;

class News extends Base {
    //党建之家首页
    public function index(){
        $news = new NewsModel();
        //轮播推荐
        $lists = $news ->where(['status' => ['egt',0],'recommend' => 1]) ->order('create_time desc') ->select();
        $list2 = $news ->where(['status' => ['egt',0]]) ->order('create_time desc') ->limit(6)->select();
        $this ->assign('lists',$lists);
        $this ->assign('list2',$list2);
        return $this ->fetch();
    }

    /**
     * 加载更多
     */
    public function listmore(){
        $len = input("length");
        $news = new NewsModel();
        $list = $news ->where(['status' => ['egt',0]])->order('create_time desc')->limit($len,5)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功",Null,$list);
        }else{
            return $this->error("加载失败");
        }
    }
}