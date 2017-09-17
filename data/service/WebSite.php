<?php

namespace data\service;

use data\service\BaseService as BaseService;
use data\api\IWebSite as IWebSite;
use data\model\WebSiteModel as WebSiteModel;
use data\model\WebStyleModel as WebStyleModel;
use data\model\ModuleModel as ModuleModel;
use data\model\AuthGroupModel as AuthGroupModel;
use data\model\InstanceModel as InstanceModel;
use data\model\InstanceTypeModel;
use data\model\UserModel;
use data\model\AdminUserModel;
use think\Session;
use think\Log;

class WebSite extends BaseService implements IWebSite
{

    private $website;

    private $module;

    public function __construct()
    {
        parent::__construct();
        $this->website = new WebSiteModel();
        $this->module = new ModuleModel();
    }

    /**
     * 获取版本号
     */
    public function getVersion()
    {}

    /**
     * 获取网站信息
     *
     * @param string $field            
     */
    public function getWebSiteInfo()
    {
        if (cache("WEBSITEINFO")) {
            return cache("WEBSITEINFO");
        } else {
            //$info = $this->website->getInfo('');
            $info = Array(
                    'website_id' => 1,
                    'title' => '家顺康微信',
                    'logo' => 'public/static/images/adv_position/logo.png',
                    'web_desc' => '开源商城',
                    'key_words' => '',
                    'web_icp' => '1111111',
                    'style_id_pc' => 2,
                    'web_address' => '北京市',
                    'web_qrcode' => 'public/static/images/adv_position/qrcode.png',
                    'web_url' => 'http://baidu.com',
                    'web_email' => '331644741@qq.com',
                    'web_phone' => '400-886-7993',
                    'web_qq' => '331644741',
                    'web_weixin' => 'echototo666',
                    'web_status' => 1,
                    'third_count' => '',
                    'close_reason' => '对不起，维护中，大家敬请期待...',
                    'wap_status' => 1,
                    'style_id_admin' => 4,
                    'create_time' => 1477452112,
                    'modify_time' => 1498896658,
                    'url_type' => 0,
                );
            cache("WEBSITEINFO", $info);
        }
        
        return cache("WEBSITEINFO");
    }

    /**
     * (non-PHPdoc)
     * @see \data\api\IWebsite::updateWebSite()
     */
    function updateWebSite($title, $logo, $web_desc, $key_words, $web_icp, $web_style_pc, $web_style_admin, $visit_pattern, $web_qrcode, $web_url, $web_phone, $web_email, $web_qq, $web_weixin, $web_address, $web_status, $wap_status, $third_count, $close_reason)
    {
        $data = array(
            'title' => $title,
            'logo' => $logo,
            'web_desc' => $web_desc,
            'key_words' => $key_words,
            'web_icp' => $web_icp,
            'style_id_pc' => $web_style_pc,
            'style_id_admin' => $web_style_admin,
            'url_type' => $visit_pattern,
            'web_qrcode' => $web_qrcode,
            'web_url' => $web_url,
            'web_phone' => $web_phone,
            'web_email' => $web_email,
            'web_qq' => $web_qq,
            'web_weixin' => $web_weixin,
            'web_address' => $web_address,
            'web_status' => $web_status,
            'wap_status' => $wap_status,
            'third_count' => $third_count,
            'close_reason' => $close_reason,
            'modify_time' => time()
        );
        $this->website = new WebSiteModel();
        $res = $this->website->save($data, [
            "website_id" => 1
        ]);
        if ($res) {
            cache("WEBSITEINFO", null);
        }
        return $res;
    }

    /**
     * 添加系统模块
     *
     * @see \data\api\IWebsite::addSytemModule()
     */
    public function addSytemModule($module_name, $controller, $method, $pid, $url, $is_menu, $is_dev, $sort, $module_picture, $desc, $icon_class, $is_control_auth)
    {
        // 查询level
        if ($pid == 0) {
            $level = 1;
        } else {
            $level = $this->getSystemModuleInfo($pid, $field = 'level')['level'] + 1;
        }
        $data = array(
            'module_name' => $module_name,
            'module' => \think\Request::instance()->module(),
            'controller' => $controller,
            'method' => $method,
            'pid' => $pid,
            'level' => $level,
            'url' => $url,
            'is_menu' => $is_menu,
            "is_control_auth" => $is_control_auth,
            'is_dev' => $is_dev,
            'sort' => $sort,
            'module_picture' => $module_picture,
            'desc' => $desc,
            'create_time' => time(),
            'icon_class' => $icon_class
        );
        $mod = new ModuleModel();
        $res = $mod->save($data);
        $this->updateUserModule();
        return $res;
    }

