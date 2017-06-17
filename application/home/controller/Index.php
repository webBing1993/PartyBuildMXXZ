<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 13:47
 */

namespace app\home\controller;
use app\admin\model\Picture;
use think\Controller;
use app\home\model\News as NewsModel;
use app\home\model\Learn as LearnModel;
use app\home\model\Notice as NoticeModel;


/**
 * 党建主页
 */
class Index extends Base {
    /**
     * 首页
     * @return mixed
     */
    public function index(){
        $this ->anonymous();
        $uid = session('userId');
        $len = array('news' => 1,'learn' => 0,'notice' => 0);
        $list2 = $this ->getDataList($len);
        $this ->assign('user',$uid);
        $this ->assign('list2',$list2['data']);
        return $this->fetch();
    }

    /**
     * 获取数据列表 红色足记 两学一做 鸡毛传贴
     * @param $len
     */
    public function getDataList($len)
    {
        $count1 = $len['news']; //从第几条开始取数据
        $count2 = $len['learn'];
        $count3 = $len['notice'];
        $news = new NewsModel();
        $learn = new LearnModel();
        $notice = new NoticeModel();
        $news_check = false; //新闻数据状态 true为取空
        $learn_check = false;
        $notice_check = false;
        $all_list = array();
        //获取数据  取满6条 或者取不出数据退出循环
        while(true)
        {
            if(!$news_check &&
                count($all_list) < 6)
            {
                $res = $news ->getDataList($count1); //获取一条数据
                if(empty($res))
                {
                    $news_check = true;
                }else {
                    $count1 ++;
                    $all_list = $this ->changeTpye($all_list,$res,1); //给每条数据增加类别判断
                }
            }
            if(!$learn_check &&
                count($all_list) < 6)
            {
                $res = $learn ->getDataList($count2);
                if(empty($res))
                {
                    $learn_check = true;
                }else {
                    $count2 ++;
                    $all_list = $this ->changeTpye($all_list,$res,2);
                }
            }
            if(!$notice_check &&
                count($all_list) < 6)
            {
                $res = $notice ->getDataList($count3);
                if(empty($res))
                {
                    $notice_check = true;
                }else {
                    $count3 ++;
                    $all_list = $this ->changeTpye($all_list,$res,3);
                }
            }
            if(count($all_list) >= 6 ||
                ($news_check && $learn_check && $notice_check))
            {
                break;
            }
        }
        if (count($all_list) != 0)
        {
            return ['code' => 1,'msg' => '获取成功','data' => $all_list];
        }else{
            return ['code' => 0,'msg' => '获取失败','data' => $all_list];
        }
    }

    /**
     * 进行数据区分
     * @param $list
     * @param $type 1新闻  2两学一做 3通知
     */
    private function changeTpye($all,$list,$type){
        $list['class'] = $type;
        array_push($all,$list);
        return $all;
    }
    /**
     * 首页加载更多新闻列表
     * @return array
     */
    public function moreDataList(){
        $len = input('get.');
        $list = $this ->getDataList($len);
        //转化图片路径 时间戳
        foreach ($list['data'] as $k => $v)
        {
            $img_path = Picture::get($list['data'][$k]['front_cover']);
            $list['data'][$k]['time'] = date('Y-m-d',$v['create_time']);
            $list['data'][$k]['path'] = $img_path['path'];
        }
        return $list;
    }
}