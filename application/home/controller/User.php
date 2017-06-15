<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 15:34
 */

namespace app\home\controller;
use app\home\model\Comment;
use app\home\model\Learn;
use app\home\model\WechatDepartment;
use app\home\model\Picture;
use app\home\model\Browse;
use app\home\model\Feedback;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use think\Controller;
use think\Db;

/**
 * Class User
 * 用户个人中心
 */
class User extends Base {
    /**
     * 个人中心主页
     */
    public function index(){
        //是否游客登录
        $this->anonymous();
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        $this->assign('user',$user);
        return $this->fetch();
    }

    /**
     * 个人信息页
     */
    public function personal(){
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        $Department = WechatDepartment::where(['id'=>$user['department']])->find();
        $user['department'] = $Department['name'];
        switch ($user['gender']) {
            case 0:
                $user['sex'] = "未定义";
                break;
            case 1:
                $user['sex'] = "男";
                break;
            case 2:
                $user['sex'] = "女";
                break;
            default:
                break;
        }
        $this->assign('user',$user);

        return $this->fetch();
    }

    /**
     * 设置头像
     */
    public function setHeader(){
        $userId = session('userId');
        $header = input('header');
        $map = array(
            'header' => $header,
        );
        $info = WechatUser::where('userid',$userId)->update($map);
        if($info){
            return $this->success("修改成功");
        }else{
            return $this->error("修改失败");
        }
    }
    /**
     * 临时党员信息
     */
    public function eg() {
        $id = input('id');
        $user = WechatUser::where('userid',$id)->find();
        $Department = WechatDepartment::where(['id'=>$user['department']])->find();
        $user['department'] = $Department['name'];
        switch ($user['gender']) {
            case 0:
                $user['sex'] = "未定义";
                break;
            case 1:
                $user['sex'] = "男";
                break;
            case 2:
                $user['sex'] = "女";
                break;
            default:
                break;
        }
        $this->assign('user',$user);
        return $this->fetch();
    }
    /**
     * 积分商城
     */
    public function score() {
        return $this->fetch();
    }
    /**
     * 意见反馈
     */
    public function feedback() {
        return $this->fetch();
    }
    /*
     * 意见反馈  提交
     */
    public function feedbackup(){
        $data['content'] = input('post.content');
        $data['userid'] = session('userId');
        $Feedback = new Feedback();
        $res = $Feedback->save($data);
        if ($res){
            return $this->success('提交成功');
        }else{
            return $this->error('提交失败');
        }
    }
}
