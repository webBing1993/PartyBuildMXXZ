<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\admin\model\Action;
use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use app\admin\model\Member;
use app\user\controller\Index;
use app\user\model\UcenterMember;
use org\Page;
use think\Config;
use think\Input;

/**
 * 后台用户控制器
 */
class User extends Admin 
{
    /**
     * 用户管理首页
     */
    public function index(){
        $nickname       =   input('nickname');
        $map['status']  =   array('egt',0);
        if(is_numeric($nickname)){
            $map['id|nickname']=   array(intval($nickname),array('like','%'.$nickname.'%'),'_multi'=>true);
        }else{
            $map['nickname']    =   array('like', '%'.(string)$nickname.'%');
        }

        $list = $this->lists("Member", $map);
//        foreach ($list as $key => $value) {
//            $msg = UcenterMember::get($value['id']);
//            $value['email'] = $msg['email'];
//        }
        $this->assign('list',$list);

        $info['status'] = array('egt', 0);
        $authGroups = AuthGroup::where($info)->select();
        $this->assign('authGroups',$authGroups);

        return $this->fetch();
    }

    /**
     * 修改昵称初始化
     */
    public function updateNickname(){
        $nickname = M('Member')->getFieldByUid(UID, 'nickname');
        $this->assign('nickname', $nickname);
        $this->meta_title = '修改昵称';
        $this->display('updatenickname');
    }

    /**
     * 修改昵称提交
     */
    public function submitNickname(){
        //获取参数
        $nickname = I('post.nickname');
        $password = I('post.password');
        empty($nickname) && $this->error('请输入昵称');
        empty($password) && $this->error('请输入密码');

        //密码验证
        $User   =   new UserApi();
        $uid    =   $User->login(UID, $password, 4);
        ($uid == -2) && $this->error('密码不正确');

        $Member =   D('Member');
        $data   =   $Member->validate()->create(array('nickname'=>$nickname));
        if(!$data){
            $this->error($Member->getError());
        }

        $res = $Member->where(array('uid'=>$uid))->save($data);

        if($res){
            $user               =   session('user_auth');
            $user['username']   =   $data['nickname'];
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            return $this->success('修改昵称成功！');
        }else{
            return $this->error('修改昵称失败！');
        }
    }

    /**
     * 修改密码初始化
     */
    public function updatePassword(){
        $this->meta_title = '修改密码';
        $this->display('updatepassword');
    }

    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitPassword(){
        //获取参数
        $password   =   I('post.old');
        empty($password) && $this->error('请输入原密码');
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if($data['password'] !== $repassword){
            return $this->error('您输入的新密码与确认密码不一致');
        }

        $Api    =   new UserApi();
        $res    =   $Api->updateInfo(UID, $password, $data);
        if($res['status']){
            return $this->success('修改密码成功！');
        }else{
            return $this->error($res['info']);
        }
    }

    /**
     * 用户行为列表
     */
    public function action(){
        //获取列表数据
        $where = array('status'=>array('gt',-1));
        $list = $this->lists("Action", $where);
        int_to_string($list);
        $this->assign('_list', $list);

        // 记录当前列表页的cookie
        cookie('__forward__',$_SERVER['REQUEST_URI']);
        return $this->fetch();
    }

    /**
     * 新增行为
     */
    public function addAction(){
        $this->assign('data',null);
        $this->display('editaction');
    }

    /**
     * 编辑行为
     */
    public function editAction(){
        $data = Action::get(input('id'));
        $this->assign('data',$data);

        return $this->fetch('editaction');
    }

    /**
     * 更新行为
     */
    public function saveAction(){
        $res = D('Action')->update();
        if(!$res){
            return $this->error(D('Action')->getError());
        }else{
            return $this->success($res['id']? '更新成功！':' 新增成功！', cookie('__forward__'));
        }
    }

    /**
     * 会员状态修改
     */
    public function changeStatus($method=null){
        $id = array_unique((array)input('id/a',0));
        if( in_array(Config::get('user_administrator'), $id)){
            return $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            return $this->error('请选择要操作的数据!');
        }
        $map['id'] = array('in',$id);
        switch ( strtolower($method) ){
            case 'forbiduser':
                $data = ['status'=> 0];
                break;
            case 'resumeuser':
                $data = ['status'=> 1];
                break;
            case 'deleteuser':
                $data = ['status'=> -1];
                break;
            default:
                return $this->error('参数非法');
        }

        $result = Member::where($map)->update($data);
        if($result) {
            return $this->success('更新成功');
        } else {
            return $this->error('更新失败');
        }
    }

    public function add($username = '', $password = '', $repassword = '', $email = '') {
        //检查用户名
        if(empty($username)){
            return $this->error("账号不能为空！");
        }else{
            $exsit = UcenterMember::where('username',$username)->find();
            if($exsit){
                return $this->error("账号已存在，请更换账号名称！");
            }
        }
        if(empty($email)) {
            return $this->error("邮箱不能为空！");
        }else{
            $s = UcenterMember::where('email',$email)->find();
            if($s){
                return $this->error("邮箱已存在，请更改邮箱注册！");
            }
        }
        /* 检测密码 */
        if($password != $repassword){
            return $this->error('密码和重复密码不一致！');
        }
        if (strlen($password) < 6 || strlen($password) > 50) {
            return $this->error('密码至少6位数');
        }
        /* 调用注册接口注册用户 */
        $uid = action('user/index/register', [$username, $password, $email]);
        //$uid = $UserApi->register($username, $password, $email);
        if(0 < $uid){   //注册成功
            AuthGroupAccess::create(['uid'=>$uid, 'group_id'=>input('group_id')]);
            return $this->success('用户添加成功！');
        } else {
            return $this->error($uid);
        }
    }

    public function edit() {
        $nickname = input('username');
        $email = input('email');
        $groupId = input('group_id');
        $id = input('id');
        $password = input('password');
        $repassword = input('repassword');
        //更改昵称
        $result1 = array();
        if($nickname){
            $map1['nickname'] = $nickname;
            $result1 = Member::where('id',$id)->update($map1);
        }
        //更改邮箱
        $result2 =array();
        if($email){
            $find = UcenterMember::where('email',$email)->find();
            $map2['email'] = $email;
            if($find){
                return $this->error('邮箱已存在，请另输入邮箱！！');
            }else{
                UcenterMember::where('id',$id)->update($map2);
            }
        }
        //更改密码
        $result3 = array();
        if(!empty($password)) {
            if($password != $repassword){
                return $this->error('密码和重复密码不一致！');
            }else{
                $map3['password'] = think_ucenter_md5($repassword, config('uc_auth_key'));
                $result3 = UcenterMember::where('id',$id)->update($map3);
            }
        }
        //更改用户组
        $result4 = array();
        if($groupId){
            $user = AuthGroupAccess::where(['uid'=>$id])->find();
            if($user){
                $map4 = array(
                    'group_id' => $groupId,
                );
                $result4 = AuthGroupAccess::where(['uid'=>$id])->update($map4);
            }else{
                $map5 = array(
                    'group_id' => $groupId,
                    'uid' => $id,
                );
                $result4 = AuthGroupAccess::create($map5);
            }
        }

        if($result1 || $result2 || $result3 || $result4) {
            return $this->success('更新成功');
        } else {
            return $this->error('更新失败');
        }
    }

}