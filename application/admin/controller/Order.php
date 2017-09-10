<?php

namespace app\admin\controller;

use data\service\Address as AddressService;
use data\service\Express;
use data\service\Express as ExpressService;
use data\service\Order\OrderGoods;
use data\service\Order\OrderStatus;
use data\service\Order as OrderService;
use data\service\Address;
/**
 * 订单控制器
 *
 * @author Administrator
 *        
 */
class Order extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 订单列表
     */
    public function orderList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $start_date = request()->post('start_date') == "" ? 0 : getTimeTurnTimeStamp(request()->post('start_date'));
            $end_date = request()->post('end_date') == "" ? 0 : getTimeTurnTimeStamp(request()->post('end_date'));
            $user_name = request()->post('user_name', '');
            $order_no = request()->post('order_no', '');
            $order_status = request()->post('order_status', '');
            $receiver_mobile = request()->post('receiver_mobile', '');
            $payment_type = request()->post('payment_type', 1);
            if($start_date != 0 && $end_date != 0){
                $condition["create_time"] = [
                    [
                        ">",
                        $start_date
                    ],
                    [
                        "<",
                        $end_date
                    ]
                ];
            }elseif($start_date != 0 && $end_date == 0){
                $condition["create_time"] = [
                    [
                        ">",
                        $start_date
                    ]
                ];
            }elseif($start_date == 0 && $end_date != 0){
                $condition["create_time"] = [
                    [
                        "<",
                        $end_date
                    ]
                ];
            }
            if ($order_status != '') {
                // $order_status 1 待发货
                if ($order_status == 1) {
                    // 订单状态为待发货实际为已经支付未完成还未发货的订单
                    $condition['shipping_status'] = 0; // 0 待发货
                    $condition['pay_status'] = 2; // 2 已支付
                    $condition['order_status'] = array(
                        'neq',
                        4
                    ); // 4 已完成
                    $condition['order_status'] = array(
                        'neq',
                        5
                    ); // 5 关闭订单
                } else
                    $condition['order_status'] = $order_status;
            }
            if (! empty($payment_type)) {
                $condition['payment_type'] = $payment_type;
            }
            if (! empty($user_name)) {
                $condition['receiver_name'] = $user_name;
            }
            if (! empty($order_no)) {
                $condition['order_no'] = $order_no;
            }
            if (! empty($receiver_mobile)) {
                $condition['receiver_mobile'] = $receiver_mobile;
            }
            $condition['shop_id'] = $this->instance_id;
            $order_service = new OrderService();
            $list = $order_service->getOrderList($page_index, $page_size, $condition, 'create_time desc');
            return $list;
        } else {
            $status = request()->get('status', '');
            $this->assign("status", $status);
            $all_status = OrderStatus::getOrderCommonStatus();
            $child_menu_list = array();
            $child_menu_list[] = array(
                'url' => "Order/orderList",
                'menu_name' => '全部',
                "active" => $status == '' ? 1 : 0
            );
            foreach ($all_status as $k => $v) {
                // 针对发货与提货状态名称进行特殊修改
                /*
                 * if($v['status_id'] == 1)
                 * {
                 * $status_name = '待发货/待提货';
                 * }elseif($v['status_id'] == 3){
                 * $status_name = '已收货/已提货';
                 * }else{
                 * $status_name = $v['status_name'];
                 * }
                 */
                $child_menu_list[] = array(
                    'url' => "order/orderlist?status=" . $v['status_id'],
                    'menu_name' => $v['status_name'],
                    "active" => $status == $v['status_id'] ? 1 : 0
                );
            }
            $this->assign('child_menu_list', $child_menu_list);
            // 获取物流公司
            $express = new ExpressService();
            $where = 1;
            $expressList = $express->expressCompanyQuery();
            // var_dump($expressList);exit;
            // $this->assign('expressList', $expressList);
            return view($this->style . "Order/orderList");
        }
    }

    /**
     * 功能说明：获取店铺信息
     */
    public function getShopInfo()
    {
        // 获取信息
        $shopInfo['shopId'] = $this->instance_id;
        $shopInfo['shopName'] = $this->instance_name;
        // 返回信息
        return $shopInfo;
    }

    /**
     * 功能说明：获取打印订单项预览信息
     */
    public function getOrderExpressPreview()
    {
        $shop_id = $this->instance_id;
        // 获取值
        $orderIdArray = $_GET['ids'];
        // 操作
        $order_service = new OrderService();
        $goods_express_list = $order_service->getOrderGoodsExpressDetail($orderIdArray, $shop_id);
        // 返回信息
        return $goods_express_list;
    }

    /**
     * 功能说明：打印预览 发货单
     */
    public function printDeliveryPreview()
    {
        // 获取值
        $order_service = new OrderService();
        $order_ids = $_GET["order_ids"] ? $_GET["order_ids"] : "";
        $ShopName = $_GET["ShopName"] ? $_GET["ShopName"] : "";
        $shop_id = $this->instance_id;
        $order_str = explode(",", $order_ids);
        $order_array = array();
        foreach ($order_str as $order_id) {
            $detail = array();
            $detail = $order_service->getOrderDetail($order_id);
            if (empty($detail)) {
                $this->error("没有获取到订单信息");
            }
            $order_array[] = $detail;
        }
        $receive_address = $order_service->getShopReturnSet($shop_id);
        $this->assign("order_print", $order_array);
        $this->assign("ShopName", $ShopName);
        $this->assign("receive_address", $receive_address);
        return view($this->style . 'Order/printDeliveryPreview');
    }

    /**
     * 打印快递单
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function printExpressPreview()
    {
        $order_service = new OrderService();
        $address_service = new AddressService();
        
        $order_ids = $_GET["order_ids"] ? $_GET["order_ids"] : "";
        $ShopName = $_GET["ShopName"] ? $_GET["ShopName"] : "";
        $co_id = $_GET["co_id"] ? $_GET["co_id"] : "";
        
        $shop_id = $this->instance_id;
        $order_str = explode(",", $order_ids);
        $order_array = array();
        foreach ($order_str as $order_id) {
            $detail = array();
            $detail = $order_service->getOrderDetail($order_id);
            if (empty($detail)) {
                $this->error("没有获取到订单信息");
            }
            //$detail['address'] = $address_service->getAddress($detail['receiver_province'], $detail['receiver_city'], $detail['receiver_district']);
            $order_array[] = $detail;
        }
        $express_server = new ExpressService();
        // 物流模板信息
        $express_shipping = $express_server->getExpressShipping($co_id);
        // 物流打印信息
        $express_shipping_item = $express_server->getExpressShippingItems($express_shipping["sid"]);
        $receive_address = $order_service->getShopReturnSet($shop_id);
        $this->assign("order_print", $order_array);
        $this->assign("ShopName", $ShopName);
        $this->assign("express_ship", $express_shipping);
        $this->assign("express_item_list", $express_shipping_item);
        $this->assign("receive_address", $receive_address);
        return view($this->style . 'Order/printExpressPreview');
    }

    /**
     * 订单详情
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function orderDetail()
    {
        $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
        if ($order_id == 0) {
            $this->error("没有获取到订单信息");
        }
        $order_service = new OrderService();
        $detail = $order_service->getOrderDetail($order_id);
        if (empty($detail)) {
            $this->error("没有获取到订单信息");
        }
        if(!empty($detail['operation'])){
            $operation_array = $detail['operation'];        
            foreach($operation_array as $k=>$v){
             if($v["no"] == 'logistics')  {
                 unset($operation_array[$k]);
             } 
            }
            $detail['operation'] = $operation_array;
        }
        $this->assign("order", $detail);
        return view($this->style . "Order/orderDetail");
    }

    /**
     * 订单退款详情
     */
    public function orderRefundDetail()
    {
        $order_goods_id = isset($_GET['itemid']) ? $_GET['itemid'] : 0;
        if ($order_goods_id == 0) {
            $this->error("没有获取到退款信息");
        }
        $order_service = new OrderService();
        $info = $order_service->getOrderGoodsRefundInfo($order_goods_id);
        $this->assign('order_goods', $info);
        return view($this->style . "Order/orderRefundDetail");
    }

    /**
     * 线下支付
     */
    public function orderOffLinePay()
    {
        $order_service = new OrderService();
        $order_id = $_POST['order_id'];
        $order_info = $order_service->getOrderInfo($order_id);
        if ($order_info['payment_type'] == 6) {
            $res = $order_service->orderOffLinePay($order_id, 6, 0);
        } else {
            $res = $order_service->orderOffLinePay($order_id, 10, 0);
        }
        
        return AjaxReturn($res);
    }

    /**
     * 交易完成
     *
     * @param unknown $orderid            
     * @return Exception
     */
    public function orderComplete()
    {
        $order_service = new OrderService();
        $order_id = $_POST['order_id'];
        $res = $order_service->orderComplete($order_id);
        return AjaxReturn($res);
    }

    /**
     * 交易关闭
     */
    public function orderClose()
    {
        $order_service = new OrderService();
        $order_id = $_POST['order_id'];
        $res = $order_service->orderClose($order_id);
        return AjaxReturn($res);
    }

    /**
     * 订单发货 所需数据
     */
    public function orderDeliveryData()
    {
        $order_service = new OrderService();
        $express_service = new ExpressService();
        $address_service = new AddressService();
        $order_id = $_POST['order_id'];
        $order_info = $order_service->getOrderDetail($order_id);
        $order_info['address'] = $address_service->getAddress($order_info['receiver_province'], $order_info['receiver_city'], $order_info['receiver_district']);
        $shopId = $this->instance_id;
        // 快递公司列表
        $express_company_list = $express_service->expressCompanyQuery('shop_id = ' . $shopId, "*");
        // 订单商品项
        $order_goods_list = $order_service->getOrderGoods($order_id);
        $data['order_info'] = $order_info;
        $data['express_company_list'] = $express_company_list;
        $data['order_goods_list'] = $order_goods_list;
        return $data;
    }

    /**
     * 订单发货
     */
    public function orderDelivery()
    {
        $order_service = new OrderService();
        $order_id = $_POST['order_id'];
        $order_goods_id_array = $_POST['order_goods_id_array'];
        $express_name = $_POST['express_name'];
        $shipping_type = $_POST['shipping_type'];
        $express_company_id = $_POST['express_company_id'];
        $express_no = $_POST['express_no'];
        if ($shipping_type == 1) {
            $res = $order_service->orderDelivery($order_id, $order_goods_id_array, $express_name, $shipping_type, $express_company_id, $express_no);
        } else {
            $res = $order_service->orderGoodsDelivery($order_id, $order_goods_id_array);
        }
        return AjaxReturn($res);
    }

    /**
     * 获取订单大订单项
     */
    public function getOrderGoods()
    {
        $order_id = $_POST['order_id'];
        $order_service = new OrderService();
        $order_goods_list = $order_service->getOrderGoods($order_id);
        $order_info = $order_service->getOrderInfo($order_id);
        $list[0] = $order_goods_list;
        $list[1] = $order_info;
        return $list;
    }

    /**
     * 订单价格调整
     */
    public function orderAdjustMoney($order_id, $order_goods_id_adjust_array, $shipping_fee)
    {
        $order_id = $_POST['order_id'];
        $order_goods_id_adjust_array = $_POST['order_goods_id_adjust_array'];
        $shipping_fee = $_POST['shipping_fee'];
        $order_service = new OrderService();
        $res = $order_service->orderMoneyAdjust($order_id, $order_goods_id_adjust_array, $shipping_fee);
        return AjaxReturn($res);
    }

    public function test()
    {
        $order_service = new OrderService();
        $list = $order_service->test();
        var_dump($list);
    }

    public function orderGoodsOpertion()
    {
        $order_goods = new OrderGoods();
        $order_id = 14;
        $order_goods_id = 35;
        
        // 申请退款
        $refund_type = 2;
        $refund_require_money = 202;
        $refund_reason = '不想买了';
        $retval = $order_goods->orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason);
        
        // 卖家同意退款
        // $retval = $order_goods->orderGoodsRefundAgree($order_id, $order_goods_id);
        
        // 卖家确认退款
        // $refund_real_money = 10;
        // $retval = $order_goods->orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money);
        
        // 买家退货
        // $refund_shipping_company = 8;
        // $refund_shipping_code = '545654465';
        // $retval = $order_goods->orderGoodsReturnGoods($order_id ,$order_goods_id, $refund_shipping_company, $refund_shipping_code);
        
        // 卖家确认收货
        // $retval = $order_goods->orderGoodsConfirmRecieve($order_id, $order_goods_id);
        
        // 买家取消订单
        // $retval = $order_goods->orderGoodsCancel($order_id ,$order_goods_id);
        
        // 卖家拒绝退款
        // $retval = $order_goods->orderGoodsRefuseForever($order_id, $order_goods_id);
        
        // 卖家拒绝本次退款
        // $retval = $order_goods->orderGoodsRefuseOnce($order_id, $order_goods_id);
        // $orderGoodsList = NsOrderGoodsModel::where("order_id=$order_id AND refund_status<>0 AND refund_status<>5")->select();
        // $map = array("order_id"=>$order_id, "refund_status"=>array("neq", 0), "refund_status"=>array("neq", 5));
        // $orderGoodsList = NsOrderGoodsModel::all($map);
        // $refund_count = count($orderGoodsList);
        // $orderGoodsListTotal = NsOrderGoodsModel::where("order_id=$order_id AND refund_status=5")->count();
        // $total_count = count($orderGoodsListTotal);
        // $retval = $orderGoodsListTotal;
        var_dump($retval);
    }

    /**
     * 买家申请退款
     *
     * @return Ambigous <number, \data\service\niushop\Order\Exception, \data\service\niushop\Order\Ambigous>
     */
    public function orderGoodsRefundAskfor()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $this->error("缺少必需参数order_id");
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : $this->error("缺少必需参数order_goods_id");
        $refund_type = isset($_POST['refund_type']) ? $_POST['refund_type'] : $this->error("缺少必填参数refund_type");
        $refund_require_money = isset($_POST['refund_require_money']) ? $_POST['refund_require_money'] : 0;
        $refund_reason = isset($_POST['refund_reason']) ? $_POST['refund_reason'] : '';
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason);
        return AjaxReturn($retval);
    }

    /**
     * 买家取消退款
     *
     * @return number
     */
    public function orderGoodsCancel()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $this->error("缺少必需参数order_id");
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : $this->error("缺少必需参数order_goods_id");
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsCancel($order_id, $order_goods_id);
        return AjaxReturn($retval);
    }

    /**
     * 买家退货
     *
     * @return Ambigous <number, \think\false, boolean, string>
     */
    public function orderGoodsReturnGoods()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $this->error("缺少必需参数order_id");
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : $this->error("缺少必需参数order_goods_id");
        $refund_shipping_company = $_POST['refund_shipping_company'];
        $refund_shipping_code = $_POST['$refund_shipping_code'];
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsReturnGoods($order_id, $order_goods_id, $refund_shipping_company, $refund_shipping_code);
        return AjaxReturn($retval);
    }

    /**
     * 买家同意买家退款申请
     *
     * @return number
     */
    public function orderGoodsRefundAgree()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $this->error("缺少必需参数order_id");
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : $this->error("缺少必需参数order_goods_id");
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsRefundAgree($order_id, $order_goods_id);
        return AjaxReturn($retval);
    }

    /**
     * 买家永久拒绝本次退款
     *
     * @return Ambigous <number, Exception>
     */
    public function orderGoodsRefuseForever()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $this->error("缺少必需参数order_id");
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : $this->error("缺少必需参数order_goods_id");
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsRefuseForever($order_id, $order_goods_id);
        return AjaxReturn($retval);
    }

    /**
     * 卖家拒绝本次退款
     *
     * @return Ambigous <number, Exception>
     */
    public function orderGoodsRefuseOnce()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $this->error("缺少必需参数order_id");
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : $this->error("缺少必需参数order_goods_id");
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsRefuseOnce($order_id, $order_goods_id);
        return AjaxReturn($retval);
    }

    /**
     * 卖家确认收货
     *
     * @return Ambigous <number, Exception>
     */
    public function orderGoodsConfirmRecieve()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $this->error("缺少必需参数order_id");
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : $this->error("缺少必需参数order_goods_id");
        $storage_num = request()->post("storage_num","");
        $isStorage = request()->post("isStorage","");
        $goods_id = request()->post("goods_id",'');
        $sku_id = request()->post('sku_id','');
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsConfirmRecieve($order_id, $order_goods_id, $storage_num, $isStorage, $goods_id, $sku_id);
        return AjaxReturn($retval);
    }

    /**
     * 卖家确认退款
     *
     * @return Ambigous <Exception, unknown>
     */
    public function orderGoodsConfirmRefund()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $this->error("缺少必需参数order_id");
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : $this->error("缺少必需参数order_goods_id");
        $refund_real_money = isset($_POST['refund_real_money']) ? $_POST['refund_real_money'] : $this->error("缺少必需参数refund_real_money");
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money);
        return AjaxReturn($retval);
    }

    /**
     * 确认退款时，查询买家实际付款金额
     */
    public function orderGoodsRefundMoney()
    {
        $order_service = new OrderService();
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : "";
        $res = 0;
        if ($order_goods_id != '') {
            $res = $order_service->orderGoodsRefundMoney($order_goods_id);
        }
        return $res;
    }

    /**
     * 获取订单销售统计
     */
    public function getOrderAccount()
    {
        $order_service = new OrderService();
        // 获取日销售统计
        $account = $order_service->getShopOrderAccountDetail($this->instance_id);
        var_dump($account);
    }

    /**
     * 退货设置
     * 任鹏强
     */
    public function returnSetting()
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
                "active" => 1
            ),
            array(
                'url' => "shop/pickuppointlist",
                'menu_name' => "自提点管理",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointfreight",
                'menu_name' => "自提点运费菜单",
                "active" => 0
            ),
            array(
                'url' => "config/distributionareamanagement",
                'menu_name' => "货到付款地区管理",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        $order_service = new OrderService();
        $shop_id = $this->instance_id;
        if (request()->isAjax()) {
            $address = request()->post('address', '');
            $real_name = request()->post('real_name', '');
            $mobile = request()->post('mobile', '');
            $zipcode = request()->post('zipcode', '');
            $retval = $order_service->updateShopReturnSet($shop_id, $address, $real_name, $mobile, $zipcode);
            return AjaxReturn($retval);
        } else {
            $info = $order_service->getShopReturnSet($shop_id);
            $this->assign('info', $info);
            return view($this->style . "Order/returnSetting");
        }
    }

    /**
     * 提货
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function pickupOrder()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $this->error("缺少必需参数order_id");
        $buyer_name = isset($_POST['buyer_name']) ? $_POST['buyer_name'] : "";
        $buyer_phone = isset($_POST['buyer_phone']) ? $_POST['buyer_phone'] : "";
        $remark = isset($_POST['remark']) ? $_POST['remark'] : "";
        $order_service = new OrderService();
        $retval = $order_service->pickupOrder($order_id, $buyer_name, $buyer_phone, $remark);
        return AjaxReturn($retval);
    }

    /**
     * 获取物流跟踪信息
     */
    public function getExpressInfo()
    {
        $order_service = new OrderService();
        $order_goods_id = request()->post('order_goods_id');
        $expressinfo = $order_service->getOrderGoodsExpressMessage($order_goods_id);
        return $expressinfo;
    }

    /**
     * 添加备注
     */
    public function addMemo()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id');
        $memo = request()->post('memo');
        $result = $order_service->addOrderSellerMemo($order_id, $memo);
        return AjaxReturn($result);
    }

    /**
     * 获取订单备注信息
     * 
     * @return unknown
     */
    public function getOrderSellerMemo()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id');
        $res = $order_service->getOrderSellerMemo($order_id);
        return $res;
    }
    
  /**
   * 获取修改收货地址的信息
   * @return string
   */
    public function getOrderUpdateAddress()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id');
        $res = $order_service->getOrderReceiveDetail($order_id);
        return $res;
    }
    /**
     * 修改收货地址的信息
     * @return string
     */
    public function updateOrderAddress()
    {
        $order_service = new OrderService();
        $order_id = request()->post('order_id','');
        $receiver_name = request()->post('receiver_name','');
        $receiver_mobile = request()->post('receiver_mobile','');
        $receiver_zip = request()->post('receiver_zip','');
        $receiver_province = request()->post('seleAreaNext','');
        $receiver_city = request()->post('seleAreaThird','');
        $receiver_district = request()->post('seleAreaFouth','');
        $receiver_address = request()->post('address_detail','');
        $res = $order_service->updateOrderReceiveDetail($order_id, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name);
        return $res;
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
     * 导出粉丝列表到excal
     */
    public function testExcel(){    
        //var_dump($list);
        //导出Excel
        $xlsName  = "开门记录列表";
        $xlsCell  = array(
            array('userid','用户id'),
            array('use_name','使用者姓名')           
        );
        $list = array(
            array(
                "userid"=>"55",
                "use_name"=>"王二小"
                ),
            array(
                "userid"=>"56",
                "use_name"=>"王二大"
            )
        );
        //                 dump($list);exit();
        dataExcel($xlsName,$xlsCell,$list);
         
    }
    /**
     * 订单数据excel导出
     */
    public function orderDataExcel(){
        $xlsName  = "订单数据列表";
        $xlsCell  = array(
            array('order_no','订单编号'),
            array('create_date','日期'),
            array('receiver_info','收货人信息'),
            array('order_money','订单金额'),
            array('pay_money','实际支付'),
            array('pay_type_name','支付方式'),
            array('shipping_type_name','配送方式'),
            array('pay_status_name','支付状态'),
            array('status_name','发货状态'),
            array('goods_info','商品信息')
        );
        $start_date = request()->get('start_date') == "" ? 0 : getTimeTurnTimeStamp(request()->get('start_date'));
        $end_date = request()->get('end_date') == "" ? 0 : getTimeTurnTimeStamp(request()->get('end_date'));
        $user_name = request()->get('user_name', '');
        $order_no = request()->get('order_no', '');
        $order_status = request()->get('order_status', '');
        $receiver_mobile = request()->get('receiver_mobile', '');
        $payment_type = request()->get('payment_type', '');
        if($start_date != 0 && $end_date != 0){
            $condition["create_time"] = [
                [
                    ">",
                    $start_date
                ],
                [
                    "<",
                    $end_date
                ]
            ];
        }elseif($start_date != 0 && $end_date == 0){
            $condition["create_time"] = [
                [
                    ">",
                    $start_date
                ]
            ];
        }elseif($start_date == 0 && $end_date != 0){
            $condition["create_time"] = [
                [
                    "<",
                    $end_date
                ]
            ];
        }
        if ($order_status != '') {
            // $order_status 1 待发货
            if ($order_status == 1) {
                // 订单状态为待发货实际为已经支付未完成还未发货的订单
                $condition['shipping_status'] = 0; // 0 待发货
                $condition['pay_status'] = 2; // 2 已支付
                $condition['order_status'] = array(
                    'neq',
                    4
                ); // 4 已完成
                $condition['order_status'] = array(
                    'neq',
                    5
                ); // 5 关闭订单
            } else
                $condition['order_status'] = $order_status;
        }
        if (! empty($payment_type)) {
            $condition['payment_type'] = $payment_type;
        }
        if (! empty($user_name)) {
            $condition['receiver_name'] = $user_name;
        }
        if (! empty($order_no)) {
            $condition['order_no'] = $order_no;
        }
        if (! empty($receiver_mobile)) {
            $condition['receiver_mobile'] = $receiver_mobile;
        }
        $condition['shop_id'] = $this->instance_id;
        $order_service = new OrderService();
        $list = $order_service->getOrderList(1, 0, $condition, 'create_time desc');
        $list = $list["data"];
        foreach($list as $k=>$v){
            $list[$k]["create_date"] = getTimeStampTurnTime($v["create_time"]); //创建时间
            $list[$k]["receiver_info"] = $v["receiver_name"] ."  " . $v["receiver_mobile"]."  " . $v["receiver_province_name"] . $v["receiver_city_name"] . $v["receiver_district_name"] . $v["receiver_address"] ."  " . $v["receiver_zip"]; //创建时间
            if($v['shipping_type'] == 1) {
                $list[$k]["shipping_type_name"] = '商家配送';
            } elseif ($v['shipping_type'] == 2) {
                $list[$k]["shipping_type_name"] = '门店自提';
            } else {
                $list[$k]["shipping_type_name"] = '';
            }
            if($v['pay_status'] == 0) {
                $list[$k]["pay_status_name"] = '待付款';
            } elseif ($v['pay_status'] == 2) {
                $list[$k]["pay_status_name"] = '已付款';
            } elseif($v['pay_status'] == 1) {
                $list[$k]["pay_status_name"] = '支付中';
            }
            $goods_info = "";
            foreach($v["order_item_list"] as $t=>$m){
                $goods_info .="商品名称:".$m["goods_name"]."  规格:".$m["sku_name"]."  商品价格:".$m["price"]."  购买数量:".$m["num"]."  ";
            }
            $list[$k]["goods_info"] = $goods_info;
        }
        dataExcel($xlsName,$xlsCell,$list);
    }
    
    public function getOrderGoodsDetialAjax(){
        if(request()->isAjax()){
            $order_goods_id = request()->post("order_goods_id",'');
            $order_goods = new OrderGoods();
            $res = $order_goods->getOrderGoodsRefundDetail($order_goods_id);
            return $res;
        }
    }
}