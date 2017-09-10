<?php
namespace data\service;
use \think\Session as Session;
class BaseService
{
    protected $uid;
    protected $instance_id;  //店铺id
    protected $is_admin;
    protected $module_id_array;
    protected $instance_name;
    public function __construct(){
        $this->init();
    }
    
    /**
     * 初始化数据
     */
    private function init(){
        $this->uid = Session::get('uid');
        $this->instance_id = 0;
        $this->is_admin = Session::get('is_admin');
        $this->module_id_array = Session::get('module_id_array');
        $this->instance_name = Session::get('instance_name');
        $this->is_member = Session::get('is_member');
        $this->is_system = Session::get('is_system');
    }
    /**
     * 把返回的数据集转换成Tree
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    function listToTree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
          for($k = 0;$k<count($list);$k++)
          {
              $list[$k][$child] = array();
          }
        // 创建Tree  
        for($i = count($list)-1;$i>=0;$i--)
        {
        for($j = 0;$j<count($list);$j++)
        {
            if($list[$j][$pk] == $list[$i][$pid])
                {
                    if(empty($list[$j][$child]))
                    {
                        $list[$j][$child][0] = $list[$i];
                    }else{
                        $list[$j][$child] = array_push($list[$j][$child], $list[$i]);
                    }
                    
               
            }
        }
        
        }
        return $list;
    }
    
}