    /**
     * 修改系统模块
     *
     * @see \data\api\IWebsite::updateSystemModule()
     */
    public function updateSystemModule($module_id, $module_name, $controller, $method, $pid, $url, $is_menu, $is_dev, $sort, $module_picture, $desc, $icon_class, $is_control_auth)
    {
        // 查询level
        if ($pid == 0) {
            $level = 1;
        } else {
            $level = $this->getSystemModuleInfo($pid, $field = 'level')['level'] + 1;
        }
        $data = array(
            'module_id' => $module_id,
            'module_name' => $module_name,
            'module' => \think\Request::instance()->module(),
            'controller' => $controller,
            'method' => $method,
            'pid' => $pid,
            'level' => $level,
            'url' => $url,
            'is_menu' => $is_menu,
            "is_control_auth" => $is_control_auth,
            'is_dev' => $is_dev,
            'sort' => $sort,
            'module_picture' => $module_picture,
            'desc' => $desc,
            'modify_time' => time(),
            'icon_class' => $icon_class
        );
        $mod = new ModuleModel();
        $res = $mod->allowField(true)->save($data, [
            'module_id' => $module_id
        ]);
        $this->updateUserModule();
        return $res;
    }

    /**
     * 删除系统模块
     *
     * @param unknown $module_id            
     */
    public function deleteSystemModule($module_id_array)
    {
        $sub_list = $this->getModuleListByParentId($module_id_array);
        if (! empty($sub_list)) {
            $res = SYSTEM_DELETE_FAIL;
        } else {
            $res = $this->module->destroy($module_id_array);
        }
        $this->updateUserModule();
        return $res;
    }
    /**
     * 清除菜单
     */
    private function updateUserModule(){
        $module = request()->module();
        Session::set($module.'module_list_0', '');
        $mod = new ModuleModel();
        $module_id_list = $mod->getQuery('', 'module_id', '');
        foreach ($module_id_list as $k => $v)
        {
            Session::set($module.'module_list_' . $v['module_id'], '');
        }
        
    }

    /**
     * 获取系统模块
     *
     * @param unknown $module_id            
     */
    public function getSystemModuleInfo($module_id, $field = '*')
    {
        $res = $this->module->getInfo(array(
            'module_id' => $module_id
        ), $field);
        return $res;
    }

    /**
     * 修改系统模块 单个字段
     *
     * @param unknown $module_id            
     * @param unknown $order            
     */
    public function ModifyModuleField($module_id, $field_name, $field_value)
    {
        $res = $this->module->ModifyTableField('module_id', $module_id, $field_name, $field_value);
        $this->updateUserModule();
        return $res;
    }

