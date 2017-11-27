<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/26
 * Time: 10:50
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Like;
use app\home\model\Report as ReportModel;
use app\home\model\Comment;
use app\home\model\Picture;
use app\home\model\ReportVote;
use app\user\model\WechatUser;
/**
 * Class Report
 * @package 网上述职
 */
class Report extends Base{
    /**
     * 主页列表
     */
    public function index(){
        $this->anonymous();
        $userId = session('userId');
        $model = new ReportModel();
        $map = array(
            'status' => array('egt',0),
        );
        $list = $model->where($map)->order('id desc')->limit(10)->select();
        foreach($list as $value){
            $value['year'] = date('Y', $value['create_time']);
            $value['month'] = date('m', $value['create_time']);
            $value['day'] = date('d', $value['create_time']);
        }
        $this->assign('list',$list);
        return $this ->fetch();
    }
    /**
     * 主页加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = array(
            'status' => array('egt',0),
        );
        $list = ReportModel::where($map)->order('id desc')->limit($len,5)->select();
        foreach($list as $value){
            $value['year'] = date('Y', $value['create_time']);
            $value['month'] = date('m', $value['create_time']);
            $value['day'] = date('d', $value['create_time']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 详情页
     */
    public function detail(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $learnModel = new ReportModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $learnModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'report_id' => $id,
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
        $article = $learnModel::get($id);
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
        $like = $likeModel->getLike(13,$id,$userId);
        $article['is_like'] = $like;
        $this->assign('article',$article);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(13,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }
    /*
     * 投票
     */
    public function vote(){
        $this->checkAnonymous();
        $userId = session('userId');
        $id = input('id');
        $Vote = new ReportVote();
        $res = $Vote->save(['userid' => $userId,'pid' => $id]);
        if ($res){
            ReportModel::where(['id' => $id,'status' => 0])->setInc('votes');
            return $this->success('成功');
        }else{
            return $this->error('失败');
        }
    }
}