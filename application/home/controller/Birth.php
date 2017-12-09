<?php

namespace app\home\controller;
/**
 * Class Birth
 * @package  入党那一天
 */
use app\home\model\Birth as BirthModel;
use com\wechat\TPWechat;
use think\Config;
use think\Controller;

class Birth extends Controller {
    /**
     * 获取公众号签名
     */
    public function jssdk(){
        $Wechat = new TPWechat(Config::get('party'));
        session('jsapiticket', $Wechat->getJsTicket()); // 官方7200,设置7000防止误差
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $jsSign = $Wechat->getJsSign($url);
        $this->assign("jsSign", $jsSign);
    }
    /**
     * 主页
     */
    public function index(){
        $this->jssdk();
        //分享图片及链接及描述
        $list = array();
        $list['title'] = '我为党庆生！';
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].'/home/images/birth/index_bottom.jpg';
        $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $list['desc'] = "您是第几位祝福中国共产党96岁生日的小镇创客。转发微信参与祝福传递。";
        $this->assign('info',$list);
        return $this->fetch();
    }
    /*
     * 提交
     */
    public function push(){
        $name = input('post.name');
        $data = input('post.data');  // 2017-07-07
        if (strtotime($data) > time()){
            return 0;
        }
        $res = BirthModel::where('name',$name)->find();
        $Birth = new BirthModel();
        if ($res){
            // 有数据  修改
            $Birth->save(['name' => $name,'content' => strtotime($data),'create_time' => time()],['name' => $name]);
            return $res['id'];
        }else{
            // 无数据  添加
            $num = BirthModel::count();  // 添加 人数
            $result = $Birth -> save(['name' => $name,'num' => $num+500+1,'content' => strtotime($data),'create_time' => time()]);
            if ($result){
                return $result;
            }
        }
    }
    /**
     *  详细页
     */
    public function detail(){
        $id = input('get.id');
        $res = BirthModel::where('id',$id)->find();
        $str1 = date('Y年m月d日',$res['content']);
        $str2 = $this ->datediffage($res['content'],time());
        $this->assign(['name' => $res['name'],'num' => $res['num'],'str1' => $str1,'str2' => $str2]);
		$this->jssdk();
		//分享图片及链接及描述
		$list = array();
		$list['title'] = '我为党庆生！';
		$list['share_image'] = "http://".$_SERVER['SERVER_NAME'].'/home/images/birth/index_bottom.jpg';
		$list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
		$list['desc'] = $res['name'];
		$this->assign('info',$list);
        return $this->fetch();
    }
    function datediffage($before, $after) {
        if ($before>$after) {
            $b = getdate($after);
            $a = getdate($before);
        }else {
            $b = getdate($before);
            $a = getdate($after);
        }

        $n = array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);
        $y=$m=$d=0;

        if ($a['mday']>=$b['mday']) { //天相减为正

            if ($a['mon']>=$b['mon']) {//月相减为正
                $y=$a['year']-$b['year'];$m=$a['mon']-$b['mon'];
            }else { //月相减为负，借年
                $y=$a['year']-$b['year']-1;$m=$a['mon']-$b['mon']+12;
            }

            $d=$a['mday']-$b['mday'];

        }else {  //天相减为负，借月
            if ($a['mon']==1) { //1月，借年
                $y=$a['year']-$b['year']-1;$m=$a['mon']-$b['mon']+12;$d=$a['mday']-$b['mday']+$n[12];
            }else {

                if ($a['mon']==3) { //3月，判断闰年取得2月天数
                    $d=$a['mday']-$b['mday']+($a['year']%4==0?29:28);
                } else {
                    $d=$a['mday']-$b['mday']+$n[$a['mon']-1];
                }


                if ($a['mon']>=$b['mon']+1) { //借月后，月相减为正
                    $y=$a['year']-$b['year'];$m=$a['mon']-$b['mon']-1;
                }else { //借月后，月相减为负，借年
                    $y=$a['year']-$b['year']-1;$m=$a['mon']-$b['mon']+12-1;
                }


            }


        }

        return ($y==0?'':$y.'年').($m==0?'':$m.'个月').($d==0?'':$d.'天');

    }

    /**
     *  关于小镇
     */
    public function smalltown(){
        return $this->fetch();
    }


}