<?php
namespace app\admin\controller;

use app\admin\model\Activity;
use app\admin\model\Comment;
use app\admin\model\Config;
use app\admin\model\Course;
use app\admin\model\Focus;
use app\admin\model\Log;
use app\admin\model\WechatLog;
use app\admin\model\WechatUser;
use app\user\model\UcenterMember;
use think\Controller;
use think\Db;

class Index extends Admin {

    public function index() {
        // 通讯录总人数
//        $userTotal = WechatUser::where(['status'=>1])->count();
//        // 活动通知数
//        $activityTotal = Activity::count();
//        // 评论数
//        $commentTotal = Comment::where(['status'=>0])->count();
//        // 课程数
//        $courseTotal = Course::count();
//        $this->assign('userTotal', $userTotal);
//        $this->assign('activityTotal', $activityTotal);
//        $this->assign('commentTotal', $commentTotal);
//        $this->assign('courseTotal', $courseTotal);
//        //浏览数
//        $courseView = Course::where('status','eq','1')->column('views');
//        $focusView = Focus::where('status','eq',1)->column('views');
//        $activityView = Activity::where('status','eq',1)->column('views');
//        $all = array_merge($courseView,$focusView,$activityView);
//        //合并数值
//        $viewTotal = 0;
//        foreach ($all as $k=>$v){
//            $viewTotal += $v;
//        }
//        $this->assign('viewTotal',$viewTotal);
//
//        // 用户分析
//        $activeUser = WechatLog::all(function($query) {
//            $query->field('create_time,count(id) as count')
//                ->where("to_days(now()) - to_days(date_format(from_UNIXTIME(`create_time`),'%Y%m%d')) <= 30")
//                ->group("FROM_UNIXTIME(create_time,'%Y%m%d')");
//        });
//        $dataStr = "";
//        foreach ($activeUser as $key=>$value) {
//            $dataStr .= "[".($value['create_time'] * 1000).",".$value['count']."]";
//            if($key < count($activeUser)-1){
//                $dataStr .= ",";
//            }
//        }
//        $this->assign('entyUserStr',$dataStr);
//
//        // 进入事件的用户
//        $entyUser = WechatLog::all();
//        $this->assign('entyUser',$entyUser);
//
//        // 用户活跃度排行榜
//        $userList = Db::table('pb_log')->alias('log')
//            ->field('log.userid,count(log.userid) as count,log.create_time,user.name')
//            ->join('wechat_user user', 'log.userid=user.userid')
//            ->group('log.userid')->order('count desc')->limit(8)
//            ->select();
//        $this->assign('userList', $userList);
//
//        // 部门活跃度排行榜
//        $departmentList = Db::table('pb_log')->alias('log')
//            ->field('log.department_id,count(log.department_id) as count,log.create_time,department.name')
//            ->join('wechat_department department', 'log.department_id=department.id')
//            ->group('log.department_id')->order('count desc')->limit(8)
//            ->select();
//        $this->assign('departmentList', $departmentList);
//
//        $list = array();
//        $this->assign('list',$list);
        return $this->fetch();
    }

    public function test() {
        $Config = new Config();
        $config = $Config->lists();
        //$config = Config::where('status',1)->column('type', 'name', 'value');
        $config = $Config->lists();
        //$config = Db::table('pb_config')->where('status',1)->column('type', 'name', 'value');

        dump($config);
    }

    public function search() {
        return $this->fetch();
    }

    /**
     * 修改密码
     */
    public function editpassword(){
        if(IS_POST){
            $id = input('id');
            $password = input('password');
            $repassword = input('repassword');
            if(!empty($password)){
                if($password != $repassword){
                    return $this->error('两次输入的密码不一致!');
                }else{
                    $data['password'] = think_ucenter_md5($repassword, config('uc_auth_key')); //转码存入数据库
                }
            }else{
                return $this->error("密码不能为空!");
            }
            $result = UcenterMember::where('id',$id)->update($data);
            if($result){
                return $this->success("修改成功",Url('Index/index'));
            }else{
                return $this->error("修改失败");
            }
        }else{
            //获取用户的信息
            $user = session('user_auth');
            $this->assign('user',$user);
            
            return $this->fetch();
        }
    }
}