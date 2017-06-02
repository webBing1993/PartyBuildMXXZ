<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/6/2
 * Time: 14:00
 */
namespace app\home\model;
use think\Model;
use app\home\model\Push;
use app\home\model\News;

class PushReview extends Model{
    public $insert = array(
        'review_time' => NOW_TIME,
    );
    public function get_info($id){
        //找出对应的新闻id
        $record =Push::where('id',$id) ->field(['focus_main','class']) ->find();
        if($record['class'] == 1){
            $info = News::where('id',$record['focus_main']) ->field(['front_cover','title']) ->find();
            return $info;
        }
    }
}