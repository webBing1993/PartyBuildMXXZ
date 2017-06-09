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

class Details extends Controller{
    /**
     * 新闻详情页
     * @return mixed
     */
    public function index(){
        //判断是不是微信打开
//        $this ->oauth();
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
    /*
    * 判断当天积分是否达到上限
    */
    public function score_up(){
        $con = strtotime(date("Y-m-d",time()));  //  获取当天年月日时间戳
        $userid = session('userId');
        $map = array(
            'create_time' => ['egt',$con],
            'user_id' => $userid,
        );
        $map1 = array(
            'create_time' => ['egt',$con],
            'uid' => $userid,
            'score' => 1
        );
        $map2 = array(
            'create_time' => ['egt',$con],
            'userid' => $userid
        );
        $browse = Browse::where($map)->count(); //  浏览得分
        $like = Like::where($map1)->count();  // 点赞得分
        $comment = Comment::where($map1)->count();  // 评论得分
        $Answer = Answers::where($map2)->find();
        $answer = $Answer['score'];  // 答题得分
        $num = $browse + $like + $comment + $answer;
        if ($num < 15){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 判断是否是游客登录
     */
    public function anonymous() {
        //读取本地cookie
        Cookie::init(['prefix'=>'think_','expire'=>31533600,'path'=>'/']);
        session('userId',Cookie::get('dypb')['user']);

        $userId = session('userId');
        //如果userId等于visitor  则为游客登录，否则则正常显示
        if(empty($userId)){
            $this->assign('visit', 1);
            session('userId','visitor');
        }else{
            $this->assign('visit', 0);
        }
    }
    /**
     * 获取公众号签名
     */
    public function jssdk(){
        $Wechat = new TPWechat(Config::get('party'));
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $jsSign = $Wechat->getJsSign($url);
        $this->assign("jsSign", $jsSign);
    }
    /**
     * 判断是否微信打开
     */
    public function oauth(){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            // 非微信浏览器禁止浏览
            return $this ->error('请在微信打开!');
        } else {
            // 微信浏览器，允许访问
            // 获取版本号
            preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
        }
    }
}