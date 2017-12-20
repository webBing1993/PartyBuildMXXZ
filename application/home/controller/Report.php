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
use app\home\model\Display as DisplayModel;
use app\home\model\Style as StyleModel;
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
        $list = $model->where($map)->order('type,id desc')->limit(10)->select();

        $model = new StyleModel();
        $map2 = array(
            'status' => array('egt',0),
        );
        $list2 = $model->where($map2)->order('id')->limit(10)->select();

        $model = new DisplayModel();
        $map3 = array(
            'status' => array('egt',0),
        );
        $list3 = $model->where($map3)->order('id desc')->limit(10)->select();
        /*foreach($list as $value){
            $value['year'] = date('Y', $value['create_time']);
            $value['month'] = date('m', $value['create_time']);
            $value['day'] = date('d', $value['create_time']);
        }*/
        $this->assign('list',$list);
        $this->assign('list2',$list2);
        $this->assign('list3',$list3);
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
        $list = ReportModel::where($map)->order('type,id desc')->limit($len,5)->select();
        foreach($list as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
//            $value['year'] = date('Y', $value['create_time']);
//            $value['month'] = date('m', $value['create_time']);
//            $value['day'] = date('d', $value['create_time']);
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
        if($article['type'] == 1){
            $article['record_text'] = "党委语音述职";
        }elseif($article['type'] == 2){
            $article['record_text'] = "工会语音述职";
        }elseif($article['type'] == 3){
            $article['record_text'] = "团委语音述职";
        }elseif($article['type'] == 4){
            $article['record_text'] = "妇联语音述职";
        }else{
            $article['record_text'] = "语音述职";
        }

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

        $Vote = new ReportVote();
        $data = ['userid' => $userId, 'pid' => $id];
        if(empty($Vote->where($data)->find())){
            $is_vote = true;
        }else{
            $is_vote = false;
        }
        $this->assign('is_vote',$is_vote);
        $this->assign('pid',$id);
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
        $data = ['userid' => $userId, 'pid' => $id];
        if(empty($Vote->where($data)->find())){
            $res = $Vote->save($data);
            if ($res){
                ReportModel::where(['id' => $id,'status' => 0])->setInc('votes');
                return $this->success('成功');
            }else{
                return $this->error('失败');
            }
        }else{
            return $this->error('已投票');
        }


    }

    /*
     * 测试
     */
    public function cese(){
        return $this->fetch();
    }

}