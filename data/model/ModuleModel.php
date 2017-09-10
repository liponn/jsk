<?php

namespace data\model;
use think\Db;
use data\model\BaseModel as BaseModel;
/**
 * 系统模块表
 * @author Administrator
 *
 */
class ModuleModel extends BaseModel
{
    protected  $table = 'sys_module';
    protected $rule = [
        'module_id'  =>  '',
    ];
    protected $msg = [
        'module_id'  =>  '',
    ];
    
    /**
     * 通过模块方法查询权限id
     * @param unknown $controller
     * @param unknown $action
     * @return unknown
     */
    public function getModuleIdByModule($controller, $action)
    {
        $condition = array(
            'controller' => $controller,
            'method' => $action,
            'module' => \think\Request::instance()->module()
        );
        $count = $this->where($condition)->count('module_id');
        if($count > 1)
        {
            $condition = array(
                'module' => \think\Request::instance()->module(),
                'controller' => $controller,
                'method' => $action,
                'pid' => array('<>', 0)
            );
        }
        $res = $this->where($condition)->find();
        return $res;
    }
    /**
     * 查询权限节点的根节点
     * @param unknown $module_id
     */
    public function getModuleRoot($module_id)
    {
        $root_id = $module_id;
        $pid = $this->getInfo(['module_id' => $module_id], 'pid');
        $pid = $pid['pid'];
        if(empty($pid))
        {
            return 0;
        }
        while($pid != 0){
            $module= $this->getInfo(['module_id' => $pid], 'pid, module_id');
            $root_id = $module['module_id'];
            $pid = $module['pid'];
    
        }
        return $root_id;
    }
    
    /**
     * 通过权限id组查询权限列表
     * @param unknown $list_id_arr
     */
    public function getAuthList($pid)
    {
        $contdition = array(
            'pid' => $pid,
            'is_menu' => 1,
            'module'  => \think\Request::instance()->module()
        );
        $list = $this->where($contdition)->order("sort")->column('module_id,module_name,controller,method,pid,url,is_menu,is_dev,icon_class,is_control_auth');
        return $list;
    }
    /**
     * 查询当前模块的上级ID
     * @param unknown $module_id
     */
    public function getModulePid($module_id)
    {
        $pid = $this->get($module_id);
        return $pid['pid'];
    }
}