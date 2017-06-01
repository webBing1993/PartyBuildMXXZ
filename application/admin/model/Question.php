<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/10
 * Time: 14:27
 */
namespace app\admin\model;
use think\Model;
class Question extends Model{
    protected $autoWriteTimestamp=true;
    public function Option(){

        //获取题目编号
        $questionId=$this->getData('id');
        $Option=new Option();
        //根据题目编号获取所有选项信息
        $option=$Option->where('question_id='.$questionId)->select();
        $arr=array();
        foreach($option as $value){
            $arr[]=$value->style.'.&nbsp;'.$value->content;
        }
        return $arr;
    }
    public function Right(){
        //获取正确答案
        $lists=array(
            1=>"A",
            2=>"B",
            3=>"C",
            4=>"D"
        );
        $type = $this->getData('type');
        if ($type == 1){
            // 多选
            $rights = json_decode($this->getData('value'));
            foreach($rights as $value){
                $right[]=$lists[$value];
            }
            return implode('&nbsp;,&nbsp;',$right);
        }else{
            // 单选
            $rights = json_decode($this->getData('value'));
            return $lists[$rights];
        }
    }
}