    /**
     * 获取系统模块列表
     *
     * @param unknown $where            
     * @param unknown $order            
     * @param unknown $page_size            
     * @param unknown $page_index            
     */
    public function getSystemModuleList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        // 针对开发者模式处理
        if (! config('app_debug')) {
            if (is_array($condition)) {
                $condition = array_merge($condition, [
                    'is_dev' => 0
                ]);
            } else {
                if (! empty($condition)) {
                    $condition = $condition . ' and is_dev=0 ';
                } else {
                    $condition = 'is_dev=0';
                }
            }
        }
        $res = $this->module->pageQuery($page_index, $page_size, $condition, $order, $field);
        return $res;
    }

    /**
     * 根据当前实例查询权限列表
     *
     * @param unknown $instanceid            
     */
    public function getInstanceModuleQuery()
    {
        // 单用户查询全部
        $condition_module = array(
            'module' => \think\Request::instance()->module(),
            'is_control_auth' => 1
        );
        $moduelList = $this->getSystemModuleList(1, 0, $condition_module);
        return $moduelList['data'];
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWebsite::addSystemInstance()
     */
    public function addSystemInstance($uid, $instance_name, $type)
    {
        $instance = new InstanceModel();
        $instance->startTrans();
        try {
            $instance_model = new InstanceModel();
            // 创建实例
            $data_instance = array(
                'instance_name' => $instance_name,
                'instance_typeid' => $type,
                'create_time' => time()
            );
            $instance_model->save($data_instance);
            $instance_id = $instance_model->instance_id;
            // 查询实例权限
            $instance_type_model = new InstanceTypeModel();
            $instance_type_info = $instance_type_model->get($type);
            // 创建管理员组
            $data_group = array(
                'instance_id' => $instance_id,
                'group_name' => '管理员组',
                'is_system' => 1,
                'module_id_array' => $instance_type_info['type_module_array'],
                'create_time' => time()
            );
            $user_group = new AuthGroupModel();
            $user_group->save($data_group);
            // 调整用户属性
            $user = new UserModel();
            $user->save([
                'is_system' => 1,
                'instance_id' => $instance_id
            ], [
                'uid' => $uid
            ]);
            // 添加后台用户
            $user_admin = new AdminUserModel();
            $data_admin = array(
                'uid' => $uid,
                'admin_name' => '',
                'group_id_array' => $user_group->group_id
            );
            $user_admin->save($data_admin);
            $instance->commit();
            return $instance_id;
        } catch (\Exception $e) {
            $instance->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 修改系统实例
     */
    public function updateSystemInstance()
    {}

    /**
     * 获取系统实例
     *
     * @param unknown $instance_id            
     */
    public function getSystemInstance($instance_id)
    {
        $instance = new InstanceModel();
        $info = $instance->get($instance_id);
        return $info;
    }

    /**
     * 查询系统实例列表
     *
     * @param unknown $where            
     * @param unknown $order            
     * @param unknown $page_size            
     * @param unknown $page_index            
     */
    public function getSystemInstanceList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '*')
    {
        $instance = new InstanceModel();
        $instance_list = $instance->pageQuery($page_index, $page_size, $condition, $order, $field);
        if (! empty($instance_list['data'])) {
            foreach ($instance_list['data'] as $k => $v) {
                $instance_type = new InstanceTypeModel();
                $type_name = $instance_type->getInfo([
                    'instance_typeid' => $v['instance_typeid']
                ], 'type_name');
                if (! empty($type_name['type_name'])) {
                    $v['type_name'] = $type_name['type_name'];
                } else {
                    $v['type_name'] = '';
                }
                $instance_list['data'][$k] = $v;
            }
        }
        return $instance_list;
    }

    /**
     * 通过模块和方法查询权限(non-PHPdoc)
     *
     * @see \data\api\IWebsite::getModuleIdByModule()
     */
    public function getModuleIdByModule($controller, $action)
    {
        $res = $this->module->getModuleIdByModule($controller, $action);
        return $res;
    }

    /**
     * 查询权限节点的根节点
     *
     * @param unknown $module_id            
     */
    public function getModuleRoot($module_id)
    {
        $root_id = $this->module->getModuleRoot($module_id);
        return $root_id;
    }

    /**
     * 获取系统模块列表
     *
     * @param string $tpye
     *            0 debug模式 1 部署模式
     */
    public function getModuleListTree($type = 0)
    {
        $list = $this->module->order('pid,sort')->select();
        $new_list = $this->list_tree($list);
        
        return $new_list;
    }

    /**
     * 数组转化为树
     *
     * @param unknown $list            
     * @param string $p_id            
     * @return multitype:boolean
     */
    private function list_tree($list, $p_id = '0')
    {
        $tree = array();
        foreach ($list as $row) {
            if ($row['pid'] == $p_id) {
                $tmp = $this->list_tree($list, $row['module_id']);
                if ($tmp) {
                    $row['sub_menu'] = $tmp;
                } else {
                    $row['leaf'] = true;
                }
                $tree[] = $row;
            }
        }
        Return $tree;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IWebsite::getModuleListByParentId()
     */
    public function getModuleListByParentId($pid)
    {
        $list = $this->getSystemModuleList(1, 0, 'pid=' . $pid);
        return $list['data'];
    }

    /**
     * 获取当前节点的根节点以及二级节点项(non-PHPdoc)
     *
     * @see \ata\api\IWebsite::getModuleRootAndSecondMenu()
     */
    public function getModuleRootAndSecondMenu($module_id)
    {
        $count = $this->module->where([
            'module_id' => $module_id,
            'module' => \think\Request::instance()->module()
        ])
            ->count();
        if ($count == 0) {
            return array(
                0,
                0
            );
        }
        $info = $this->module->getInfo([
            'module_id' => $module_id,
            'module' => \think\Request::instance()->module(),
            'pid' => array(
                'neq',
                0
            )
        ], 'pid, level');
        if (empty($info)) {
            return array(
                $module_id,
                0
            );
        } else {
            if ($info['level'] == 2) {
                return array(
                    $info['pid'],
                    $module_id
                );
            } else {
                $pid = $info['pid'];
                while ($pid != 0) {
                    $module = $this->module->getInfo([
                        'module_id' => $pid,
                        'module' => \think\Request::instance()->module(),
                        'pid' => array(
                            'neq',
                            0
                        )
                    ], 'pid, module_id, level');
                    if ($module['level'] == 2) {
                        $pid = 0;
                        return array(
                            $module['pid'],
                            $module['module_id']
                        );
                    } else {
                        $pid = $module['pid'];
                    }
                }
            }
        }
    }

    /**
     * 获取模板样式(non-PHPdoc)
     *
     * @see \ata\api\IWebsite::getWebStyle()
     */
    public function getWebStyle()
    {
        $config_style = ''; // 根据用户实例从数据库中获取样式，以及项目
        $style = \think\Request::instance()->module() . '/' . $config_style;
        return $style;
    }

    public function getWebStyleList($condition)
    {
        $webstyle = new WebStyleModel();
        $style_list = $webstyle->getQuery($condition, '*', '');
        return $style_list;
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IWebsite::getWebDetail()
     */
    public function getWebDetail()
    {
        // TODO Auto-generated method stub
        $web_info = $this->website->getInfo(array(
            "website_id" => 1
        ));
        return $web_info;
    }
}