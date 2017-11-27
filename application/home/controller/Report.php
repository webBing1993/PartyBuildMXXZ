<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/26
 * Time: 10:50
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Like;
use app\home\model\Report as ReportModel;
use app\home\model\Comment;
use app\home\model\Picture;
use app\home\model\ReportVote;
use app\user\model\WechatUser;
/**
 * Class Report
 * @package 网上述职
 */
class Report extends Base{
    /**
     * 主页列表
     */
    public function index(){
        $this->anonymous();
        $userId = session('userId');
        $model = new ReportModel();
        $map = array(
            'status' => array('egt',0),
        );
        $list = $model->where($map)->order('id desc')->limit(10)->select();
        foreach($list as $value){
            $value['year'] = date('Y', $value['create_time']);
            $value['month'] = date('m', $value['create_time']);
            $value['day'] = date('d', $value['create_time']);
        }
        $this->assign('list',$list);
        return $this ->fetch();
    }

    /**
     * 详情页
     */
    public function detail(){
        return $this->fetch();
    }
}