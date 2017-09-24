<?php
namespace app\wap\controller;

use data\service\Goods as GoodsService;
use data\service\GoodsBrand as GoodsBrand;
use data\service\GoodsCategory;
use data\service\GoodsLeibie;
use data\service\GoodsGroup;
use data\service\Order as OrderService;
use data\service\Platform;
use data\service\promotion\GoodsExpress;
use data\service\Address;
use data\service\Config as WebConfig;

/**
 * 商品相关
 *
 * @author Administrator
 *        
 */
class Goods extends BaseController
{

    /**
     * 商品详情
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function goodsDetail()
    {
        $goods_id = isset($_GET['id']) ? $_GET['id'] : 0;
        if ($goods_id == 0) {
            $this->error("没有获取到商品信息");
        }
        $goods = new GoodsService();
        $goods_detail = $goods->getGoodsDetail($goods_id);
        if (empty($goods_detail)) {
            $this->error("没有获取到商品信息");
        }
        // 把属性值相同的合并
        $goods_attribute_list = $goods_detail['goods_attribute_list'];
        $goods_attribute_list_new = array();
        foreach ($goods_attribute_list as $item) {
            $attr_value_name = '';
            foreach ($goods_attribute_list as $key => $item_v) {
                if ($item_v['attr_value_id'] == $item['attr_value_id']) {
                    $attr_value_name .= $item_v['attr_value_name'] . ',';
                    unset($goods_attribute_list[$key]);
                }
            }
            if (! empty($attr_value_name)) {
                array_push($goods_attribute_list_new, array(
                    'attr_value_id' => $item['attr_value_id'],
                    'attr_value' => $item['attr_value'],
                    'attr_value_name' => rtrim($attr_value_name, ',')
                ));
            }
        }
        $goods_detail['goods_attribute_list'] = $goods_attribute_list_new;
        
        $user_location = get_city_by_ip();
        $this->assign("user_location", get_city_by_ip()); // 获取用户位置信息
        if ($user_location['status'] == 1) {
            // 定位成功，查询当前城市的运费
            $goods_express = new GoodsExpress();
            $address = new Address();
            $province = $address->getProvinceId($user_location["province"]);
            $city = $address->getCityId($user_location["city"]);
            $district = $address->getCityFirstDistrict($city['city_id']);
            $express = $goods_express->getGoodsExpressTemplate($goods_id, $province['province_id'], $city['city_id'], $district);
            $goods_info["shipping_fee_name"] = $express;
        }
        //获取当前时间
        $current_time = $this->getCurrentTime();
        $this->assign('ms_time',$current_time);
        
        $this->assign("goods_detail", $goods_detail);
        $this->assign("shopname", $this->shop_name);
        $this->assign("price", intval($goods_detail["promotion_price"]));
        $this->assign("goods_id", $goods_id);
        $this->getCartInfo($goods_id);
        // 分享
        $ticket = $this->getShareTicket();
        $this->assign("signPackage", $ticket);
        // 评价数量
        $evaluates_count = $goods->getGoodsEvaluateCount($goods_id);
        $this->assign('evaluates_count', $evaluates_count);
        // 美洽客服
        $shop_id = $this->instance_id;
        $config_service = new WebConfig();
        $list = $config_service->getcustomserviceConfig($shop_id);
        if (empty($list)) {
            $list['id'] = '';
            $list['value']['service_addr'] = '';
        }
        $this->assign("list", $list);
        
        // 查询点赞记录表，获取详情再判断当天该店铺下该商品该会员是否已点赞
        $goods = new GoodsService();
        $shop_id = $this->instance_id;
        $uid = $this->uid;
        $click_detail = $goods->getGoodsSpotFabulous($shop_id, $uid, $goods_id);
        $this->assign('click_detail', $click_detail);
        
        return view($this->style . 'Goods/goodsDetail');
    }
    
    /**
     * 得到当前时间戳的毫秒数
     * @return number
     */
    public function getCurrentTime(){
        $time=time();
        $time=$time*1000;
        return $time;
    }
    
