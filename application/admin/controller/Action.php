<?php

namespace app\admin\controller;

use app\admin\model\ActionLog;

class Action extends Admin
{
    /**
     * 行为日志列表
     */
    public function actionLog(){
        //获取列表数据
        $map['status'] = array('gt', -1);
        $list = $this->lists('ActionLog', $map);
        int_to_string($list);
//        foreach ($list as $key=>$value){
//            $model_id               = get_document_field($value['model'],"name","id");
//            $list[$key]['model_id'] = $model_id ? $model_id : 0;
//        }
        $this->assign('_list', $list);

        return $this->fetch();
    }

    /**
     * 查看行为日志
     */
    public function edit($id = 0){
        empty($id) && $this->error('参数错误！');
        $info = ActionLog::get($id);
        $this->assign('info', $info);

        return $this->fetch();
    }

    /**
     * 删除日志
     * @param mixed $ids
     */
    public function remove($ids = 0){
        if(ActionLog::destroy($ids)) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 清空日志
     */
    public function clear(){
        if(ActionLog::where('1','=', 1)->delete()) {
            $this->success('日志清空成功！');
        } else {
            $this->error('日志清空失败！');
        }
    }

}
