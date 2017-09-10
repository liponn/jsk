<?php
namespace app\admin\controller;

use data\service\Address;
use data\service\Shop as ShopService;
use data\service\Config;

/**
 * 店铺设置控制器
 *
 * @author Administrator
 *        
 */
class Shop extends BaseController
{

    /**
     * 店铺基础设置
     */
    public function shopConfig()
    {
        $child_menu_list = array(
            array(
                'url' => "Shop/shopConfig",
                'menu_name' => "店铺设置",
                "active" => 1
            ),
            array(
                'url' => "Shop/shopStyle",
                'menu_name' => "PC端主题",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopWchatStyle",
                'menu_name' => "微信端主题",
                "active" => 0
            )
        );
        $shop = new ShopService();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $shop_logo = isset($_POST['shop_logo']) ? $_POST['shop_logo'] : '';
            $shop_banner = isset($_POST['shop_banner']) ? $_POST['shop_banner'] : '';
            $shop_avatar = isset($_POST['shop_avatar']) ? $_POST['shop_avatar'] : '';
            $shop_qq = isset($_POST['shop_qq']) ? $_POST['shop_qq'] : '';
            $shop_ww = isset($_POST['shop_ww']) ? $_POST['shop_ww'] : '';
            $shop_phone = isset($_POST['shop_phone']) ? $_POST['shop_phone'] : '';
            $shop_keywords = isset($_POST['shop_keywords']) ? $_POST['shop_keywords'] : '';
            $shop_description = isset($_POST['shop_description']) ? $_POST['shop_description'] : '';
            $res = $shop->updateShopConfigByshop($shop_id, $shop_logo, $shop_banner, $shop_avatar, $shop_qq, $shop_ww, $shop_phone, $shop_keywords, $shop_description);
            return AjaxReturn($res);
        }
        $shop_info = $shop->getShopDetail($this->instance_id);
        $this->assign('shop_info', $shop_info);
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . "Shop/shopConfig");
    }

    /**
     * 店铺幻灯设置
     */
    public function shopAd()
    {
        $child_menu_list = array(
            array(
                'url' => "Shop/shopConfig",
                'menu_name' => "店铺设置",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopAd",
                'menu_name' => "幻灯设置",
                "active" => 1
            ),
            array(
                'url' => "Shop/shopStyle",
                'menu_name' => "PC端主题",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopWchatStyle",
                'menu_name' => "微信端主题",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . "Shop/shopAd");
    }

    /**
     * 店铺主题
     */
    public function shopStyle()
    {
        $child_menu_list = array(
            array(
                'url' => "Shop/shopConfig",
                'menu_name' => "店铺设置",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopStyle",
                'menu_name' => "PC端主题",
                "active" => 1
            ),
            array(
                'url' => "Shop/shopWchatStyle",
                'menu_name' => "微信端主题",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . "Shop/shopStyle");
    }

    /**
     * 微信端样式
     */
    public function shopWchatStyle()
    {
        $child_menu_list = array(
            array(
                'url' => "Shop/shopConfig",
                'menu_name' => "店铺设置",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopStyle",
                'menu_name' => "PC端主题",
                "active" => 0
            ),
            array(
                'url' => "Shop/shopWchatStyle",
                'menu_name' => "微信端主题",
                "active" => 1
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . "Shop/shopWchatStyle");
    }

    /**
     * 自提点列表
     */
    public function pickupPointList()
    {
        $child_menu_list = array(
            array(
                'url' => "express/expresscompany",
                'menu_name' => "物流公司",
                "active" => 0
            ),
            array(
                'url' => "config/areamanagement",
                'menu_name' => "地区管理",
                "active" => 0
            ),
            array(
                'url' => "order/returnsetting",
                'menu_name' => "商家地址",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointlist",
                'menu_name' => "自提点管理",
                "active" => 1
            ),
            array(
                'url' => "shop/pickuppointfreight",
                'menu_name' => "自提点运费",
                "active" => 0
            ),
            array(
                'url' => "config/distributionareamanagement",
                'menu_name' => "货到付款地区管理",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        if (request()->isAjax()) {
            $shop = new ShopService();
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $condition = array(
                'name' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            );
            $result = $shop->getPickupPointList($page_index, $page_size, $condition, 'create_time asc');
            return $result;
        } else {
            return view($this->style . "Shop/sinceList");
        }
    }
    
    /**
     * 自提点运费菜单
     * @return \think\response\View
     */
    public function pickuppointfreight(){
        $child_menu_list = array(
            array(
                'url' => "express/expresscompany",
                'menu_name' => "物流公司",
                "active" => 0
            ),
            array(
                'url' => "config/areamanagement",
                'menu_name' => "地区管理",
                "active" => 0
            ),
            array(
                'url' => "order/returnsetting",
                'menu_name' => "商家地址",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointlist",
                'menu_name' => "自提点管理",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointfreight",
                'menu_name' => "自提点运费",
                "active" => 1
            ),
            array(
                'url' => "config/distributionareamanagement",
                'menu_name' => "货到付款地区管理",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        
        $config_service = new Config();
        $config_info = $config_service->getConfig($this->instance_id, 'PICKUPPOINT_FREIGHT');
        $this->assign('config',json_decode($config_info['value']));
        return view($this->style . "Shop/pickupPointFreight");
    }
    
    /**
     * 修改自提点运费菜单
     */
    public function pickupPointFreightAjax(){
        if(request()->isAjax()){
            $is_enable = request()->post('is_enable','');
            $pickup_freight = request()->post('pickup_freight','');
            $manjian_freight = request()->post('manjian_freight','');
            $config_service = new Config();
            $res = $config_service->setPickupPointFreight($is_enable, $pickup_freight, $manjian_freight);
            return AjaxReturn($res);
        }
    }
    
    /**
     * 添加自提点
     */
    public function addPickupPoint()
    {
        if (request()->isAjax()) {
            $shop = new ShopService();
            $shop_id = $this->instance_id;
            $name = request()->post('name');
            $address = request()->post('address');
            $contact = request()->post('contact');
            $phone = request()->post('phone');
            $province_id = request()->post('province_id');
            $city_id = request()->post('city_id');
            $district_id = request()->post('district_id');
            $res = $shop->addPickupPoint($shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, '', '');
            return AjaxReturn($res);
        }
        return view($this->style . "Shop/addSince");
    }

    /**
     * 修改自提点
     */
    public function updatePickupPoint()
    {
        $pickip_id = isset($_GET['id']) ? $_GET['id'] : '';
        if (request()->isAjax()) {
            $shop = new ShopService();
            $id = request()->post('id');
            $shop_id = $this->instance_id;
            $name = request()->post('name');
            $address = request()->post('address');
            $contact = request()->post('contact');
            $phone = request()->post('phone');
            $province_id = request()->post('province_id');
            $city_id = request()->post('city_id');
            $district_id = request()->post('district_id');
            $res = $shop->updatePickupPoint($id, $shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, '', '');
            return AjaxReturn($res);
        }
        $shop = new ShopService();
        $pickupPoint_detail = $shop->getPickupPointDetail($pickip_id);
        $this->assign('pickupPoint_detail', $pickupPoint_detail);
        $this->assign('pickip_id', $pickip_id);
        return view($this->style . "Shop/updatePickupPoint");
    }

    /**
     * 删除自提点
     */
    public function deletepickupPoint()
    {
        if (request()->isAjax()) {
            $pickip_id = request()->post('pickupPoint_id');
            $shop = new ShopService();
            $res = $shop->deletePickupPoint($pickip_id);
            return AjaxReturn($res);
        }
    }

    /**
     * 获取省列表
     */
    public function getProvince()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        return $province_list;
    }

    /**
     * 获取城市列表
     *
     * @return Ambigous <multitype:\think\static , \think\false, \think\Collection, \think\db\false, PDOStatement, string, \PDOStatement, \think\db\mixed, boolean, unknown, \think\mixed, multitype:, array>
     */
    public function getCity()
    {
        $address = new Address();
        $province_id = isset($_POST['province_id']) ? $_POST['province_id'] : 0;
        $city_list = $address->getCityList($province_id);
        return $city_list;
    }

    /**
     * 获取区域地址
     */
    public function getDistrict()
    {
        $address = new Address();
        $city_id = isset($_POST['city_id']) ? $_POST['city_id'] : 0;
        $district_list = $address->getDistrictList($city_id);
        return $district_list;
    }

    /**
     * 获取选择地址
     *
     * @return unknown
     */
    public function getSelectAddress()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        $province_id = isset($_POST['province_id']) ? $_POST['province_id'] : 0;
        $city_id = isset($_POST['city_id']) ? $_POST['city_id'] : 0;
        $city_list = $address->getCityList($province_id);
        $district_list = $address->getDistrictList($city_id);
        $data["province_list"] = $province_list;
        $data["city_list"] = $city_list;
        $data["district_list"] = $district_list;
        return $data;
    }
}
