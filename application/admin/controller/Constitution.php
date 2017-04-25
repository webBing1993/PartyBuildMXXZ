<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/8/16
 * Time: 14:37
 */

namespace app\admin\controller;
use app\admin\model\Question;
use app\admin\model\Option as OptionModel;

class Constitution extends Admin {
    /*
     * 题目列表
     */
    public function question(){
        $data=array();
        $question=$this->lists('Question',$data,'id');
        $this->assign('question',$question);
        return $this->fetch();
    }
    /*
     * 题目添加
     */
    public function plus(){
        if(IS_POST){
            $post=input('post.');
            //根据传递的值获得选项的信息
            $right=$post['answer'];
            if($post['type']==1){//获得多选的选项信息
                $right=implode($right,":");
            }
            if(empty($post['id'])){
                return $this->error("请输入题目");
            }else{
                //添加题目
                $Question=new Question();
                $Question->title="{$post['id']}";
                $Question->type=$post['type'];
                $Question->value=$right;
                if($Question->save()){
                    //获取添加的题目id
                    $question_id=$Question->id;
                    $Option=new OptionModel();
                    $list=[
                        ['question_id'=>$question_id,'content'=>"{$post['opt_A']}",'style'=>"A"],
                        ['question_id'=>$question_id,'content'=>"{$post['opt_B']}",'style'=>"B"],
                        ['question_id'=>$question_id,'content'=>"{$post['opt_C']}",'style'=>"C"],
                        ['question_id'=>$question_id,'content'=>"{$post['opt_D']}",'style'=>"D"]
                    ];
                    $result=$Option->saveAll($list); //根据获取的题目id,添加选项表
                    if($result){
                        return $this->success("新增成功",Url("Constitution/question"));
                    }else{
                        return $this->error("新增选项失败");
                    }
                }else{
                    return $this->error("新增题目失败");
                }
            }
        }else{
            return $this->fetch();
        }
    }
    /*
     * 题目删除
     */
    public function del(){
        $question_id=input('param.id/d');
        //删除指定题目,由于添加外键关联,选项表中对应的选项自动删除
        $result=Question::destroy($question_id);
        if($result){
            OptionModel::destroy(['question_id'=>$question_id]);
            return $this->success('删除成功',Url('Constitution/question'));
        }else{
            return $this->error('删除失败');
        }
    }
    /*
     * 题目修改
     */
    public function update(){
        if(IS_POST){
            $data=input('post.');
            $Question=new Question();
            $right=$data['answer'];
            if($data['type']==1){//获得多选的选项信息
                $right=implode($right,":");
            }
            //更新题目
            $result=$Question->update(array('title'=>$data['id'],'type'=>$data['type'],'value'=>$right),array('id'=>$data['question_id']));
            if($result){
                //更新选项
                $Option1=OptionModel::get($data['option_A']);
                $Option1->content=$data['opt_A'];
                $Option1->save();
                $Option2=OptionModel::get($data['option_B']);
                $Option2->content=$data['opt_B'];
                $Option2->save();
                $Option3=OptionModel::get($data['option_C']);
                $Option3->content=$data['opt_C'];
                $Option3->save();
                $Option4=OptionModel::get($data['option_D']);
                $Option4->content=$data['opt_D'];
                $Option4->save();
                return $this->success('更新成功',url('Constitution/question'));
            }else{
                return $this->error('更新失败');
            }
        }else{
            //获得id并强制转换为整型
            $question_id=input('param.id/d');
            //获取题目记录
            $question=Question::get($question_id);
            //判断该记录是否存在
            if(false===$question){
                return $this->error('系统错误,不存在该条记录');
            }
            $Option=new OptionModel();
            //获取选项信息
            $option=$Option->where('question_id='.$question_id)->select();
            $Right=Question::get($question_id);
            $rights=$Right->value;
            //获取正确答案信息
            $arr=explode(":",$rights);
            foreach($arr as $value){
                $right[]=$value;
            }
            $this->assign('right',$right);
            $this->assign('option',$option);
            $this->assign('question',$question);
            return $this->fetch();
        }
    }
}