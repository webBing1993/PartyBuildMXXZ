<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/1
 * Time: 16:32
 */
namespace app\home\model;
use think\Model;

class News extends Model{
    //首页获取推荐的数据
    public function getDataList($length){
        $map = array(
            'status' => ['egt',0],
            'recommend' => 1
        );
        $order = 'create_time desc';
        $limit = "$length,1";
        $list = $this ->where($map) ->order($order) ->limit($limit) ->select();
        if(!empty($list))
        {
            return $list[0] ->data;
        }else{
            return $list;
        }
    }
}