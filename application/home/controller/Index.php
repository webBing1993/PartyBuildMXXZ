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
use app\home\model\Learn as LearnModel;


/**
 * 党建主页
 */
class Index extends Base {
    public function index(){
        $len = array('news' => 0,'learn' => 0);
        $list2 = $this ->getDataList($len);
        $this ->assign('list2',$list2['data']);
        return $this->fetch();
    }

    /**
     * 获取新闻发布 两学一做数据
     * @param $len
     */
    public function getDataList($len){
        $count1 = $len['news'];
        $count2 = $len['learn'];
        $surplus = 0;
        $all_list = array();
        $news = new NewsModel();
        $learn = new LearnModel();
        $map = array('status' => ['egt',0],'recommend' => 1);
        //开始先取新闻三条
        $list1 = $news ->where($map) ->order('create_time desc') ->limit($count1,3)->select();
        //压入数据
        $all_list = $this ->changTpye($all_list,$list1,1);
        //记录数量
        $count = count($all_list);
        $surplus = 6 - $count;
        $count1 += $count;
        //再取两学一做数据
        $list2 = $learn ->where($map) ->order('create_time desc') ->limit($count2,$surplus)->select();
        $all_list = $this ->changTpye($all_list,$list2,2);
        //最后再补一遍
        if(count($all_list) < 6){
            $count = count($all_list);
            $surplus = 6 - $count;
            $list1 = $news ->where($map) ->order('create_time desc') ->limit($count1,$surplus)->select();
            $all_list = $this ->changTpye($all_list,$list1,1);
        }
        if (count($all_list) != 0){
            return ['code' => 1,'msg' => '获取成功','data' => $all_list];
        }else{
            return ['code' => 0,'msg' => '获取失败','data' => $all_list];
        }
    }

    /**
     * 进行数据区分
     * @param $list
     * @param $type
     */
    private function changTpye($all,$list,$type){
        foreach ($list as $k => $v){
            $res = array(
                'class' => $type,
                'id' => $v['id'],
                'title' => $v['title'],
                'publisher' => $v['publisher'],
                'create_time' => $v['create_time'],
                'front_cover' => $v['front_cover'],
                'type' => $v['type']
            );
            array_push($all,$res);
        }
        return $all;
    }
    public function moreDataList(){
        $len = input('get.');
        $list = $this ->getDataList($len);
        foreach ($list['data'] as $k => $v){
            $list['data'][$k]['time'] = date('Y-m-d',$v['create_time']);
        }
        return $list;
    }
}