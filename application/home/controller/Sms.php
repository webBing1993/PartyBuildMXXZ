<?php
/**
 * Created by PhpStorm.
 * User: stiff
 * Date: 24/11/17
 * Time: 上午10:31
 */

namespace app\home\controller;

use think\Controller;
use Yunpian\Sdk\YunpianClient;
use app\home\model\SmsCode;
use app\home\model\WechatUser;
use think\Cookie;
use think\Config;

class Sms extends Controller
{
    public function _initialize(){
        Cookie::init(Config::get('cookie'));
    }

    static $ACTIVETIME = 300;// 验证码有效时间(秒)
    static $INTERVAL = 90;// 验证码间隔时间(秒)

    /**
     * 云片短信验证码
     */
    public function register()
    {
        if (IS_POST) {
            $data = input('post.');
            $checkRes = $this->checkParam($data); //检查参数 防止恶意调用 重复注册
            if ($checkRes) {

               $this->error($checkRes);
            }
            $rand = rand(1000,9999); // 生成随机四位二维码

            $clnt = YunpianClient::create(Config('sms_apikey'));
            $param = [
                YunpianClient::MOBILE => $data['tel'],
                YunpianClient::TEXT => '【梦想红色驿站】感谢您的注册，验证码是'.$rand.'(5分钟内有效)，如非本人操作，请忽略本短信。',
            ];
            $res = $clnt->sms()->single_send($param); //发送验证码

            // code 0:发送成功
            $code = $res->code();
            if ($code === 0) {
                $this->createRecord($data,$rand); // 生成验证码记录

                return $this->success('验证码发送成功！');
            } else if ($code === 33) {

                $this->error('很抱歉，为防止恶意获取验证码您每30秒只能获取1条!');
            } else if ($code === 22) {

                $this->error('很抱歉，为防止恶意获取验证码您每小时最多能获取3条!');
            } else if ($code === 17) {

                $this->error('很抱歉，为防止恶意获取验证码您在24小时内最多能获取10条!');
            } else {

                $this->error('很抱歉，获取验证码失败！');
            }

        }

    }

    /**
     *注册验证
     */
    public function checkRegister()
    {
        if (IS_POST) {
            $data = input('post.');

            //检查参数 防止恶意调用 重复注册
            $checkRes = $this->checkCode($data);
            if ($checkRes) {

                $this->error($checkRes);
            }

            $uid = md5(uniqid());// 不重复随机id
            Cookie::set('dypb',['user' =>$uid]);
            session('userId','');
            $user = [
                'userid' => $uid,
                'name' => $data['tel'],
                'mobile' => $data['tel'],
                'department'=> 1,
                'gender' => 1,
                'tag' => 1,
                'status' => 1,
            ];
            WechatUser::create($user);

            // 写入注册成功后对应代码
            return $this->success('恭喜您，注册成功！');

        }
    }

    /*
     * 检查注册参数
     */
    private function checkCode($data)
    {
        // 手机号码格式验证
        if (!isset($data['tel']) || empty($data['tel'])) {

            return '手机号码不能为空！';
        } else if (!preg_match('/^1(3[0-9]|4[57]|5[0-35-9]|8[0-9]|7[01678])\\d{8}$/',$data['tel'])) {

            return '请输入正确手机号码！';
        }
        // 手机号码是否重复注册
        $userRes = WechatUser::where('mobile',$data['tel'])->find();
        if ($userRes) {

            return '您已注册，请勿重复注册！';
        }

        // 验证码格式验证
        if (!isset($data['code']) || empty($data['code'])) {

            return '短信验证码不能为空！';
        } else if (strlen($data['code']) != 4){

            return '短信验证码格式错误，应为四位数字！';
        }

        // 验证码有效认证
        $smsCodeModel = new SmsCode();
        $map = [
            'mobile' => $data['tel'],
            'active_time' => ['gt',time()],
            'status' => 0
        ];
        $res = $smsCodeModel->where($map)->order('create_time desc')->find();

        if (empty($res)) {

            return '很抱歉，验证码已过期或者不存在！';
        } else if ($res['code'] != $data['code']) {

            return '很抱歉，验证码错误！';
        } else if ($res['code'] == $data['code']) {
            // 已验证成功
            $smsCodeModel->save(['status'  => 1,],['id' => $res['id']]);

        }
    }

    /**
     * 检查参数
     */
    private function checkParam($data)
    {
        // 手机号码格式验证
        if (!isset($data['tel']) || empty($data['tel'])) {

            return '手机号码不能为空！';
        } else if (!preg_match('/^1(3[0-9]|4[57]|5[0-35-9]|8[0-9]|7[01678])\\d{8}$/',$data['tel'])) {

            return '请输入正确手机号码！';
        }
        // 手机号码是否重复注册
        $userRes = WechatUser::where('mobile',$data['tel'])->find();
        if ($userRes) {

            return '您已注册，请勿重复注册！';
        }
        // 验证码调用时间间隔
        $smsCodeModel = new SmsCode();
        $map = [
            'mobile' => $data['tel'],
            'status' => 0
        ];
        $res = $smsCodeModel->where($map)->order('create_time desc')->find();
        if (!empty($res)) {
            if ($res['create_time']+$this::$INTERVAL > time()) {

                return '请在'.($res['create_time']+$this::$INTERVAL-time()).'秒后再次请求！';
            }
        }

    }



    /*
     * 新建待验证记录
     */
    private function createRecord($data,$rand)
    {
        $smsCodeModel = new SmsCode();
        $smsCodeModel->data([
            'mobile' => $data['tel'],
            'code' => $rand,
            'active_time' => time() + $this::$ACTIVETIME,
        ])->save();
    }

}