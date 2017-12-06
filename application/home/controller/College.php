<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 16:12
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatUser;
use think\Controller;
use app\home\model\Notice as NoticeModel;
use app\home\model\Matter as MatterModel;
use think\Db;

/**
 * Class Notice
 * @package  通知公告
 */
class College extends Base {
    /**
     * 主页
     */
    public function index(){
        $this->anonymous(); //判断是否是游客
        //通知轮播
        $topNotice = NoticeModel::getThreeNotice();
        $this->assign('top_notice',$topNotice);
        //相关通知
        $map = array(
            'status' => array('egt',0)
        );
        $list = NoticeModel::where($map)->order('id desc')->limit(10)->select();
//        var_dump($list);die;
        /*foreach($list as $value){
            $value['is_over'] = 0;  // 未结束
            if (!empty($value['end_time']) && $value['end_time'] < time()){
                $value['is_over'] = 1;  // 已结束
            }
        }*/
        $list2 = MatterModel::where($map)->order('id desc')->limit(10)->select();
        $this->assign('list',$list);
        $this->assign('list2',$list2);
        return $this->fetch();
    }

    /**
     * 更多  通知 重要文件
     */
    public function leadlistmore(){
        $len = input('length');
        $type = input('type');
        $map = array(
            'status' => array('egt',0),
        );
        if ($type == 1){
            $list = NoticeModel::where($map)->order('id desc')->limit($len,6)->field('id,title,create_time,end_time')->select();
        }else{
            $list = MatterModel::where($map)->order('id desc')->limit($len,6)->field('id,title,create_time')->select();
        }
        foreach($list as $value){
            $value['create_time'] = date("Y-m-d",$value['create_time']);
            /*$value['is_over'] = 0;  // 未结束
            if (!empty($value['end_time']) && $value['end_time'] < time()){
                $value['is_over'] = 1;  // 已结束
            }*/
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     *  相关通知  活动通知 详细页
     */
    public function forumnotice(){
        //判断是否是游客
        $this->anonymous();
        $this->jssdk();
        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
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
        //活动基本信息
        $list = $noticeModel::get($id);
        $list['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$list['front_cover'])->find();
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));
        $list['desc'] = str_replace(" ",'',$list['desc']);
        $list['desc'] = str_replace("\n",'',$list['desc']);

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(4,$id,$userId);
        $list['is_like'] = $like;
        $this->assign('info',$list);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(4,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }
    /**
     *  重要文件 详细页m
     */
    public function forummatter(){
        //判断是否是游客
        $this->anonymous();
        $this->jssdk();
        $userId = session('userId');
        $id = input('id');
        $noticeModel = new MatterModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'matter_id' => $id,
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
        //活动基本信息
        $list = $noticeModel::get($id);
        $list['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$list['front_cover'])->find();
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));
        $list['desc'] = str_replace(" ",'',$list['desc']);
        $list['desc'] = str_replace("\n",'',$list['desc']);

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(12,$id,$userId);
        $list['is_like'] = $like;
        $this->assign('info',$list);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(12,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }
}