<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/4/25
 * Time: 10:39
 */

namespace app\user\controller;

use app\user\model;
use app\user\model\UcenterMember;
use app\user\model\WechatUser;
use app\user\validate\Member;
use think\Config;
use think\Controller;

class Index extends Controller
{

    public function index() {

    }

    /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email    用户邮箱
     * @param  string $mobile   用户手机号码
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function register($username, $password, $email, $mobile = '') {
        $UcenterMemberModel = new UcenterMember();
        $data = [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'mobile' => $mobile,
            'nickname' => $username,
        ];
        // 验证手机
        if (empty($data['mobile'])) unset($data['mobile']);
        if (empty($data['email'])) unset($data['email']);
        
        $result = $UcenterMemberModel->validate(true)->save($data);
        if ($result) {
            return $result;//登录成功返回1
        } else {
            return $UcenterMemberModel->getError();//不成功返回原因
        }
    }

    /**
     * 添加一个微信通讯录用户
     * @param $user
     * @return int|string
     * @throws \think\Exception
     */
    public function addWechatUser($user){
        unset($user['errcode']);
        unset($user['errmsg']);

        $User = new WechatUser();
        $User->data($user);
        $result = $User->save();

        if(false === $result){
            return $User->getError();
        } else {
            return $result;
        }
    }

    /**
     * 更新本地微信通讯录用户
     * @param $user
     * @return mixed
     */
    public function updateWechatUser($user) {
        unset($user['errcode']);
        unset($user['errmsg']);

        return WechatUser::where('userid',$user['userid'])->update($user);
    }

    /**
     * 检查本地数据库中是否存在
     * @param $userId
     * @return bool
     */
    public function checkWechatUser($userId) {
        $user = WechatUser::where('userid',$userId)->find();
        if(!empty($user)) {
            return $user;
        } else {
            return false;
        }
    }

    public function reforgot_password($id,$password){
        $repassword = UcenterMember::get($id);
        $repassword->password=$password;
        $result = $repassword->validate(true)->save();
        if($result){
            return 1;
        } else{
            return 2;
        }
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1){
        switch ($type) {
            case 1:
                $user = UcenterMember::where('username', $username)->find();
                break;
            case 2:
                $user = UcenterMember::where('email', $username)->find();
                break;
            case 3:
                $user = UcenterMember::where('mobile', $username)->find();
                break;
            case 4:
                $user = UcenterMember::where('id', $username)->find();
                break;
            default:
                return 0; //参数错误
        }

        /* 获取用户数据 */
        if(!empty($user) && $user['status']){
            $UcenterMember = new UcenterMember();
            /* 验证用户密码 */
            if(think_ucenter_md5($password, Config::get('uc_auth_key')) === $user['password']){
                $UcenterMember->updateLogin($user['id']); //更新用户登录信息
                return $user['id']; //登录成功，返回用户ID
            } else {
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }
    }

    /**
     * 获取用户信息
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid, $is_username = false){

    }

    /**
     * 检测用户名
     * @param  string  $field  用户名
     * @return integer         错误编号
     */
    public function checkUsername($username){

    }

    /**
     * 检测邮箱
     * @param  string  $email  邮箱
     * @return integer         错误编号
     */
    public function checkEmail($email){

    }

    /**
     * 检测手机
     * @param  string  $mobile  手机
     * @return integer         错误编号
     */
    public function checkMobile($mobile){

    }

    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author huajie <banhuajie@163.com>
     */
    public function updateInfo($uid, $password, $data){

    }

    /**
     * 检测用户名是否已经禁用
     * @param  string  $field  用户名
     * @return bool|mixed
     */
    public function checkMemberExist($username){

    }

}