<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/1
 * Time: 17:10
 */
namespace app\home\controller;
use think\Controller;
use app\home\model\News;
use app\home\model\Browse;
use  app\home\model\WechatUser;
use app\home\model\Picture;
use app\home\model\Like;
use app\home\model\Comment;
use app\home\model\Answers;
use think\Cookie;
use com\wechat\TPWechat;
use think\Config;

class Details extends Base{
    /**
     * 新闻详情页
     * @return mixed
     */
    public function index(){
        //判断是否是游客
        $this ->anonymous();
        //获取jssdk
        $this ->jssdk();
        $userId = session('userId');
        $id = input('id');
        $newsModel = new News();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $newsModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'news_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                $s['score'] = array('exp','`score`+1');
                if ($this->score_up()){
                    // 未满 15 分
                    Browse::create($con);
                    WechatUser::where('userid',$userId)->update($s);
                }
            }
        }

        //新闻基本信息
        $list = $newsModel::get($id);
        $list['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$list['front_cover'])->find();
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(1,$id,$userId);
        $list['is_like'] = $like;
        $this->assign('new',$list);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(1,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }
}