<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Member;
use app\user\controller\Index;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpMailer;
use think\Cache;
use think\Config;
use think\Controller;
use think\Input;
use think\Url;


/**
 * 后台公共控制器
 */
class Base extends Controller {
    /**
     * 后台用户登录
     */
    public function login($username = null, $password = null, $verify = null) {
        if(IS_POST) {
            /* 检测验证码 TODO: */
//            if(!check_verify($verify)){
//                $this->error('验证码输入错误！');
//            }

            /* 调用UC登录接口登录 */
            $UserApi = new Index();
            $uid = $UserApi->login($username, $password);
            if(0 < $uid){ //UC登录成功
                /* 登录用户 */
                $Member = new Member();
                if($Member->login($uid)){ //登录用户
                    //TODO:跳转到登录前页面
                    return $this->success('登录成功！', Url('Index/index'));
                } else {
                    return $this->error($Member->getError());
                }
            } else { //登录失败
                switch($uid) {
                    case -1 : $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -2 : $error = '密码错误！'; break;
                    default : $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }
                return $this->error($error);
            }
        } else {
            if(is_login()){
                $this->redirect('Index/index');
            }else{
                /* 读取数据库中的配置 */
                $config	= Cache::get('db_config_data');
                if(!$config) {
                    $config	= \app\admin\model\Config::all();
                    Cache::set('db_config_data',$config);
                }
                Config::set($config); //添加配置

                return $this->fetch();
            }
        }
    }

    /* 退出登录 */
    public function logout(){
        if(is_login()){
            $member = new Member();
            $member->logout();
            session('[destroy]');
            return $this->success('退出成功！', Url::build('login'));
        } else {
            $this->redirect('login');
        }
    }

    public function forgot_password($email=null)
    {
        if (IS_POST) {
            $User =\app\user\model\UcenterMember::get(['email'=>$email]);

            if($User){
                $uid=$User->id;
                $mail = new Message;
                $mailer = new SmtpMailer(
                    [
                        'host' => 'smtp.163.com',    //SMTP服务器
                        'port' => '25',
                        'username' => 'z13868295088', //发件人邮箱
                        'password' => 'zhangyufan1',//发件人邮箱密码
                        'timeout'  => '10',
                    ]
                );
                $str = null;
                $num = 10;// 字符串长度
                $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";//如果不需要小写字母，可以把小写字母都删除
                $max = strlen($strPol)-1;
                for($i=0;$i<$num;$i++){
                    $str.=$strPol[rand(18,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
                }
                $time=time();
                // 生成URL
                $findpwd = $_SERVER ['HTTP_HOST'] . Url::build('reforgot_password') .'?number='.$str; // code是用来检验time是否有修改过
                $mail -> setFrom('z13868295088 <z13868295088@163.com>')
                      -> addTo($email)
                      -> setSubject('Order Confirmation')
                      -> setHtmlBody('尊敬的客户：<br />&nbsp;&nbsp;&nbsp;&nbsp;你使用了本站提供的密码找回功能，如果你确认此密码找回功能是你启用的，请点击下面的链接
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;找回密码！'.$findpwd);
                $mailer -> send($mail);
                //将str,time输入数据库中
                $member=Member::get(['id'=>$uid]);
                $member->check_number = $str;
                $member->active_time   = $time;
                $member->save();
                return $this->success('发送成功',Url::build('login'));
            } else {
                return $this->error('邮箱不存在  ');
            }
        } else {
            return $this->fetch();
        }
    }

    /**
     * @param null $password   传入密码
     * @param null $password2  确认密码
     * @return mixed
     */
    public function reforgot_password($password=null,$password2=null) {
        //通过number获取到表中数据
        $number=input('number');
        $user = Member::get(['check_number' => $number]);
        $id=$user->id;
        //验证当前时间有无超出验证时间，如果超出五分钟重新验证
        $time1=$user->active_time; //注册时间
        $time2=time();             //当前时间
        $time=ceil(($time2-$time1)/3600);//计算间隔时间
        if($time<5) {
            $User = \app\user\model\UcenterMember::get($id);
            if ($User) {
                if (IS_POST) {
                    if ($password == $password2) {
                        /* 调用UC修改密码接口修改 */
                        $Userpsd = new Index();
                        $result = $Userpsd->reforgot_password($id, $password);
                        if ($result == 1) {  //修改密码成功
                            return $this->success('修改成功', Url::build('login'));
                        } else {        //修改不成功返回原因
                            return $this->error('格式出现问题');
                        }
                    } else {
                        return $this->error('密码不一致');
                    }
                } else {
                    return $this->fetch();
                }
            }
        }else{
            $wrong='超出验证时间，请重新登录';
            return $this->error($wrong, Url::build('login'),' ',5);
        }
    }


    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

    public function register($name=null,$email=null,$password=null,$password2=null,$mobile='') {
        if(IS_POST) {
            if (strlen($password) < 6 || strlen($password) > 50) {
                return $this->error('密码至少6位数');
            }else {
                if ($password == $password2) {
                    /* 调用UC注册接口注册 */
                    $user = new Index();
                    $member = $user->register($name, $email, $password, $mobile);
                    if (0 < $member) { //UC注册成功
                        return $this->success('登录成功！', Url::build('login'));
                    } else { //注册失败输出错误原因
                        return $this->error($member);
                    }
                } else {
                    return $this->error('密码不一致');
                }
            }
        }
        return $this->fetch();
    }

    /**
     * 微信号认证接口
     */
    public function oauth() {
        $wechat = new TPWechat(C('gallery'));
        $wechat->valid();
//        $wechat->valid();
//        $wechat->getRev();

//        $data = $weObj->getRevData();
//        $type = $weObj->getRevType();
//        $ToUserName = $weObj->getRevTo();
//        $FromUserName = $weObj->getRevFrom();
//
//        if ($type == TPWechat::MSGTYPE_EVENT) {
//            $event = $weObj->getRevEvent();
//            if ($event['event'] == TPWechat::EVENT_ENTER_AGENT ) {
//                //$weObj->text("你好2！来自星星的：".$FromUserName."\n你发送的".$type."类型信息：\n原始信息如下：\n".var_export($data,true))->reply();
//                //简单写入数据
//                //$wechatUser = M('WechatUser')->where('userid='.$FromUserName)->find();
//                $userData = array(
//                    'userid' => $FromUserName,
//                    'msgtype' => '',
//                    'event' => 'EVENT_ENTER_AGENT',
//                    'agentid' => '',
//                    'create_time' => time()
//                );
//
//                M('WechatLog')->add($userData);
//            }
//
//        }
    }

    /**
     * 404错误页
     */
    public function error_404() {
        return $this->fetch('404');
    }

    /**
     * 500错误页
     */
    public function error_500() {
        return $this->fetch('500');
    }
}