    /**
     * 功能：商品评论
     * 创建人：李志伟
     * 创建时间：2017年2月23日11:12:57
     */
    public function getGoodsComments()
    {
        $comments_type = $_POST['comments_type'];
        $order = new OrderService();
        $condition['goods_id'] = $_POST["goods_id"];
        switch ($comments_type) {
            case 1:
                $condition['explain_type'] = 1;
                break;
            case 2:
                $condition['explain_type'] = 2;
                break;
            case 3:
                $condition['explain_type'] = 3;
                break;
            case 4:
                $condition['image|again_image'] = array(
                    'NEQ',
                    ''
                );
                break;
        }
        $condition['is_show'] = 1;
        $goodsEvaluationList = $order->getOrderEvaluateDataList(1, PAGESIZE, $condition, 'addtime desc');
        return $goodsEvaluationList;
    }

    /**
     * 返回商品数量和当前商品的限购
     *
     * @param unknown $goods_id            
     */
    public function getCartInfo($goods_id)
    {
        $goods = new GoodsService();
        $cartlist = $goods->getCart($this->uid);
        $num = 0;
        foreach ($cartlist as $v) {
            if ($v["goods_id"] == $goods_id) {
                $num = $v["num"];
            }
        }
        $this->assign("carcount", count($cartlist)); // 购物车商品数量
        $this->assign("num", $num); // 购物车已购买商品数量
    }

    /**
     * 购物车页面
     */
    public function cart()
    {
        $this->is_member = $this->user->getSessionUserIsMember();
        if ($this->is_member == 0) {
            $redirect = __URL(__URL__ . "/wap/login");
            $this->redirect($redirect);
        }
        $this->assign("shopname", $this->shop_name);
        $goods = new GoodsService();
        $cartlist = $goods->getCart($this->uid, $this->instance_id);
        // var_dump($this->instance_id);exit;
        // 店铺，店铺中的商品
        $list = Array();
        for ($i = 0; $i < count($cartlist); $i ++) {
            // $cartlist[$i]["goods_name"] = mb_substr($cartlist[$i]["goods_name"], 0,20,"utf-8");
            // $cartlist[$i]["sku_name"] = mb_substr($cartlist[$i]["goods_name"], 0,20,"utf-8");
            $list[$cartlist[$i]["shop_id"] . ',' . $cartlist[$i]["shop_name"]][] = $cartlist[$i];
        }
        // var_dump($list);exit;
        $this->assign("list", $list);
        $this->assign("countlist", count($cartlist));
        return view($this->style . '/Goods/cart');
    }

    /**
     * 添加购物车
     */
    public function addCart()
    {
        $cart_detail = $_POST['cart_detail'];
        $cart_tag = $_POST['cart_tag'];
        $uid = $this->uid;
        $shop_id = $cart_detail["shop_id"];
        $shop_name = $cart_detail["shop_name"];
        $goods_id = $cart_detail['trueId'];
        $goods_name = $cart_detail['goods_name'];
        $num = $cart_detail['count'];
        // $sku_id = $cart_detail['select_skuid'];
        $sku_id = $cart_detail['trueId'];
        $sku_name = $cart_detail['select_skuName'];
        $price = $cart_detail['price'];
        $cost_price = $cart_detail['cost_price'];
        $picture = $cart_detail['picture'];
        // var_dump($_POST);exit;
        $this->is_member = $this->user->getSessionUserIsMember();
        if (! empty($this->uid) && $this->is_member == 1) {
            /* if($cart_tag == "addCart") { */
            $goods = new GoodsService();
            $retval = $goods->addCart($uid, $shop_id, $shop_name, $goods_id, $goods_name, $sku_id, $sku_name, $price, $num, $picture, 0);
            
            /*
             * }else{
             * $retval = 0;
             * }
             */
        } else {
            $retval = "-1";
        }
        return $retval;
    }

