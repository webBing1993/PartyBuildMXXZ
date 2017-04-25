<?php
namespace app\admin\model;

use think\Model;

class Member extends Base
{
    protected $insert = [ 'status'=>1 ];
    /**
     * 用户权限组等三张表关联
     * @return string|\think\db\Query
     */
    public function roles() {
        return $this->belongsToMany('AuthGroup', 'pb_auth_group_access', 'group_id', 'uid');
    }

    public function lists($status = 1, $order = 'uid DESC', $field = true){
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

    /**
     * 登录指定用户
     * @param  integer $id 用户ID
     * @return boolean true-登录成功，false-登录失败
     */
    public function login($id){
        /* 检测是否在当前应用注册 */
        $user = $this->get($id);
        if(!$user || $user['status'] != 1) {
            $this->error = '用户不存在或已被禁用！'; //应用级别禁用
            return false;
        }

        //记录行
        //action_log('user_login', 'member', $id, $id);

        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }

    /**
     * 注销当前用户
     */
    public function logout(){
        session(null);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'login_total'     => array('exp', '`login_total`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->save($data, ['id'=>$user['id']]);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'id'              => $user['id'],
            'username'        => $user['nickname'],
            'header'          => $user['header'],
            'last_login_time' => $user['last_login_time'],
        );

        $access = AuthGroupAccess::where('uid='.$user['id'])->find();
        if(!empty($access)) $auth['group_name'] = $access->group->title;

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
    }

    /**
     * 获取用户昵称
     * @param $uid
     * @return mixed
     */
    public function getNickName($uid){
        return $this->where(['uid'=>$uid])->value('nickname');
    }

    /**
     * 检测昵称重复
     * @param $nickName
     * @return bool
     */
    public function checkName($nickName){
        $result = $this->where(['nickname'=>$nickName])->find();
        if($result) {
            return false;
        }else {
            return true;
        }
    }
}