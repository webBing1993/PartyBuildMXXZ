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
    public function index()
    {
        //检查游客权限
        $this ->anonymous();
        $news = new NewsModel();
        $order = 'create_time desc';
        //轮播推荐
        $lists = $news ->where(['status' => ['egt',0],'recommend' => 1]) ->order($order) ->limit(6) ->select();
        //数据列表
        $list2 = $news ->where(['status' => ['egt',0]]) ->order($order) ->limit(6)->select();
        $this ->assign('lists',$lists);
        $this ->assign('list2',$list2);
        return $this ->fetch();
    }
    /**
     * 加载更多
     * @return array|void
     */
    public function listmore()
    {
        $len = input("length");
        $news = new NewsModel();
        $map = array('status' => ['egt',0]);
        $order = 'create_time desc';
        $list = $news ->where($map) ->order($order) ->limit($len,5) ->select();
        //图片跟时间戳转化
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if(!empty($list))
        {
            return $this->success("加载成功",Null,$list);
        }else {
            return $this->error("加载失败");
        }
    }
    /**
     * 发布新闻
     */
    public function publish()
    {
        //检查游客权限
        $this ->checkAnonymous();
        $uid = session('userId');
        $data = input('post.');
        if(empty($data) ||
           !isset($data)){
            return $this ->fetch();
        }else{
            //保存数据
            $news = new NewsModel();
            $data['images'] = json_encode($data['images']);
            $data['publisher'] = get_name($uid);
            $data['create_time'] = time();
            $data['create_user'] = $uid;
            $news ->data($data) ->save();
            $res = $news ->id;
            if(empty($res))
            {
                return $this ->error('发布失败,请刷新页面后重试!');
            }else{
                return $this ->success('发布成功!');
            }
        }
    }

}