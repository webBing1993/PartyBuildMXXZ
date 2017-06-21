<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/6/17
 * Time: 17:40
 */
namespace app\home\controller;
use app\home\model\WechatDepartment;
use app\home\model\Wish as  wishModel;
use app\home\model\Comment;
use app\home\model\Wish;
use app\home\model\Picture;
use app\home\model\WishVote;
use app\user\model\WechatUser;

/**
 *  活动发起  控制器
 */
class Activity extends Base{
    /*
     * 活动发起 主页 
     */
    public function index(){
        $this->checkAnonymous();
        $userId = session('userId');
        $Wish = new wishModel();
        $list = $Wish->where(['type' => 1,'status' => 0])->order('id desc')->limit(5)->select();  // 活动列表
        $lists = $Wish->where(['type' => 2 ,'status' => 0])->order('id desc')->limit(5)->select(); // 投票
        foreach($lists as $value){
            $User = WechatUser::where('userid',$value['create_user'])->field('department,headimgurl')->find();
            $Depart = WechatDepartment::where('id',$User['department'])->field('name')->find();
            $value['head'] = $User['headimgurl'];
            $value['department'] = $Depart['name'];
            //  获取  图片
            $value['images'] = json_decode($value['images']);
            // 获取  赞成 或者  反对
            $likeModel = new WishVote();
            $like = $likeModel->getLike($value['id'],$userId);
            $value['is_like'] = $like;  //  0 未投票    1  赞成  2 反对
            // 获取评论
            $commentModel = new Comment();
            $comment = $commentModel->getComment(6,$value['id'],$userId);
            $value['comment'] = $comment;
        }
        $this->assign('list',$list);
        $this->assign('lists',$lists);
        return $this ->fetch();
    }
    /*
     * 活动  列表 更多
     */
    public function morelist(){
        $this->checkAnonymous();
        $userId = session('userId');
        $Wish = new wishModel();
        $type = input('post.type');
        $len = input('post.length');
        if ($type == 1){
            // 活动  列表
            $list = $Wish->where(['type' => 1,'status' => 0])->order('id desc')->limit($len,5)->select();  // 活动列表
            foreach($list as $value){
                $value['time'] = date('Y-m-d',$value['create_time']);
            }
        }else{
            // 投票  
            $list = $Wish->where(['type' => 2,'status' => 0])->order('id desc')->limit($len,5)->select();  // 投票
            foreach($list as $value){
                $User = WechatUser::where('userid',$value['create_user'])->field('department,headimgurl')->find();
                $Depart = WechatDepartment::where('id',$User['department'])->field('name')->find();
                $value['head'] = $User['headimgurl'];
                $value['department'] = $Depart['name'];
                //  获取  图片
                $value['images'] = json_decode($value['images']);
                $image =array();
                foreach ($value['images'] as $k=>$val){
                    $img = Picture::get($val);
                    $image[$k] = $img['path'];
                }
                $value['images'] = $image;
                $value['create_time'] = date('Y-m-d',$value['create_time']);
                // 获取  赞成 或者  反对
                $likeModel = new WishVote();
                $like = $likeModel->getLike($value['id'],$userId);
                $value['is_like'] = $like;  //  0 未投票    1  赞成  2 反对
                // 获取评论
                $commentModel = new Comment();
                $comment = $commentModel->getComment(6,$value['id'],$userId);
                $value['comment'] = $comment;
            }
        }
        return $this->success('','',$list);
    }
    /* 活动发起   详情 */
    public function activitydetails(){
        $userId = session('userId');
        $this->checkAnonymous();
        $id = input('get.id/d');
        $list = Wish::where(['id' => $id,'status' => 0])->find();
        if (empty($list)){
            return $this->error('系统错误,数据不存在');
        }
        // 认领权限
        $User = WechatUser::where('userid',$userId)->field('review,department')->find();
        $list['review'] = $User['review'];
        // 已认领名单
        $Receive = db('wish_receive')->where(['rid' => $id,'status' => 0])->select();
        foreach($Receive as $key => $value){
            $User = WechatUser::where('userid',$value['userid'])->field('name,department,headimgurl')->find();
            $Receive[$key]['name'] = $User['name'];
            $Receive[$key]['head'] = $User['headimgurl'];
            $Receive[$key]['department'] = WechatDepartment::where('id',$User['department'])->value('name');
        }
        $list['receive'] = $Receive;
        // 判断自己所在部门     是否已经认领
        $department = $User['department'];
        $info = db('wish_receive')->where(['rid' => $id,'department' => $department,'status' => 0])->find();
        if ($info){
            $list['is_receive'] = 1;  //   同部门 其他管理员  已经认领
        }else{
            //  判断自己是否 认领
            $infoes = db('wish_receive')->where(['rid' => $id,'userid' => $userId,'status' => 0])->find();
            if ($infoes){
                // 自己已经认领
                $list['is_receive'] = 1;
            }else{
                $list['is_receive'] = 0;  // 未认领
            }
        }
        //活动基本信息
        $list['user'] = $userId;
        //分享图片及链接及描述
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].'/home/images/feedback/feedback.jpg';
        $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $list['desc'] = $list['description'];
        
        $this->assign('info',$list);
        return $this ->fetch();
    }
    /*
     * 活动 认领
     */
    public function enroll(){
        $this->checkAnonymous();
        $id = input('post.id/d');
        $list = Wish::where(['id' => $id,'status' => 0])->find();
        if (empty($list)){
            return $this->error('系统错误,数据不存在');
        }
        $userId = session('userId');
        $department = WechatUser::where('userid',$userId)->value('department');
        $res = db('wish_receive')->insert(['rid' => $id,'userid' => $userId,'department' => $department,'create_time' => time(),'status' => 0]);
        if ($res){
            // 返回 用户数据
            $User = WechatUser::where('userid',$userId)->field('name,department,headimgurl')->find();
            $User['department'] = WechatDepartment::where('id',$User['department'])->value('name');
            $User['time'] = date('Y-m-d',db('wish_receive')->where(['rid' => $id,'userid' => $userId])->value('create_time'));
            return $this->success('认领成功','',$User);
        }else{
            return $this->error('认领失败');
        }
    }
    /*
     * 投票
     */
    public function vote(){
        $this->checkAnonymous();
        $userId = session('userId');
        $id = input('post.id');
        $status = input('post.status');
        $Vote = new  WishVote();
        $res = $Vote->save(['userid' => $userId,'vote_id' => $id,'status' => $status]);
        if ($res){
            if ($status == 2){
               // 反对
                wishModel::where(['id' => $id,'status' => 0])->setInc('likes_no');
            }else{
                // 赞成
                wishModel::where(['id' => $id,'status' => 0])->setInc('likes_yes');
            }
            return $this->success('成功');
        }else{
            return $this->error('失败');
        }
    }
    /*
     * 投票  发布
     */
    public function publish(){
        $this->checkAnonymous();
        $data = input('post.');
        $uid = session('userId');
        if(empty($data))
        {
            return $this ->fetch();
        }else{
            $wishModel = new wishModel();
            $data['type'] = 2;
            $data['images'] = json_encode($data['images']);
            $data['publisher'] = get_name($uid);
            $data['create_time'] = time();
            $data['create_user'] = $uid;
            $data['status'] = 0;
            $wishModel ->data($data) ->save();
            if($wishModel ->id)
            {
                return $this ->success('发布成功!');
            }else{
                return $this ->error('发布失败!');
            }
        }
    }
}