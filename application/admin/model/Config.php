<?php
namespace app\admin\model;
use think\Db;
use think\Model;

class Config extends Base
{
    /**
     * 获取配置列表
     * @return array 配置数组
     */
    public function lists(){
        $data = $this::all(function($query){
            $query->where('status',1)->field('type,name,value');
        });
        $config = array();
        foreach ($data as $value) {
            $config[$value['name']] = $this->parse($value['type'], $value['value']);
        }
        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param $type
     * @param $value
     * @return array
     */
    private function parse($type, $value){
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if(strpos($value,':')){
                    $value  = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k]   = $v;
                    }
                } else {
                    $value =    $array;
                }
                break;
        }
        return $value;
    }

    /**
     * 检验标识是否重复
     * @param $name
     * @return bool
     */
    public function checkNote($name){
        $config = $this::get(function($query) use ($name){
            $query->where('name', $name);
        });
        if ($config) {
            return false;
        } else {
            return true;
        }
    }

}