    /**
     * 购物车修改数量
     */
    public function cartAdjustNum()
    {
        if (request()->isAjax()) {
            $cart_id = $_POST['cartid'];
            $num = $_POST['num'];
            $goods = new GoodsService();
            $retval = $goods->cartAdjustNum($cart_id, $num);
            return AjaxReturn($retval);
        } else
            return AjaxReturn(- 1);
    }

    /**
     * 购物车项目删除
     */
    public function cartDelete()
    {
        if (request()->isAjax()) {
            $cart_id_array = $_POST['del_id'];
            $goods = new GoodsService();
            $retval = $goods->cartDelete($cart_id_array);
            return AjaxReturn($retval);
        } else
            return AjaxReturn(- 1);
    }

    /**
     * 平台商品分类列表
     */
    public function goodsClassificationList()
    {
        $goods_leibie = new GoodsLeibie();
        // $goods_category_list_1 = $goods_category->getGoodsCategoryList(1, 0, [
        //     "is_visible" => 1,
        //     "level" => 1
        // ]);
        // var_dump($goods_category_list_1['data'][0]);
        // print_r(json_decode(json_encode($goods_category_list_1['data']), true) ); 

        //一级分类
        $sql = "SELECT * FROM jc_chanpinleibie WHERE `上级代码` = ''";
        $goods_category_list_1 = $goods_leibie->getGoodsCategoryList(1,0,$sql);
        foreach ($goods_category_list_1 as &$value) {
            # code...
            $value['category_id'] = $value['代码'];
            $value['short_name'] = $value['名称'];
        }
        unset($value);

        $sql = "SELECT * FROM jc_chanpinleibie WHERE `上级代码` NOT LIKE '%.%' AND `上级代码` != ''";
        $goods_category_list_2 = $goods_leibie->getGoodsCategoryList(1,0,$sql);
        foreach ($goods_category_list_2 as &$value) {
            # code...
            $value['category_id'] = $value['代码'];
            $value['short_name'] = $value['名称'];
            $value['pid'] = $value['上级代码'];
        }
        unset($value);

        $sql = "SELECT * FROM jc_chanpinleibie WHERE `上级代码` LIKE '%.%' AND `上级代码` != ''";
        $goods_category_list_3 = $goods_leibie->getGoodsCategoryList(1,0,$sql);
        foreach ($goods_category_list_3 as &$value) {
            # code...
            $value['category_id'] = $value['代码'];
            $value['short_name'] = explode('_', $value['名称'])[2];
            $value['pid'] = $value['上级代码'];
        }
        unset($value);
        // $goods_category_list_3 = [
        //     [
        //         'category_id'=>1,
        //         'category_name'=> '曲面电视',
        //         'short_name'=>'曲面电视',
        //         'pid' => 14,
        //         'level' => 3,
        //         'is_visible' => 1,
        //         'attr_id' => 0,
        //         'attr_name' => '',
        //         'keywords' => '曲面电视',
        //         'description' => '曲面电视',
        //         'sort' => 1,
        //         'category_pic' => '',
        //     ],
        //     [
        //         'category_id'=>1,
        //         'category_name'=> '曲面电视2',
        //         'short_name'=>'曲面电视2',
        //         'pid' => 14,
        //         'level' => 3,
        //         'is_visible' => 1,
        //         'attr_id' => 0,
        //         'attr_name' => '',
        //         'keywords' => '曲面电视2',
        //         'description' => '曲面电视2',
        //         'sort' => 1,
        //         'category_pic' => '',
        //     ],
        // ];

        $this->assign("goods_category_list_1", $goods_category_list_1);
        $this->assign("goods_category_list_2", $goods_category_list_2);
        $this->assign("goods_category_list_3", $goods_category_list_3);
        $this->assign("footer_check", 'classify_check');//底部导航选中参数
        return view($this->style . 'Goods/goodsClassificationList');
    }

