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
        $question=$this->lists('Question',array(),'id desc');
        $this->assign('question',$question);
        return $this->fetch();
    }
    /*
     * 题目添加
     */
    public function plus(){
        if(IS_POST){
            $post=input('post.');
            if (empty($post['title'])){
                return $this->error('请输入题目标题');
            }
            if ((!empty($post['opt_D'])) && (empty($post['opt_C']) || empty($post['opt_B']) || empty($post['opt_A']))){
                return  $this->error('请顺序添加选项');
            }
            if ((!empty($post['opt_C'])) && (empty($post['opt_B']) || empty($post['opt_A']))){
                return  $this->error('请顺序添加选项');
            }
            if ((!empty($post['opt_B'])) && empty($post['opt_A'])){
                return  $this->error('请顺序添加选项');
            }
            if (empty($post['opt_A'])){
                return  $this->error('请顺序添加选项');
            }
            if ($post['type'] == 1){
                if (empty($post['answer'])){
                    return  $this->error('请添加多选的正确答案');
                }else{
                    if (count($post['answer']) < 2){
                        return  $this->error('正确答案不符合题目类型');
                    }else{
                        if (empty($post['opt_D'])){
                            foreach($post['answer'] as $val){
                                if ($val >= 4){
                                    return  $this->error('正确答案出现未知选项');
                                }
                            }
                        }
                        if (empty($post['opt_C'])){
                            foreach($post['answer'] as $val){
                                if ($val >= 3){
                                    return  $this->error('正确答案出现未知选项');
                                }
                            }
                        }
                        if (empty($post['opt_B'])){
                            foreach($post['answer'] as $val){
                                if ($val >= 2){
                                    return  $this->error('正确答案出现未知选项');
                                }
                            }
                        }
                    }
                }
            }else{
                if (empty($post['opt_D'])){
                    if ($post['answer'] >= 4){
                        return  $this->error('正确答案出现未知选项');
                    }
                }
                if (empty($post['opt_C'])){
                    if ($post['answer'] >= 3){
                        return  $this->error('正确答案出现未知选项');
                    }
                }
                if (empty($post['opt_B'])){
                    if ($post['answer'] >= 2){
                        return  $this->error('正确答案出现未知选项');
                    }
                }
            }
            // 添加题目 入表
            $data = array(
                'title' => $post['title'],
                'type' => $post['type'],
                'value' => json_encode($post['answer'])
            );
            $Question=new Question();
            $res = $Question->save($data);
            if ($res){
                $Option=new OptionModel();
                if (!empty($post['opt_D'])){
                    $list = [
                        ['question_id'=>$res,'content'=>$post['opt_A'],'style'=>"A"],
                        ['question_id'=>$res,'content'=>$post['opt_B'],'style'=>"B"],
                        ['question_id'=>$res,'content'=>$post['opt_C'],'style'=>"C"],
                        ['question_id'=>$res,'content'=>$post['opt_D'],'style'=>"D"]
                    ];
                }else if (!empty($post['opt_C'])){
                    $list = [
                        ['question_id'=>$res,'content'=>$post['opt_A'],'style'=>"A"],
                        ['question_id'=>$res,'content'=>$post['opt_B'],'style'=>"B"],
                        ['question_id'=>$res,'content'=>$post['opt_C'],'style'=>"C"],
                    ];
                }else if(!empty($post['opt_B'])){
                    $list = [
                        ['question_id'=>$res,'content'=>$post['opt_A'],'style'=>"A"],
                        ['question_id'=>$res,'content'=>$post['opt_B'],'style'=>"B"],
                    ];
                }else{
                    $list = ['question_id'=>$res,'content'=>$post['opt_A'],'style'=>"A"];
                }
                $result = $Option->saveAll($list);
                if ($result){
                    return $this->success("新增成功",Url("Constitution/question"));
                }else{
                    return $this->error($Option->getError());
                }
            }else{
                return $this->error($Question->getError());
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
            $post=input('post.');
            if (empty($post['title'])){
                return $this->error('请输入题目标题');
            }
            if ((!empty($post['opt_D'])) && (empty($post['opt_C']) || empty($post['opt_B']) || empty($post['opt_A']))){
                return  $this->error('请顺序添加选项');
            }
            if ((!empty($post['opt_C'])) && (empty($post['opt_B']) || empty($post['opt_A']))){
                return  $this->error('请顺序添加选项');
            }
            if ((!empty($post['opt_B'])) && empty($post['opt_A'])){
                return  $this->error('请顺序添加选项');
            }
            if (empty($post['opt_A'])){
                return  $this->error('请顺序添加选项');
            }
            if ($post['type'] == 1){
                if (empty($post['answer'])){
                    return  $this->error('请添加多选的正确答案');
                }else{
                    if (count($post['answer']) < 2){
                        return  $this->error('正确答案不符合题目类型');
                    }else{
                        if (empty($post['opt_D'])){
                            foreach($post['answer'] as $val){
                                if ($val >= 4){
                                    return  $this->error('正确答案出现未知选项');
                                }
                            }
                        }
                        if (empty($post['opt_C'])){
                            foreach($post['answer'] as $val){
                                if ($val >= 3){
                                    return  $this->error('正确答案出现未知选项');
                                }
                            }
                        }
                        if (empty($post['opt_B'])){
                            foreach($post['answer'] as $val){
                                if ($val >= 2){
                                    return  $this->error('正确答案出现未知选项');
                                }
                            }
                        }
                    }
                }

            }else{
                if (empty($post['opt_D'])){
                    if ($post['answer'] >= 4){
                        return  $this->error('正确答案出现未知选项');
                    }
                }
                if (empty($post['opt_C'])){
                    if ($post['answer'] >= 3){
                        return  $this->error('正确答案出现未知选项');
                    }
                }
                if (empty($post['opt_B'])){
                    if ($post['answer'] >= 2){
                        return  $this->error('正确答案出现未知选项');
                    }
                }
            }
            // 修改 题目 数据
            $data = array(
                'title' => $post['title'],
                'type' => $post['type'],
                'value' => json_encode($post['answer'])
            );
            $Question=new Question();
            $res = $Question->save($data,['id' => $post['question_id']]);
            if ($res){
                $Option=new OptionModel();
                if (!empty($post['opt_D'])){
                    $list = [
                        ['question_id'=>$post['question_id'],'content'=>$post['opt_A'],'style'=>"A"],
                        ['question_id'=>$post['question_id'],'content'=>$post['opt_B'],'style'=>"B"],
                        ['question_id'=>$post['question_id'],'content'=>$post['opt_C'],'style'=>"C"],
                        ['question_id'=>$post['question_id'],'content'=>$post['opt_D'],'style'=>"D"]
                    ];
                }else if (!empty($post['opt_C'])){
                    $list = [
                        ['question_id'=>$post['question_id'],'content'=>$post['opt_A'],'style'=>"A"],
                        ['question_id'=>$post['question_id'],'content'=>$post['opt_B'],'style'=>"B"],
                        ['question_id'=>$post['question_id'],'content'=>$post['opt_C'],'style'=>"C"],
                    ];
                }else if(!empty($post['opt_B'])){
                    $list = [
                        ['question_id'=>$post['question_id'],'content'=>$post['opt_A'],'style'=>"A"],
                        ['question_id'=>$post['question_id'],'content'=>$post['opt_B'],'style'=>"B"],
                    ];
                }else{
                    $list = ['question_id'=>$post['question_id'],'content'=>$post['opt_A'],'style'=>"A"];
                }
                if($Option->where('question_id',$post['question_id'])->delete()){
                    $result = $Option->saveAll($list);
                    if ($result){
                        return $this->success("修改成功",Url("Constitution/question"));
                    }else{
                        return $this->get_update_error_msg($Option->getError());
                    }
                }
            }else{
                return $this->get_update_error_msg($Question->getError());
            }
        }else{
            //获得id并强制转换为整型
            $question_id=input('param.id/d');
            //获取题目记录
            $question=Question::get($question_id);
            //判断该记录是否存在
            if(empty($question)){
                return $this->error('系统错误,不存在该条记录');
            }
            $Option=new OptionModel();
            //获取选项信息
            $option=$Option->where('question_id='.$question_id)->order('id asc')->select();
            $Right=Question::get($question_id);
            $rights=$Right['value'];
            if ($Right['type'] == 1){
                // 多选
                foreach(json_decode($rights) as $value){
                    $right[]=$value;
                }
            }else{
                // 单选
                $right = json_decode($rights);
            }
            $this->assign('right',$right);
            $this->assign('option',$option);
            $this->assign('num',count($option));
            $this->assign('question',$question);
            return $this->fetch();
        }
    }
}