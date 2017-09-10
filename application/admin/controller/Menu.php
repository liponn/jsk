<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 菜单
 */
class Menu extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addonmenu()
    {
        $this->getThreeLevelModule();//三级菜单
        $addons = request()->param('addons'); // 插件名称
        $params = request()->get('params', ''); // 插件参数
        $this->assign('params', json_decode($params, true));
        $this->assign('hook_name', $addons);
        return view($this->style . 'Menu/addonmenu');
    }
}