    /**
     * 店铺商品分组列表
     */
    public function goodsGroupList()
    {
        // 查询购物车中商品的数量
        $uid = $this->uid;
        $goods = new GoodsService();
        $cartlist = $goods->getCart($uid);
        $this->assign('uid', $uid);
        $this->assign("carcount", count($cartlist));
        
        $components = new Components();
        $grouplist = $components->goodsGroupList($this->shop_id);
        $group_frist_list = null;
        $group_second_list = null;
        foreach ($grouplist as $group) {
            if ($group["pid"] == 0) {
                $group_frist_list[] = $group;
            } else {
                $group_second_list[] = $group;
            }
        }
        $this->assign("group_frist_list", $group_frist_list);
        $this->assign("group_second_list", $group_second_list);
        
        $group_goods = new GoodsGroup();
        $tree_list = $group_goods->getGroupGoodsTree($this->shop_id);
        $this->assign("tree_list", $tree_list);
        return view($this->style . 'Goods/goodsGroupList');
    }

    /**
     * 商品分类列表
     */
    public function goodsCategoryList()
    {
        $goodscate = new GoodsCategory();
        $one_list = $goodscate->getGoodsCategoryListByParentId(0);
        if (! empty($one_list)) {
            foreach ($one_list as $k => $v) {
                $two_list = array();
                $two_list = $goodscate->getGoodsCategoryListByParentId($v['category_id']);
                $v['child_list'] = $two_list;
                if (! empty($two_list)) {
                    foreach ($two_list as $k1 => $v1) {
                        $three_list = array();
                        $three_list = $goodscate->getGoodsCategoryListByParentId($v1['category_id']);
                        $v1['child_list'] = $three_list;
                    }
                }
            }
        }
        return $one_list;
    }

    /**
     * 加入购物车前显示商品规格
     */
    public function joinCartInfo()
    {
        $goods = new GoodsService();
        $goods_id = isset($_POST['goods_id']) ? $_POST['goods_id'] : '';
        $goods_detail = $goods->_getGoodsDetail($goods_id);
        // var_dump($goods_detail);exit;
        $this->assign("goods_detail", $goods_detail);
        $this->assign("style", $this->style);
        return view($this->style . 'joinCart');
    }

    /**
     * 搜索商品显示
     */
    public function goodsSearchList()
    {
        if (request()->isAjax()) {
            $sear_name = isset($_POST['sear_name']) ? $_POST['sear_name'] : '';
            $sear_type = isset($_POST['sear_type']) ? $_POST['sear_type'] : '';
            $order_state = isset($_POST['order_state']) ? $_POST['order_state'] : 'desc';
            $controlType = isset($_POST['controlType']) ? $_POST['controlType'] : '';
            $shop_id = isset($_POST['shop_id']) ? $_POST['shop_id'] : '';
            $goods = new GoodsService();
            $condition['goods_name'] = [
                'like',
                '%' . $sear_name . '%'
            ];
            // 排序类型
            switch ($sear_type) {
                case 1:
                    $order = 'create_time desc'; // 时间
                    break;
                case 2:
                    $order = 'sales desc'; // 销售
                    break;
                case 3:
                    $order = 'promotion_price ' . $order_state; // 促销价格
                    break;
                default:
                    $order = '';
                    break;
            }
            switch ($controlType) {
                case 1:
                    $condition = [
                        'is_new' => 1
                    ];
                    break;
                case 2:
                    $condition = [
                        'is_hot' => 1
                    ];
                    break;
                case 3:
                    $condition = [
                        'is_recommend' => 1
                    ];
                    break;
                default:
                    break;
            }
            if (! empty($shop_id)) {
                $condition['ng.shop_id'] = $shop_id;
            }
            $condition['ng.state'] = 1;
            
            $search_good_list = $goods->getGoodsList(1, 0, $condition, $order);
            return $search_good_list['data'];
        } else {
            $sear_name = isset($_GET['sear_name']) ? $_GET['sear_name'] : '';
            $controlType = isset($_GET['controlType']) ? $_GET['controlType'] : ''; // 什么类型 1最新 2精品 3推荐
            $controlTypeName = isset($_GET['controlTypeName']) ? $_GET['controlTypeName'] : ''; // 什么类型 1最新 2精品 3推荐
            
            if (! empty($sear_name)) {
                $search_title = $sear_name;
            } else {
                $search_title = $controlTypeName;
            }
            if (mb_strlen($sear_name) > 10) {
                $sear_name = mb_substr($sear_name, 0, 7, 'utf-8') . '...';
            }
            $shop_id = $this->shop_id;
            $this->assign('controlType', $controlType);
            $this->assign('wherename', 'sear_name');
            $this->assign('sear_name', $sear_name);
            $this->assign('shop_id', $shop_id);
            $this->assign('search_title', $search_title);
            return view($this->style . 'Goods/goodsSearchList');
        }
    }

