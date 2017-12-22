<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 13:47
 */

namespace app\home\controller;
use app\admin\model\Picture;
use app\home\model\WechatUser;
use app\home\model\Wish;
use think\Controller;
use app\home\model\News as NewsModel;
use app\home\model\Learn as LearnModel;
use app\home\model\Notice as NoticeModel;
use app\home\model\Pioneer as PioneerModel;
use app\home\model\Wish as WishModel;


/**
 * 党建主页
 */
class Index extends Base {
    /**
     * 首页
     * @return mixed
     */
    public function indexbat(){
        $this ->anonymous();
        $uid = session('userId');
        $len = array('news' => 0,'learn' => 0,'notice' => 0,'pioneer' => 0,'wish' => 0);
        $list2 = $this ->getDataList($len);
        // 展示功能只对之图科技一体机开发
        if ($uid == '175781beb0ce2e96c082bb7327340ad0') {
            $test = 1;
        } else {
            $test = 0;
        }


        $map = array(
            'status' => ['egt',0],
            'recommend' => 1
        );
//        $where = ' status>=0 and recommend=1';
        $bannerList = wish::where($map)->field('id, front_cover, title, create_time,1 tab')->limit(3)->order('create_time desc')->select();

        /*$bannerList = \think\Db::field('id, front_cover, title, create_time, 1 tab')
            ->table('pb_wish')
            ->union("SELECT id, front_cover, title, create_time, 2 tab FROM pb_notice where ".$where." order by create_time desc limit 3")
            ->where($map);*/
//            ->select();


        $this->assign('bannerList',$bannerList);

        $this ->assign('user',$uid);
        $this ->assign('list2',$list2['data']);
        $this->assign('test',$test);
        return $this->fetch();
    }
    /**
     * 新首页
     * @return mixed
     */
    public function index(){
        $this ->anonymous();
        $uid = session('userId');
        $len = array('wish' => 0);
        $list = $this ->getDataList($len);
        // 启动仪式功能只对之图科技一体机开放
        $start = WechatUser::where('userid',$uid)->value('start');
//        var_dump($start);die;
        if($start){
            $show = 1;
        }else{
//            if($uid == 'visitor'){
            $show = 0;
        }
        $this ->assign('show',$show);
        $this ->assign('list',$list['data']);
        $this ->assign('user',$uid);

        return $this->fetch();
    }

    /**
     * 关闭启动仪式
     */
    public function shutdown()
    {
        $uid = session('userId');
        if(time() > strtotime("2017-12-26 09:00")){
            $res = WechatUser::where(array('userid'=>$uid))->update(['start' => 0]);
            if ($res){
                return $this->success('成功');
            }else{
                return $this->error('失败');
            }
        }else{
            return $this->error('没到时间');
        }
    }

    /*
     * 二次首页
     */
    public  function index1(){
        return $this->fetch();
    }

    /**
     * 获取数据列表 红色足记 两学一做 鸡毛传贴
     * @param $len
     */
    public function getDataList($len)
    {
//        $count1 = $len['news']; //从第几条开始取数据
//        $count2 = $len['learn'];
//        $count3 = $len['notice'];
//        $count4 = $len['pioneer'];
        $count5 = $len['wish'];
//        $news = new NewsModel();
//        $learn = new LearnModel();
//        $notice = new NoticeModel();
//        $pioneer = new PioneerModel();
        $wish = new WishModel();
//        $news_check = false; //新闻数据状态 true为取空
//        $learn_check = false;
//        $notice_check = false;
//        $pioneer_check = false;
        $wish_check = false;
        $all_list = array();
        //获取数据  取满4条 或者取不出数据退出循环
        while(true)
        {
            /*if(!$learn_check &&
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
            }*/
            /*if(!$notice_check &&
                count($all_list) < 4)
            {
                $res = $notice ->getDataList($count3);
                if(empty($res))
                {
                    $notice_check = true;
                }else {
                    $count3 ++;
                    $all_list = $this ->changeTpye($all_list,$res,3);
                }
            }*/
            
            /*if(!$pioneer_check &&
                count($all_list) < 6)
            {
                $res = $pioneer ->getDataList($count4);
                if(empty($res))
                {
                    $pioneer_check = true;
                }else {
                    $count4 ++;
                    $all_list = $this ->changeTpye($all_list,$res,4);
                }
            }*/

            if(!$wish_check &&
                count($all_list) < 4)
            {
                $res = $wish ->getDataList($count5);
                if(empty($res))
                {
                    $wish_check = true;
                }else {
                    $count5 ++;
                    $all_list = $this ->changeTpye($all_list,$res,5);
                }
            }
            if(count($all_list) >= 4 ||
                ($wish_check))
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