<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/20
 * Time: 15:50
 */
namespace app\home\controller;
use app\home\model\Pioneer as PioneerModel;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatUser;
//先锋引领
class Pioneer extends Base {
    /**
     * 先锋事迹详情页
     */
    public function detail()
    {
        //游客模式
        $this ->anonymous();
        $this ->jssdk();
        $userId = session('userId');
        $id = input('get.id');
        if(!empty($id))
        {
            $map = array('id' => $id,'status' => ['egt',0]);
            $res = PioneerModel::where($map) ->find();
            if(empty($res))
            {
                return $this ->error('该文章不存在或已删除!');
            }else{
                $Pioneer = new PioneerModel();
                //浏览加一
                $info['views'] = array('exp','`views`+1');
                $Pioneer::where('id',$id)->update($info);
                if($userId != "visitor"){
                    //浏览不存在则存入pb_browse表
                    $con = array(
                        'user_id' => $userId,
                        'pioneer_id' => $id,
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
                $list = $Pioneer::get($id);
                //分享图片及链接及描述
                $image = Picture::where('id',$list['front_cover'])->find();
                $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
                $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
                $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));

                //获取 文章点赞
                $likeModel = new Like;
                $like = $likeModel->getLike(5,$id,$userId);
                $list['is_like'] = $like;
                $this->assign('new',$list);

                //获取 评论
                $commentModel = new Comment();
                $comment = $commentModel->getComment(5,$id,$userId);
                $this->assign('comment',$comment);
                return $this->fetch();
            }
            return $this ->fetch();
        }else{
            return $this ->error('参数错误!');
        }
    }

    /**
     * 先锋引领首页
     * @return mixed
     */
    public function index(){
        $this ->anonymous();
        $userId = session('userId');
        $pioneer = new PioneerModel();
        $order = 'likes desc';
        //创业导师团
        $list1 = $pioneer ->where(['type' => 1,'status' => ['egt',0]]) ->order($order) ->select();
        //创客先锋队
        $list2 = $pioneer ->where(['type' => 2,'status' => ['egt',0]]) ->order($order) ->select();
        //先进事迹展
        $list3 = $pioneer ->where(['type' => 3,'status' => ['egt',0]]) ->limit(0,8) ->select();
        //非游客判断是否点赞
        if($userId != 'visitor'){
            $list1 = $this ->checkLIke($list1);
            $list2 = $this ->checkLIke($list2);
        }
        $this ->assign('list1',$list1);
        $this ->assign('list2',$list2);
        $this ->assign('list3',$list3);
        return $this ->fetch();
    }

    /**
     * 判断今日导师点赞
     * @param $data
     * @return mixed
     */
    public function checkLIke($data){
        //获取点赞
        $userId = session('userId');
        $likeModel = new Like;
        foreach($data as $v)
        {
            $like = $likeModel ->checkLike(5,$v['id'],$userId);
            $v['is_like'] = $like;
        }
        return $data;
    }

    /**
     * 导师点赞
     * @return array|void
     */
    public function like()
    {
        $data = input('post.');
        $like = new Like();
        $uid = session('userId');
        $dateStr = date('Y-m-d', time());
        //获取当天0点的时间戳
        $timestamp0 = strtotime($dateStr);
        $map = array(
            'create_time' => ['egt', $timestamp0],
            'type' => 5,
            'aid' => $data['aid'],
            'uid' => $uid
        );
        $res = $like->where($map)->find();
        //今日已点赞
        if (!empty($res)) {
            return $this->error('今日已点赞!');
        } else {
            $data['table'] = 'pioneer';
            $data['uid'] = $uid;
            $res = $like->data($data)->save();
            //点赞成功积分+1
            if ($res) {
                //判断今日积分是否超出
                $check = $this ->score_up();
                if($check){
                    WechatUser::where('userid', $uid)->setInc('score', 1);
                }
                PioneerModel::where('id', $data['aid'])->setInc('likes', 1);
                return $this->success("点赞成功");
            } else {
                return $this->error("点赞失败!");
            }
        }
    }
        /**
         * 加载更多
         * @return array|void
         */
        public function moreList()
        {
            $len = input("length");
            $news = new PioneerModel();
            $map = array('status' => ['egt',0],'type' => 3);
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
}