    /**
     * 品牌专区
     */
    public function brandlist()
    {
        $platform = new Platform();
        // 品牌专区广告位
        $brand_adv = $platform->getPlatformAdvPositionDetail(1162);
        $this->assign('brand_adv', $brand_adv);
        
        if (request()->isAjax()) {
            $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '0';
            
            if (! empty($category_id)) {
                $condition['category_id_1'] = $category_id;
            }
            $goods_brand = new GoodsBrand();
            $list = $goods_brand->getGoodsBrandList(1, '', $condition, 'sort');
            return $list['data'];
        } else {
            $goods_category = new GoodsCategory();
            $goods_category_list_1 = $goods_category->getGoodsCategoryList(1, 0, [
                "is_visible" => 1,
                "level" => 1
            ]);
            $this->assign("goods_category_list_1", $goods_category_list_1["data"]);
            
            return view($this->style . 'Goods/brandlist');
        }
    }

    /**
     * 商品列表
     */
    public function goodsList()
    {
        // 查询购物车中商品的数量
        $uid = $this->uid;
        $goods = new GoodsService();
        // $cartlist = $goods->getCart($uid);
        // $this->assign('uid', $uid);
        // $this->assign("carcount", count($cartlist));
        
        if (request()->isAjax()) {
            $category_id = isset($_POST["category_id"]) ? $_POST["category_id"] : ""; // 商品分类
            $brand_id = isset($_POST["brand_id"]) ? $_POST["brand_id"] : ""; // 品牌
            $order = isset($_POST["order"]) ? $_POST["order"] : ""; // 商品排序分类
            $sort = isset($_POST["sort"]) ? $_POST["sort"] : "desc"; // 商品排序分类
            
            $goods = new GoodsService();
            $goods_list = $goods->getGoodsList(1, 0, $category_id);
            // var_dump($goods_list);exit;
            // $goods_list['data'] = [
            //     array(
            //         'goods_id' => 33,
            //         'goods_name' => '320A高清32吋智能WIFI网络',
            //         'promotion_price' => '1399.00',
            //         'pic_cover_small' => '',
            //         'sales' =>0,
            //         'state' =>1,
            //         ),
            // ];
            return $goods_list;
        } else {
            $category_id = isset($_GET["category_id"]) ? $_GET["category_id"] : ""; // 商品分类
            $brand_id = isset($_GET["brand_id"]) ? $_GET["brand_id"] : ""; // 品牌
            $this->assign('brand_id', $brand_id);
            $this->assign('category_id', $category_id);
            return view($this->style . 'Goods/goodsList');
        }
    }

