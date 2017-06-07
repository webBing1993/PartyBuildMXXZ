<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 13:47
 */

namespace app\home\controller;
use app\home\model\Message;
use app\home\model\WechatUser;
use com\wechat\TPQYWechat;
use think\Config;
use think\Controller;
use think\Log;
use app\home\model\News as NewsModel;

/**
 * 党建主页
 */
class Index extends Base {
    public function index(){
        $news = new NewsModel();
        $list2 = $news ->where(['status' => ['egt',0]]) ->order('create_time desc') ->limit(6)->select();
        $this ->assign('list2',$list2);

        $this ->assign('user',session('userId'));
        return $this->fetch();
    }
    
    /**
     * 首页获取不同模块数据
     */
    public function lists($length = 0){
        //不是get请求默认初始数据
        if (empty($length)) {
            $length = array();
            $length['learn'] = 0;
            $length['new'] = 0;
            $length['notice'] = 0;
        }else{
            $length = json_decode($length);
            $length['learn'] = $length[0];
            $length['new'] = $length[1];
            $length['notice'] = $length[2];
        }
        $data = array();
        //记录上一条缺几条的数据
        $more = 0;
        $new = new  News();
        $learn = new LearnModel();
        $notice = new NoticeModel();
        //党员动态
        $data['new'] = $new ->get_list($length['new'],2);
        if (count($data['new']) < 2) {
            $more += 2 - count($data['new']);
        }
        //支部动态
        $data['notice'] = $notice ->get_list($length['notice'],2+$more);
        if (count($data['notice']) < (2+$more)) {
            $more = $more + 2 - count($data['notice']);
        }else{
            $more = 0;
        }
        //两学一做
        $data['learn'] = $learn ->get_list($length['learn'],2+$more);
        if (count($data['learn']) < (2+$more)) {
            $more = $more + 2 - count($data['learn']);
        }else{
            $more = 0;
        }
        //数据不够6条,反过来再取一遍
        if( count($data['learn']) + count($data['notice']) + count($data['new']) < 6){
            //先去支部动态取
            if (count($data['notice']) >= 2) {
                $record = $notice ->get_list(count($data['notice'])+$length['notice'],$more);
                //数据如果不够再补
                if (count($record) < $more) {
                    $more += $more - count($record);
                    if($record){
                        $data['notice'] = array_merge($data['notice'],$record);
                    }
                    $record = $new ->get_list(count($data['new'])+$length['new'],$more);
                    if($record){
                        $data['new'] = array_merge($data['new'],$record);
                    }
                } else {
                    if($record){
                        $data['notice'] = array_merge($data['notice'],$record);
                    }
                }
            } else if (count($data['new']) == 2){
            //支部动态不够,再党员动态取
                $record = $new ->get_list(count($data['new'])+$length['new'],$more);
                if($record){
                    $data['new'] = array_merge($data['new'],$record);
                }
            }
        }
        //对应增加类型
        $data['new'] = $this->add_field($data['new'], 'new');
        $data['notice'] = $this->add_field($data['notice'], 'notice');
        $data['learn'] = $this->add_field($data['learn'], 'learn');
        return $data;
    }

    /**
     * 给列表数据增加类型
     */
    public function add_field($data,$field){
        foreach($data as $k =>$v){
            $v['genre'] = $field;
        }
        return $data;
    }

    public function more(){
        $length = input('get.length');
        $details = $this ->lists($length);
        foreach($details as $key => $value) {
            foreach ($value as $k => $v) {
                if ($v['front_cover']) {
                    $v['front_cover'] = get_cover($v['front_cover'], 'path');
                } else {
                    $v['front_cover'] = '/home/images/common/1.jpg';
                }
                $v['time'] = date('Y-m-d',$v['create_time']);
            }
        }
        return $details;
    }
}