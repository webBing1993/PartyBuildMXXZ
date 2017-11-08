<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/18
 * Time: 14:22
 */
namespace app\home\model;
use think\Model;

class Wish extends Model{
    /**
     * 首页获取推荐的数据
     * @param $length
     */
    public function getDataList($length){
        $map = array(
            'status' => ['egt',0],
            'type' => 1,
        );
        $order = 'create_time desc';
        $limit = "$length,1";
        $list = $this ->where($map) ->field('*,999999 as front_cover') ->order($order) ->limit($limit) ->select();
        if(!empty($list))
        {
            return $list[0] ->data;
        }else{
            return $list;
        }
    }
}