    /**
     * 积分中心
     *
     * @return \think\response\View
     */
    public function integralCenter()
    {
        $platform = new Platform();
        // 积分中心广告位
        $discount_adv = $platform->getPlatformAdvPositionDetail(1165);
        $this->assign('discount_adv', $discount_adv);
        // 积分中心商品
        $this->goods = new GoodsService();
        $order = "";
        // 排序
        if (isset($_GET["id"])) {
            if ($_GET["id"] == 1) {
                $order = "sales desc";
            } else 
                if ($_GET["id"] == 2) {
                    $order = "collects desc";
                } else 
                    if ($_GET["id"] == 3) {
                        $order = "evaluates desc";
                    } else 
                        if ($_GET["id"] == 4) {
                            $order = "shares desc";
                        } else {
                            $_GET["id"] = 0;
                            $order = "";
                        }
        } else {
            $_GET["id"] = 0;
        }
        
        $page_index = isset($_GET['page']) ? $_GET['page'] : '1';
        $condition = array(
            "ng.state" => 1,
            "ng.point_exchange_type" => array(
                'NEQ',
                0
            )
        );
        $page_count = 25;
        $hotGoods = $this->goods->getGoodsList(1, 4, $condition, $order);
        $allGoods = $this->goods->getGoodsList($page_index, $page_count, $condition, $order);
        if (isset($_GET["page"])) {
            if (($_GET["page"] > 1 && $_GET["page"] <= $allGoods["page_count"])) {
                $_GET["page"] = 1;
            }
        }
        $this->assign("id", $_GET["id"]);
        $this->assign('page', $page_index);
        $this->assign("allGoods", $allGoods);
        $this->assign("hotGoods", $hotGoods);
        $this->assign('page_count', $allGoods['page_count']);
        $this->assign('total_count', $allGoods['total_count']);
        return view($this->style . 'Goods/integralCenter');
    }

    /**
     * 积分中心 全部积分商品
     *
     * @return \think\response\View
     */
    public function integralCenterList()
    {
        return view($this->style . 'Goods/integralCenterList');
    }

    /**
     * 积分中心全部商品Ajax
     */
    public function integralCenterListAjax()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            // 积分中心商品
            $this->goods = new GoodsService();
            $order = "";
            // 排序
            if (isset($_POST["id"])) {
                if ($_POST["id"] == 1) {
                    $order = "sales desc";
                } else 
                    if ($_POST["id"] == 2) {
                        $order = "collects desc";
                    } else 
                        if ($_POST["id"] == 3) {
                            $order = "evaluates desc";
                        } else 
                            if ($_POST["id"] == 4) {
                                $order = "shares desc";
                            } else {
                                $_POST["id"] = 0;
                                $order = "";
                            }
            } else {
                $_POST["id"] = 0;
            }
            
            $page_index = isset($_POST['page']) ? $_POST['page'] : '1';
            $condition = array(
                "ng.state" => 1,
                "ng.point_exchange_type" => array(
                    'NEQ',
                    0
                )
            );
            $page_count = 25;
            $allGoods = $this->goods->getGoodsList($page_index, $page_count, $condition, $order);
            return $allGoods['data'];
        }
    }

    /**
     * 设置点赞送积分
     */
    public function getClickPoint()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $uid = $this->uid;
            $goods_id = request()->post('goods_id', '');
            $goods = new GoodsService();
            $retval = $goods->setGoodsSpotFabulous($shop_id, $uid, $goods_id);
            return AjaxReturn($retval);
        }
    }
    
    /**
     * 获取商品分类下的商品
     */
    public function getCategoryChildGoods(){
        if (request()->isAjax()) {
            $page = request()->post("page",1);
            $category_id = request()->post("category_id","");
            $goods = new GoodsService();
            if($category_id == 0){
                $condition['ng.state'] = 1;
                $res = $goods->getGoodsList($page,15,$condition);
                $result = $goods->getGoodsList($page,0);
                $count = count($result['data']);
                $res['page_count'] = ceil($count/15);
            }else{
                $condition['ng.category_id'] = $category_id;
                $condition['ng.state'] = 1;
                $res = $goods->getGoodsList($page,15,$condition);
                $result = $goods->getGoodsList($page,0,$condition);
                $count = count($result['data']);
                $res['page_count'] = ceil($count/15);
            }
            return $res;
        }
    }
}