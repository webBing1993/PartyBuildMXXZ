<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use think\Controller;
use app\home\model\WechatUser;
use app\home\model\Browse;
use app\home\model\Comment;
use app\admin\model\Picture;
use app\home\model\Club as ClubModel;
use app\home\model\ClubActivity as ClubActivityModel;
use app\home\model\Volunteer as VolunteerModel;
use app\home\model\Like;
use think\Request;

class Club extends Base{
    /*
     * 主页
     */
    public function index(){
//        $this->anonymous();
        $map = array(
            'status' => ['egt',0],
        );
        $clublist = ClubModel::where($map)->order('id desc')->limit(10)->select();
//        var_dump($clublist);die;
        $map2 = array(
            'status' => ['egt',0],
        );
        $volunteerlist = VolunteerModel::where($map2)->order('id desc')->select();
        $this->assign('clublist',$clublist);
        $this->assign('volunteerlist',$volunteerlist);
        return $this->fetch();
    }
    /**
     * 主页加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = array(
            'status' => ['egt',0],
        );
        $list = ClubModel::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['set_date'] = date("Y-m-d",$value['set_date']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /*
     * 社团详情页
     */
    public function detail(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $model = new ClubModel();

        $model = $model::get($id);
        $model['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$model['front_cover'])->find();
        $model['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $model['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $model['desc'] = str_replace('&nbsp;','',strip_tags($model['content']));
        $model['desc'] = str_replace(" ",'',$model['desc']);
        $model['desc'] = str_replace("\n",'',$model['desc']);

        $map = array(
            'status' => ['egt',0],
            'pid' => $id,
        );
        $list = ClubActivityModel::where($map)->order('id desc')->limit(3)->select();
        $this->assign('list',$list);
        $this->assign('model',$model);
        return $this->fetch();
    }
    /*
    * 志愿服务详情页
    */
    public function volunteer(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $model = new VolunteerModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $model::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'volunteer_id' => $id,
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
        $article = $model::get($id);
        $article['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$article['front_cover'])->find();
        $article['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $article['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $article['desc'] = str_replace('&nbsp;','',strip_tags($article['content']));
        $article['desc'] = str_replace(" ",'',$article['desc']);
        $article['desc'] = str_replace("\n",'',$article['desc']);

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(10,$id,$userId);
        $article['is_like'] = $like;
        $this->assign('article',$article);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(10,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }
    /*
     * 活动列表
     */
    public function activity(){
//        $this->anonymous();
        $id = input('id');
        $map = array(
            'status' => ['egt',0],
            'pid' => $id,
        );
        $list = ClubActivityModel::where($map)->order('id desc')->limit(10)->select();
        $this->assign('pid',$id);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 活动加载更多
     */
    public function activitymore(){
        $len = input('length');
        $id = input('id');
        $map = array(
            'status' => ['egt',0],
            'pid' => $id,
        );
        $list = ClubActivityModel::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['set_date'] = date("Y-m-d",$value['set_date']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     * 活动详情页
     */
    public function activitydetail(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $model = new ClubActivityModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $model::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'club_activity_id' => $id,
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
        $article = $model::get($id);
        $article['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$article['front_cover'])->find();
        $article['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $article['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $article['desc'] = str_replace('&nbsp;','',strip_tags($article['content']));
        $article['desc'] = str_replace(" ",'',$article['desc']);
        $article['desc'] = str_replace("\n",'',$article['desc']);

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(9,$id,$userId);
        $article['is_like'] = $like;
        $this->assign('article',$article);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(9,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }
}
