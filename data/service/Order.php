<?php

namespace data\service;

/**
 * 订单
 */
use data\api\IOrder as IOrder;
use data\extend\Kdniao;
use data\model\AlbumPictureModel;
use data\model\CityModel;
use data\model\DistrictModel;
use data\model\NsCartModel;
use data\model\NsGoodsEvaluateModel;
use data\model\NsGoodsModel;
use data\model\NsOrderExpressCompanyModel;
use data\model\NsOrderGoodsExpressModel;
use data\model\NsOrderGoodsModel;
use data\model\NsGoodsSkuModel;
use data\model\NsOrderModel;
use data\model\NsOrderShopReturnModel;
use data\model\NsShopModel;
use data\model\ProvinceModel;
use data\model\NsGoodsCommentModel;
use data\service\BaseService;
use data\service\GoodsCalculate\GoodsCalculate;
use data\service\NfxCommissionCalculate;
use data\service\NfxUser;
use data\service\niubusiness\NbsBusinessAssistantAccount;
use data\service\Order\Order as OrderBusiness;
use data\service\Order\OrderAccount;
use data\service\Order\OrderExpress;
use data\service\Order\OrderGoods;
use data\service\Order\OrderStatus;
use data\service\promotion\GoodsExpress;
use data\service\promotion\GoodsPreference;
use data\service\shopaccount\ShopAccount;
use data\service\promotion\PromoteRewardRule;
use think\Log;
use data\model\BaseModel;

class Order extends BaseService implements IOrder
{

    function __construct()
    {
        parent::__construct();
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::getOrderDetail()
     */
    public function getOrderDetail($order_id)
    {
        // 查询主表信息
        $order = new OrderBusiness();
        $detail = $order->getDetail($order_id);
        if(empty($detail)){
            return array();
        }
        $detail['pay_status_name'] = $this->getPayStatusInfo($detail['pay_status'])['status_name'];
        $detail['shipping_status_name'] = $this->getShippingInfo($detail['shipping_status'])['status_name'];
        
        $express_list = $this->getOrderGoodsExpressList($order_id);
        // 未发货的订单项
        $order_goods_list = array();
        // 已发货的订单项
        $order_goods_delive = array();
        // 没有配送信息的订单项
        $order_goods_exprss = array();
        foreach ($detail["order_goods"] as $order_goods_obj) {
            $shipping_status = $order_goods_obj["shipping_status"];
            if ($shipping_status == 0) {
                // 未发货
                $order_goods_list[] = $order_goods_obj;
            } else {
                $order_goods_delive[] = $order_goods_obj;
            }
        }
        $detail["order_goods_no_delive"] = $order_goods_list;
        // 没有配送信息的订单项
        if (! empty($order_goods_delive) && count($order_goods_delive) > 0) {
            foreach ($order_goods_delive as $goods_obj) {
                $is_have = false;
                $order_goods_id = $goods_obj["order_goods_id"];
                foreach ($express_list as $express_obj) {
                    $order_goods_id_array = $express_obj["order_goods_id_array"];
                    $goods_id_str = explode(",", $order_goods_id_array);
                    if (in_array($order_goods_id, $goods_id_str)) {
                        $is_have = true;
                    }
                }
                if (! $is_have) {
                    $order_goods_exprss[] = $goods_obj;
                }
            }
        }
        $goods_packet_list = array();
        if (count($order_goods_exprss) > 0) {
            $packet_obj = array(
                "packet_name" => "无需物流",
                "express_name" => "",
                "express_code" => "",
                "express_id" => 0,
                "is_express" => 0,
                "order_goods_list" => $order_goods_exprss
            );
            $goods_packet_list[] = $packet_obj;
        }
        if (! empty($express_list) && count($express_list) > 0 && count($order_goods_delive) > 0) {
            $packet_num = 1;
            foreach ($express_list as $express_obj) {
                $packet_goods_list = array();
                $order_goods_id_array = $express_obj["order_goods_id_array"];
                $goods_id_str = explode(",", $order_goods_id_array);
                foreach ($order_goods_delive as $delive_obj) {
                    $order_goods_id = $delive_obj["order_goods_id"];
                    if (in_array($order_goods_id, $goods_id_str)) {
                        $packet_goods_list[] = $delive_obj;
                    }
                }
                $packet_obj = array(
                    "packet_name" => "包裹  + " . $packet_num,
                    "express_name" => $express_obj["express_name"],
                    "express_code" => $express_obj["express_no"],
                    "express_id" => $express_obj["id"],
                    "is_express" => 1,
                    "order_goods_list" => $packet_goods_list
                );
                $packet_num = $packet_num + 1;
                $goods_packet_list[] = $packet_obj;
            }
        }
        $detail["goods_packet_list"] = $goods_packet_list;
        return $detail;
        // TODO Auto-generated method stub
    }

    /**
     * 获取订单基础信息
     *
     * @param unknown $order_id            
     */
    public function getOrderInfo($order_id)
    {
        $order_model = new NsOrderModel();
        $order_info = $order_model->get($order_id);
        return $order_info;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::getOrderList()
     */
    public function getOrderList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $order_model = new NsOrderModel();
        // 查询主表
        $order_list = $order_model->pageQuery($page_index, $page_size, $condition, $order, '*');
        
        if (! empty($order_list['data'])) {
            foreach ($order_list['data'] as $k => $v) {
                // 查询订单项表
                var_dump($v);exit;
                $order_item = new NsOrderGoodsModel();
                $order_item_list = $order_item->where([
                    'order_id' => $v['order_id']
                ])->select();
                //通过sku_id查询ns_goods_sku中code
                foreach($order_item_list as $key=>$val){
                    //查询商品sku表开始
                    $goods_sku = new NsGoodsSkuModel();
                    $goods_sku_info = $goods_sku->getInfo(['sku_id'=>$val['sku_id']],'code');
                    $order_item_list[$key]['code'] = $goods_sku_info['code'];
                    //查询商品sku结束
                }
                
                $province_name = "";
                $city_name = "";
                $district_name = "";
                
                $province = new ProvinceModel();
                $province_info = $province->getInfo(array(
                    "province_id" => $v["receiver_province"]
                ), "*");
                if (count($province_info) > 0) {
                    $province_name = $province_info["province_name"];
                }
                $order_list['data'][$k]['receiver_province_name'] = $province_name;
                $city = new CityModel();
                $city_info = $city->getInfo(array(
                    "city_id" => $v["receiver_city"]
                ), "*");
                if (count($city_info) > 0) {
                    $city_name = $city_info["city_name"];
                }
                $order_list['data'][$k]['receiver_city_name'] = $city_name;
                $district = new DistrictModel();
                $district_info = $district->getInfo(array(
                    "district_id" => $v["receiver_district"]
                ), "*");
                if (count($district_info) > 0) {
                    $district_name = $district_info["district_name"];
                }
                $order_list['data'][$k]['receiver_district_name'] = $district_name;
                foreach ($order_item_list as $key_item => $v_item) {
                    
                    
                    $picture = new AlbumPictureModel();
                    // $order_item_list[$key_item]['picture'] = $picture->get($v_item['goods_picture']);
                    $goods_picture = $picture->get($v_item['goods_picture']);
                    if (empty($goods_picture)) {
                        $goods_picture = array(
                            'pic_cover' => '',
                            'pic_cover_big' => '',
                            'pic_cover_mid' => '',
                            'pic_cover_small' => '',
                            'pic_cover_micro' => '',
                            "upload_type"=>1,
                            "domain"=>""
                        );
                    }
                    $order_item_list[$key_item]['picture'] = $goods_picture;
                    if ($v_item['refund_status'] != 0) {
                        $order_refund_status = OrderStatus::getRefundStatus();
                        foreach ($order_refund_status as $k_status => $v_status) {
                            
                            if ($v_status['status_id'] == $v_item['refund_status']) {
                                $order_item_list[$key_item]['refund_operation'] = $v_status['refund_operation'];
                                $order_item_list[$key_item]['status_name'] = $v_status['status_name'];
                            }
                        }
                    } else {
                        $order_item_list[$key_item]['refund_operation'] = '';
                        $order_item_list[$key_item]['status_name'] = '';
                    }
                }
                $order_list['data'][$k]['order_item_list'] = $order_item_list;
                $order_list['data'][$k]['operation'] = '';
                // 订单来源名称
                $order_list['data'][$k]['order_from_name'] = OrderStatus::getOrderFrom($v['order_from']);
                $order_list['data'][$k]['pay_type_name'] = OrderStatus::getPayType($v['payment_type']);
                // 根据订单类型判断订单相关操作
                if ($order_list['data'][$k]['payment_type'] == 6 || $order_list['data'][$k]['shipping_type'] == 2) {
                    $order_status = OrderStatus::getSinceOrderStatus();
                } else {
                    $order_status = OrderStatus::getOrderCommonStatus();
                }
                
                // 查询订单操作
                foreach ($order_status as $k_status => $v_status) {
                    
                    if ($v_status['status_id'] == $v['order_status']) {
                        $order_list['data'][$k]['operation'] = $v_status['operation'];
                        $order_list['data'][$k]['member_operation'] = $v_status['member_operation'];
                        $order_list['data'][$k]['status_name'] = $v_status['status_name'];
                        $order_list['data'][$k]['is_refund'] = $v_status['is_refund'];
                    }
                }
            }
        }
        return $order_list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderCreate()
     */
    public function orderCreate($order_type, $out_trade_no, $pay_type, $shipping_type, $order_from, $buyer_ip, $buyer_message, $buyer_invoice, $shipping_time, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name, $point, $coupon_id, $user_money, $goods_sku_list, $platform_money, $pick_up_id, $shipping_company_id, $coin = 0)
    {
        $order = new OrderBusiness();
        if ($pay_type == 4) {
            // 如果是货到付款 判断当前地址是否符合货到付款的地址
            $address = new Address();
            $result = $address->getDistributionAreaIsUser(0, $receiver_province, $receiver_city, $receiver_district);
            if (! $result) {
                return ORDER_CASH_DELIVERY;
            }
        }
        $retval = $order->orderCreate($order_type, $out_trade_no, $pay_type, $shipping_type, $order_from, $buyer_ip, $buyer_message, $buyer_invoice, $shipping_time, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name, $point, $coupon_id, $user_money, $goods_sku_list, $platform_money, $pick_up_id, $shipping_company_id, $coin);
        runhook("Notify", "orderCreate", array(
            "order_id" => $retval
        ));
        //针对特殊订单执行支付处理
        if($retval > 0)
        {
            hook('orderCreateSuccess', ['order_id' => $retval]);
            //货到付款
            if($pay_type == 4)
            {
                $this->orderOnLinePay($out_trade_no, 4);
            }else{
                $order_model = new NsOrderModel();
                $order_info = $order_model->getInfo(['order_id' => $retval], '*');
                if(!empty($order_info))
                {
                    if($order_info['user_platform_money'] != 0)
                    {
                        if($order_info['pay_money'] == 0)
                        {
                            $this->orderOnLinePay($out_trade_no, 5);

                        }
                    }else{
                    
                        if($order_info['pay_money'] == 0)
                        {
                            $this->orderOnLinePay($out_trade_no, 1);//默认微信支付
                        }
                    }
                }
               
            }
        
        }
        
        return $retval;
        // TODO Auto-generated method stub
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getOrderTradeNo()
     */
    public function getOrderTradeNo()
    {
        $order = new OrderBusiness();
        $no = $order->createOutTradeNo();
        return $no;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderDelivery()
     */
    public function orderDelivery($order_id, $order_goods_id_array, $express_name, $shipping_type, $express_company_id, $express_no)
    {
        $order_express = new OrderExpress();
        $retval = $order_express->delivey($order_id, $order_goods_id_array, $express_name, $shipping_type, $express_company_id, $express_no);
        runhook("Notify", "orderDelivery", array(
            "order_goods_ids" => $order_goods_id_array
        ));
        if($retval){
            $params = [
                'order_id' => $order_id,
                'order_goods_id_array' => $order_goods_id_array,
                'express_name' => $express_name,
                'shipping_type' => $shipping_type,
                'express_company_id' => $express_company_id,
                'express_no' => $express_no,
            ];
            hook('orderDeliverySuccess', $params);
        }
        return $retval;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsDelivery()
     */
    public function orderGoodsDelivery($order_id, $order_goods_id_array)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsDelivery($order_id, $order_goods_id_array);
        if($retval){
            $params = [
                'order_id' => $order_id,
                'order_goods_id_array' => $order_goods_id_array,
            ];
            hook('orderDeliverySuccess', $params);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderClose()
     */
    public function orderClose($order_id)
    {
        $order = new OrderBusiness();
        $retval = $order->orderClose($order_id);
        if($retval){
            hook("orderCloseSuccess", ['order_id' => $order_id]);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * 订单完成的函数
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderComplete()
     */
    public function orderComplete($orderid)
    {
        $order = new OrderBusiness();
        $retval = $order->orderComplete($orderid);
        try {
            // 结算订单的分销佣金
            $this->updateOrderCommission($orderid);
            // 处理店铺的账户资金
            $this->dealShopAccount_OrderComplete("", $orderid);
            // 处理平台的账户资金
            $this->updateAccountOrderComplete($orderid);
            //更新会员的等级
            $user_service=new User();
            $order_model=new NsOrderModel();
            $order_detail=$order_model->getInfo(["order_id"=>$orderid], "shop_id, buyer_id");
            $user_service->updateUserLevel($order_detail["shop_id"], $order_detail["buyer_id"]);
            
            runhook("Notify", "orderComplete", array(
                "order_id" => $orderid
            ));
        } catch (\Exception $e) {
            Log::write($e->getMessage());
        }
        if($retval){
            hook("orderComplateSuccess", ['order_id' => $orderid]);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * 订单在线支付
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderOnLinePay()
     */
    public function orderOnLinePay($order_pay_no, $pay_type)
    {
        $order = new OrderBusiness();
        $retval = $order->OrderPay($order_pay_no, $pay_type, 0);
        try {
            if ($retval > 0) {
                // 计算店铺内部的分销佣金
                $this->orderCommissionCalculate($order_pay_no);
                // 处理店铺的账户资金
                $this->dealShopAccount_OrderPay($order_pay_no);
                // 处理平台的资金账户
                $this->dealPlatformAccountOrderPay($order_pay_no);
            
                $order_model = new NsOrderModel();
                $condition = " out_trade_no=" . $order_pay_no;
                $order_list = $order_model->getQuery($condition, "order_id", "");
                foreach ($order_list as $k => $v) {
                    runhook("Notify", "orderPay", array(
                        "order_id" => $v["order_id"]
                    ));
                    // 判断是否需要在本阶段赠送积分
                    $order = new OrderBusiness();
                    $res = $order->giveGoodsOrderPoint($v["order_id"], 3);
                }
            }    
        } catch (\Exception $e) {
            
            Log::write($e->getMessage());
        }
        if($retval){
            $pay_type_name = OrderStatus::getPayType($pay_type);
            hook('orderOnLinePaySuccess', ['order_pay_no' => $order_pay_no]);
        }
        return $retval;
    }

    /*
     * 订单线下支付
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderOffLinePay()
     */
    public function orderOffLinePay($order_id, $pay_type, $status)
    {
        $order = new OrderBusiness();
        
        $new_no = $this->getOrderNewOutTradeNo($order_id);
        if ($new_no) {
            $retval = $order->OrderPay($new_no, $pay_type, $status);
            if ($retval > 0) {
                $pay = new UnifyPay();
                $pay->offLinePay($new_no, $pay_type);
                // 计算店铺的佣金情况
                $this->orderCommissionCalculate('', $order_id);
                // 处理店铺的账户资金
                $this->dealShopAccount_OrderPay('', $order_id);
                // 处理平台的资金账户
                $this->dealPlatformAccountOrderPay('', $order_id);
                // 判断是否需要在本阶段赠送积分
                $order = new OrderBusiness();
                $res = $order->giveGoodsOrderPoint($order_id, 3);
                $pay_type_name = OrderStatus::getPayType($pay_type);
                hook('orderOffLinePaySuccess', ['order_id' => $order_id]);
            }
            return $retval;
        } else {
            return 0;
        }
        // TODO Auto-generated method stub
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getOrderNewOutTradeNo()
     */
    public function getOrderNewOutTradeNo($order_id)
    {
        $order_model = new NsOrderModel();
        $out_trade_no = $order_model->getInfo([
            'order_id' => $order_id
        ], 'out_trade_no');
        $order = new OrderBusiness();
        $new_no = $order->createNewOutTradeNo($order_id);
        $pay = new UnifyPay();
        $pay->modifyNo($out_trade_no['out_trade_no'], $new_no);
        return $new_no;
    }

    /**
     * 订单调整金额(non-PHPdoc)
     *
     * @see \data\api\IOrder::orderMoneyAdjust()
     */
    public function orderMoneyAdjust($order_id, $order_goods_id_adjust_array, $shipping_fee)
    {
        // 调整订单
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsAdjustMoney($order_goods_id_adjust_array);
        
        if ($retval >= 0) {
            // 计算整体商品调整金额
            $new_no = $this->getOrderNewOutTradeNo($order_id);
            $order = new OrderBusiness();
            $order_goods_money = $order->getOrderGoodsMoney($order_id);
            $retval_order = $order->orderAdjustMoney($order_id, $order_goods_money, $shipping_fee);
            $order_model = new NsOrderModel();
            $order_money = $order_model->getInfo([
                'order_id' => $order_id
            ], 'pay_money');
            $pay = new UnifyPay();
            $pay->modifyPayMoney($new_no, $order_money['pay_money']);
            hook("orderMoneyAdjustSuccess", ['order_id' => $order_id, 'order_goods_id_adjust_array' => $order_goods_id_adjust_array, 'shipping_fee' => $shipping_fee]);
            return $retval_order;
        } else {
            return $retval;
        }
    }

    /**
     * 查询订单
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::orderQuery()
     */
    public function orderQuery($where = "", $field = "*")
    {
        $order = new OrderBusiness();
        return $order->where($where)
            ->field($field)
            ->select();
    }

    /**
     * 查询订单项退款信息(non-PHPdoc)
     *
     * @see \data\api\IOrder::getOrderGoodsRefundInfo()
     */
    public function getOrderGoodsRefundInfo($order_goods_id)
    {
        $order_goods = new OrderGoods();
        $order_goods_info = $order_goods->getOrderGoodsRefundDetail($order_goods_id);
        return $order_goods_info;
    }

    public function test()
    {
        // $res = $order_express->getSkuGroup('466:1,467:2,468:2,462:1');
        // $res = $order_express->getGoodsShippingExpressFee(1, 5, 1, 22);
        // $res = $order_express->getSkuListExpressFee('466:1,467:2,468:2,462:1', 1, 22);
        // $res = $order_express->getGoodsSkuListPrice('466:1,467:2,468:2,462:1');
        /*
         * $order_goods = new OrderGoods();
         * $res = $order_goods->addOrderGoods(12, '466:1,467:2,468:2');
         */
        // 订单创建
        $order = new OrderBusiness();
        $res = $order->orderCreate(1, '201611208115651112', 4, 1, 1, '127.0.0.1', '', '', '0000-0-0', '15234151502', 4, 14, 209, '小店', '', '测试', 0, 527, 0, '1145:2', 0, 0);
        return $res;
        
        /*
         * //订单发货
         * $order_express = new OrderExpress();
         * $res = $order_express->delivey(3, '16,17', "测试包裹", 1, 1, 'as0000100');
         */
        /*
         * $order = new OrderBusiness();
         * $res = $order->addOrderAction(3, 1, '订单发货');
         * /* //订单关闭
         * $order = new OrderBusiness();
         * $res = $order->orderClose(4);
         * return $res;
         */
        /*
         * //线下支付
         * $res = $this->orderOffLinePay(4, 1, 0);
         * return $res;
         */
        /*
         * //线上支付
         * $res = $this->orderOnLinePay('1481177387568', 2);
         * return $res;
         */
        /*
         * $order_goods = new OrderGoods();
         * $res = $order_goods->orderGoodsRefundAskfor(71, 162, 1, 1, '不想买');
         * return $res;
         */
        // 调整金额
        /*
         * $res = $this->orderMoneyAdjust(24, '52,-20;53,-20', 5);
         * return $res;
         */
        /*
         * //计算退款实际可退金额
         * $order_goods = new OrderGoods();
         * $res = $order_goods->orderGoodsRefundMoney(37);
         * return $res;
         */
        /*
         * $order = new GoodsPreference();
         * $order->
         */
        /*
         * $goods_mansong = new GoodsMansong();
         * $mansong_array = $goods_mansong->getGoodsSkuListMansong('443:2,445:5,467:2');
         * return $mansong_array;
         */
    }

    /**
     * 查询订单的订单项列表
     *
     * @param unknown $order_id            
     */
    public function getOrderGoods($order_id)
    {
        $order = new OrderBusiness();
        return $order->getOrderGoods($order_id);
    }

    /**
     * 查询订单的订单项列表
     *
     * @param unknown $order_id            
     */
    public function getOrderGoodsInfo($order_goods_id)
    {
        $order = new OrderBusiness();
        $picture = new AlbumPictureModel();
        $order_goods_info = $order->getOrderGoodsInfo($order_goods_id);
        $order_goods_info['goods_picture'] = $picture->get($order_goods_info['goods_picture'])['pic_cover'];
        return $order_goods_info;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::addOrder()
     */
    public function addOrder($data)
    {
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsRefundAskfor()
     */
    public function orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason);
        if($retval){
            $params = [
                'order_id' => $order_id,
                'order_goods_id' => $order_goods_id,
                'refund_type' => $refund_type,
                'refund_require_money' => $refund_require_money,
                'refund_reason' => $refund_reason,
            ];
            hook('orderGoodsRefundAskforSuccess', $params);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsCancel()
     */
    public function orderGoodsCancel($order_id, $order_goods_id)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsCancel($order_id, $order_goods_id);
        if($retval){
            hook("orderGoodsCancelSuccess", ['order_id' => $order_id, 'order_goods_id' => $order_goods_id]);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsReturnGoods()
     */
    public function orderGoodsReturnGoods($order_id, $order_goods_id, $refund_shipping_company, $refund_shipping_code)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsReturnGoods($order_id, $order_goods_id, $refund_shipping_company, $refund_shipping_code);
        if($retval){
            $params = [
                'order_id' => $order_id,
                'order_goods_id' => $order_goods_id,
                'refund_shipping_company' => $refund_shipping_company,
                'refund_shipping_code' => $refund_shipping_code,
            ];
            hook("orderGoodsReturnGoodsSuccess", $params);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsRefundAgree()
     */
    public function orderGoodsRefundAgree($order_id, $order_goods_id)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsRefundAgree($order_id, $order_goods_id);
        if($retval){
            hook("orderGoodsRefundAgreeSuccess", ['order_id' => $order_id, 'order_goods_id' => $order_goods_id]);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsRefuseForever()
     */
    public function orderGoodsRefuseForever($order_id, $order_goods_id)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsRefuseForever($order_id, $order_goods_id);
        if($retval){
            hook("orderGoodsRefuseForeverSuccess", ['order_id' => $order_id, 'order_goods_id' => $order_goods_id]);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsRefuseOnce()
     */
    public function orderGoodsRefuseOnce($order_id, $order_goods_id)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsRefuseOnce($order_id, $order_goods_id);
        if($retval){
            hook("orderGoodsRefuseOnceSuccess", ['order_id' => $order_id, 'order_goods_id' => $order_goods_id]);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsConfirmRecieve()
     */
    public function orderGoodsConfirmRecieve($order_id, $order_goods_id, $storage_num, $isStorage, $goods_id, $sku_id)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsConfirmRecieve($order_id, $order_goods_id, $storage_num, $isStorage, $goods_id, $sku_id);
        if($retval){
            hook("orderGoodsConfirmRecieveSuccess", ['order_id' => $order_id, 'order_goods_id' => $order_goods_id]);
        }
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsConfirmRefund()
     */
    public function orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money);
        // 重新计算订单的佣金情况
        $this->updateCommissionMoney($order_id, $order_goods_id);
        
        // 计算店铺的账户
        $this->updateShopAccount_OrderRefund($order_goods_id);
        $this->updateShopAccount_OrderComplete($order_id);
        // 计算平台的账户
        $this->updateAccountOrderRefund($order_goods_id);
        $this->updateAccountOrderComplete($order_id);
        if($retval){
            hook("orderGoodsConfirmRefundSuccess", ['order_id' => $order_id, 'order_goods_id' => $order_goods_id, 'refund_real_money' => $refund_real_money]);
        }
        return $retval;
    }

    /**
     * 获取对应sku列表价格
     *
     * @param unknown $goods_sku_list            
     */
    public function getGoodsSkuListPrice($goods_sku_list)
    {
        $goods_preference = new GoodsPreference();
        $money = $goods_preference->getGoodsSkuListPrice($goods_sku_list);
        return $money;
    }
    public function _getGoodsSkuListPrice($goods_sku_list)
    {
        $goods_array = explode(',', $goods_sku_list);
        foreach ($goods_array as $key => $value) {
            $sku = explode(':', $value);
            $sql .= "(SELECT 单价1 FROM jc_cpwhb where `产品编号` = '$sku[0]') * $sku[1] + ";
        }
        $sql = "SELECT ".substr($sql, 0,-3). " as sum";
        $goods_preference = new NsGoodsModel();
        $money = $goods_preference->query($sql);
        return $money;
    }
    /**
     * 获取邮费
     *
     * @param unknown $goods_sku_list            
     * @param unknown $province            
     * @param unknown $city            
     * @return Ambigous <unknown, number>
     */
    public function getExpressFee($goods_sku_list, $express_company_id, $province, $city, $district)
    {
        $goods_express = new GoodsExpress();
        $fee = $goods_express->getSkuListExpressFee($goods_sku_list, $express_company_id, $province, $city, $district);
        return $fee;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::orderGoodsRefundMoney()
     */
    public function orderGoodsRefundMoney($order_goods_id)
    {
        $order_goods = new OrderGoods();
        $money = $order_goods->orderGoodsRefundMoney($order_goods_id);
        return $money;
    }

    /**
     * 获取用户可使用优惠券
     *
     * @param unknown $goods_sku_list            
     */
    public function getMemberCouponList($goods_sku_list)
    {
        $goods_preference = new GoodsPreference();
        $coupon_list = $goods_preference->getMemberCouponList($goods_sku_list);
        return $coupon_list;
    }

    /**
     * 查询商品列表可用积分数
     *
     * @param unknown $goods_sku_list            
     */
    public function getGoodsSkuListUsePoint($goods_sku_list)
    {
        $point = 0;
        $goods_sku_list_array = explode(",", $goods_sku_list);
        foreach ($goods_sku_list_array as $k => $v) {
            
            $sku_data = explode(':', $v);
            $sku_id = $sku_data[0];
            $goods = new Goods();
            $goods_id = $goods->getGoodsId($sku_id);
            $goods_model = new NsGoodsModel();
            $point_use = $goods_model->getInfo([
                'goods_id' => $goods_id
            ], 'point_exchange_type,point_exchange');
            if ($point_use['point_exchange_type'] == 1) {
                $point += $point_use['point_exchange'];
            }
        }
        return $point;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::OrderTakeDelivery()
     */
    public function OrderTakeDelivery($order_id)
    {
        $order = new OrderBusiness();
        $res = $order->OrderTakeDelivery($order_id);
        if($res){
            hook("orderTakeDeliverySuccess", ['order_id' => $order_id]);
        }
        return $res;
    }

    /**
     * 删除购物车中的数据
     * 修改时间：2017年5月26日 14:35:38 王永杰
     * 首先要查询当前商品在购物车中的数量，如果商品数量等于1则删除，如果商品数量大于1个，则减少该商品的数量
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::deleteCart()
     */
    public function deleteCart($goods_sku_list, $uid)
    {
        $cart = new NsCartModel();
        $goods_sku_list_array = explode(",", $goods_sku_list);
        foreach ($goods_sku_list_array as $k => $v) {
            $sku_data = explode(':', $v);
            $sku_id = $sku_data[0];
            $info = $cart->getInfo([
                'buyer_id' => $uid,
                'sku_id' => $sku_id
            ], "num,cart_id");
//             $num = $info['num'];
            $cart_id = $info['cart_id'];
            $cart->destroy([
                'buyer_id' => $uid,
                'sku_id' => $sku_id
            ]);
//             if ($num == 1) {
//                 // 购物车中该商品数量为1的话就删除
//             } else {
//                 // 修改商品数量
//                 $data["num"] = $num - 1;
//                 $cart->update($data, [
//                     'cart_id' => $cart_id
//                 ]);
//             }
        }
        $_SESSION["user_cart"] = '';
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::getOrderCount()
     */
    public function getOrderCount($condition)
    {
        $order = new NsOrderModel();
        $count = $order->where($condition)->count();
        return $count;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::getPayMoneySum()
     */
    public function getPayMoneySum($condition)
    {
        $order_model = new NsOrderModel();
        $money_sum = $order_model->where($condition)->sum('pay_money');
        return $money_sum;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::getGoodsNumSum()
     */
    public function getGoodsNumSum($condition)
    {
        $order_model = new NsOrderModel();
        $order_list = $order_model->where($condition)
            ->select();
        $goods_sum = 0;
        foreach ($order_list as $k => $v) {
            $order_goods = new NsOrderGoodsModel();
            $goods_sum += $order_goods->where([
                'order_id' => $v['order_id']
            ])->sum('num');
        }
        return $goods_sum;
    }

    /**
     * 获取具体配送状态信息
     *
     * @param unknown $shipping_status_id            
     * @return Ambigous <NULL, multitype:string >
     */
    public static function getShippingInfo($shipping_status_id)
    {
        $shipping_status = OrderStatus::getShippingStatus();
        $info = null;
        foreach ($shipping_status as $shipping_info) {
            if ($shipping_status_id == $shipping_info['shipping_status']) {
                $info = $shipping_info;
                break;
            }
        }
        return $info;
    }

    /**
     * 获取具体支付状态信息
     *
     * @param unknown $pay_status_id            
     * @return multitype:multitype:string |string
     */
    public static function getPayStatusInfo($pay_status_id)
    {
        $pay_status = OrderStatus::getPayStatus();
        $info = null;
        foreach ($pay_status as $pay_info) {
            if ($pay_status_id == $pay_info['pay_status']) {
                $info = $pay_info;
                break;
            }
        }
        return $info;
    }

    /**
     * 获取订单各状态数量
     */
    public static function getOrderStatusNum($condition = '')
    {
        $order = new NsOrderModel();
        $orderStatusNum['all'] = $order->where($condition)->count(); // 全部
        $condition['order_status'] = 0; // 待付款
        $orderStatusNum['wait_pay'] = $order->where($condition)->count();
        $condition['order_status'] = 1; // 待发货
        $orderStatusNum['wait_delivery'] = $order->where($condition)->count();
        $condition['order_status'] = 2; // 待收货
        $orderStatusNum['wait_recieved'] = $order->where($condition)->count();
        $condition['order_status'] = 3; // 已收货
        $orderStatusNum['recieved'] = $order->where($condition)->count();
        $condition['order_status'] = 4; // 交易成功
        $orderStatusNum['success'] = $order->where($condition)->count();
        $condition['order_status'] = 5; // 已关闭
        $orderStatusNum['closed'] = $order->where($condition)->count();
        $condition['order_status'] = - 1; // 退款中
        $orderStatusNum['refunding'] = $order->where($condition)->count();
        $condition['order_status'] = - 2; // 已退款
        $orderStatusNum['refunded'] = $order->where($condition)->count();
        $condition['order_status'] = array(
            'in',
            '3,4'
        ); // 已收货
        $condition['is_evaluate'] = 0; // 未评价
        $orderStatusNum['wait_evaluate'] = $order->where($condition)->count(); // 待评价
        
        return $orderStatusNum;
    }

    /**
     * 商品评价-添加
     *
     * @param unknown $dataList
     *            评价内容的 数组
     * @return Ambigous <multitype:, \think\false>
     */
    public function addGoodsEvaluate($dataArr, $order_id)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $goods = new NsGoodsModel();
        $res = $goodsEvaluate->saveAll($dataArr);
        $result = false;
        
        if ($res != false) {
            // 修改订单评价状态
            $order = new NsOrderModel();
            $data = array(
                'is_evaluate' => 1
            );
            $result = $order->save($data, [
                'order_id' => $order_id
            ]);
            
            $this->commentPoint($order_id);

        }
        foreach ($dataArr as $item) {
            $good_info = $goods->get($item['goods_id']);
            $evaluates = $good_info['evaluates'] + 1;
            $star = $good_info['star'] + $item['scores'];
            $match_point = $star / $evaluates;
            $match_ratio = $match_point / 5 * 100 + '%';
            $data = array(
                'evaluates' => $evaluates,
                'star' => $star,
                'match_point' => $match_point,
                'match_ratio' => $match_ratio
            );
            $goods->update($data, [
                'goods_id' => $item['goods_id']
            ]);
        }
        hook("goodsEvaluateSuccess", ['order_id' => $order_id, 'data' => $dataArr]);
        return $result;
    }

    /**
     * 商品评价-回复
     *
     * @param unknown $explain_first
     *            评价内容
     * @param unknown $ordergoodsid
     *            订单项ID
     * @return Ambigous <number, \think\false>
     */
    public function addGoodsEvaluateExplain($explain_first, $order_goods_id)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $data = array(
            'explain_first' => $explain_first
        );
        $res = $goodsEvaluate->save($data, [
            'order_goods_id' => $order_goods_id
        ]);
        hook("goodsEvaluateExplainSuccess", ['order_goods_id' => $order_goods_id, 'explain_first' => $explain_first]);
        return $res;
    }

    /**
     * 商品评价-追评
     *
     * @param unknown $again_content
     *            追评内容
     * @param unknown $againImageList
     *            传入追评图片的 数组
     * @param unknown $ordergoodsid
     *            订单项ID
     * @return Ambigous <number, \think\false>
     */
    public function addGoodsEvaluateAgain($again_content, $againImageList, $order_goods_id)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $data = array(
            'again_content' => $again_content,
            'again_addtime' => time(),
            'again_image' => $againImageList
        );
        $res = $goodsEvaluate->save($data, [
            'order_goods_id' => $order_goods_id
        ]);
        hook("goodsEvaluateAgainSuccess", ['again_content' => $again_content, 'againImageList' => $againImageList, 'order_goods_id' => $order_goods_id]);
        return $res;
    }

    /**
     * 商品评价-追评回复
     *
     * @param unknown $again_explain
     *            追评的 回复内容
     * @param unknown $ordergoodsid
     *            订单项ID
     * @return Ambigous <number, \think\false>
     */
    public function addGoodsEvaluateAgainExplain($again_explain, $order_goods_id)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $data = array(
            'again_explain' => $again_explain
        );
        $res = $goodsEvaluate->save($data, [
            'order_goods_id' => $order_goods_id
        ]);
        hook("goodsEvaluateAgainExplainSuccess", ['order_goods_id' => $order_goods_id, 'again_explain' => $again_explain]);
        return $res;
    }

    /**
     * 获取指定订单的评价信息
     *
     * @param unknown $orderid
     *            订单ID
     */
    public function getOrderEvaluateByOrder($order_id)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $condition['order_id'] = $order_id;
        $field = 'order_id, order_no, order_goods_id, goods_id, goods_name, goods_price, goods_image, shop_id, shop_name, content, addtime, image, explain_first, member_name, uid, is_anonymous, scores, again_content, again_addtime, again_image, again_explain';
        return $goodsEvaluate->getQuery($condition, $field, 'order_goods_id ASC');
    }

    /**
     * 获取指定会员的评价信息
     *
     * @param unknown $uid
     *            会员ID
     */
    public function getOrderEvaluateByMember($uid)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $condition['uid'] = $uid;
        $field = 'order_id, order_no, order_goods_id, goods_id, goods_name, goods_price, goods_image, shop_id, shop_name, content, addtime, image, explain_first, member_name, uid, is_anonymous, scores, again_content, again_addtime, again_image, again_explain';
        return $goodsEvaluate->getQuery($condition, $field, 'order_goods_id ASC');
    }

    /**
     * 评价信息 分页
     *
     * @param unknown $page_index            
     * @param unknown $page_size            
     * @param unknown $condition            
     * @param unknown $order            
     * @return number
     */
    public function getOrderEvaluateDataList($page_index, $page_size, $condition, $order)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        return $goodsEvaluate->pageQuery($page_index, $page_size, $condition, $order, "*");
    }

    /**
     * 获取评价列表
     *
     * @param unknown $page_index
     *            页码
     * @param unknown $page_size
     *            页大小
     * @param unknown $condition
     *            条件
     * @param unknown $order
     *            排序
     * @return multitype:number unknown
     */
    public function getOrderEvaluateList($page_index, $page_size, $condition, $order)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $field = 'order_id, order_no, order_goods_id, goods_id, goods_name, goods_price, goods_image, shop_id, shop_name, content, addtime, image, explain_first, member_name, uid, is_anonymous, scores, again_content, again_addtime, again_image, again_explain';
        return $goodsEvaluate->pageQuery($page_index, $page_size, $condition, $order, $field);
    }

    /**
     * 修改订单数据
     *
     * @param unknown $order_id            
     * @param unknown $data            
     */
    public function modifyOrderInfo($data, $order_id)
    {
        $order = new NsOrderModel();
        return $order->save($data, [
            'order_id' => $order_id
        ]);
    }

    /**
     * 判断店铺类型
     *
     * @param unknown $shop_id            
     */
    private function getShopTypeDetail($shop_id)
    {
        $shop_model = new NsShopModel();
        $shop_detail = $shop_model->get($shop_id);
        if (empty($shop_detail)) {
            return 0;
        } else {
            return $shop_detail["shop_type"];
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopOrderAccountList()
     */
    public function getShopOrderAccountList($shop_id, $start_time, $end_time, $page_index, $page_size)
    {
        $order_account = new OrderAccount();
        $list = $order_account->getShopOrderSumList($shop_id, $start_time, $end_time, $page_index, $page_size);
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopOrderRefundList()
     */
    public function getShopOrderRefundList($shop_id, $start_time, $end_time, $page_index, $page_size)
    {
        $order_account = new OrderAccount();
        $list = $order_account->getShopOrderRefundList($shop_id, $start_time, $end_time, $page_index, $page_size);
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopOrderStatics()
     */
    public function getShopOrderStatics($shop_id, $start_time, $end_time)
    {
        $order_account = new OrderAccount();
        $order_sum = $order_account->getShopOrderSum($shop_id, $start_time, $end_time);
        $order_refund_sum = $order_account->getShopOrderSumRefund($shop_id, $start_time, $end_time);
        $order_sum_account = $order_sum - $order_refund_sum;
        $array = array(
            'order_sum' => $order_sum,
            'order_refund_sum' => $order_refund_sum,
            'order_account' => $order_sum_account
        );
        return $array;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopOrderAccountDetail()
     */
    public function getShopOrderAccountDetail($shop_id)
    {
        // 获取总销售统计
        $account_all = $this->getShopOrderStatics($shop_id, '2015-1-1', '3050-1-1');
        // 获取今日销售统计
        $date_day_start = date("Y-m-d", time());
        $date_day_end = date("Y-m-d H:i:s", time());
        $account_day = $this->getShopOrderStatics($shop_id, $date_day_start, $date_day_end);
        // 获取周销售统计（7天）
        $date_week_start = date('Y-m-d', strtotime('-7 days'));
        $date_week_end = $date_day_end;
        $account_week = $this->getShopOrderStatics($shop_id, $date_week_start, $date_week_end);
        // 获取月销售统计(30天)
        $date_month_start = date('Y-m-d', strtotime('-30 days'));
        $date_month_end = $date_day_end;
        $account_month = $this->getShopOrderStatics($shop_id, $date_month_start, $date_month_end);
        $array = array(
            'day' => $account_day,
            'week' => $account_week,
            'month' => $account_month,
            'all' => $account_all
        );
        return $array;
    }

    /*
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopAccountCountInfo()
     */
    public function getShopAccountCountInfo($shop_id)
    {
        // 本月第一天
        $date_month_start = getTimeTurnTimeStamp(date('Y-m-d', strtotime('-30 days')));
        $date_month_end = getTimeTurnTimeStamp(date("Y-m-d H:i:s", time()));
        // 下单金额
        $order_account = new OrderAccount();
        $condition["create_time"] = [
            [
                ">=",
                $date_month_start
            ],
            [
                "<=",
                $date_month_end
            ]
        ];
        $condition['order_status'] = array(
            'NEQ',
            0
        );
        $condition['order_status'] = array(
            'NEQ',
            5
        );
        if ($shop_id != 0) {
            $condition['shop_id'] = array(
                'NEQ',
                0
            );
        }
        $order_money = $order_account->getShopSaleSum($condition);
        // var_dump($order_money);
        // 下单会员
        $order_user_num = $order_account->getShopSaleUserSum($condition);
        // 下单量
        $order_num = $order_account->getShopSaleNumSum($condition);
        // 下单商品数
        $order_goods_num = $order_account->getShopSaleGoodsNumSum($condition);
        // 平均客单价
        if ($order_user_num > 0) {
            $user_money_average = $order_money / $order_user_num;
        } else {
            $user_money_average = 0;
        }
        // 平均价格
        if ($order_goods_num > 0) {
            $goods_money_average = $order_money / $order_goods_num;
        } else {
            $goods_money_average = 0;
        }
        $array = array(
            "order_money" => sprintf('%.2f', $order_money),
            "order_user_num" => $order_user_num,
            "order_num" => $order_num,
            "order_goods_num" => $order_goods_num,
            "user_money_average" => sprintf('%.2f', $user_money_average),
            "goods_money_average" => sprintf('%.2f', $goods_money_average)
        );
        return $array;
    }

    /*
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopGoodsSalesList()
     */
    public function getShopGoodsSalesList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        // $goods_calculate = new GoodsCalculate();
        // $goods_sales_list = $goods_calculate->getGoodsSalesInfoList($page_index, $page_size , $condition , $order );
        // return $goods_sales_list;
        $goods_model = new NsGoodsModel();
        $tmp_array = $condition;
        if(!empty($condition["order_status"])){
            $order_condition["order_status"] = $condition["order_status"];
            unset($tmp_array["order_status"]);           
        }
        $goods_list = $goods_model->pageQuery($page_index, $page_size, $tmp_array, $order, '*');
        // 条件
        $start_date = getTimeTurnTimeStamp(date('Y-m-d', strtotime('-30 days')));
        $end_date = getTimeTurnTimeStamp(date("Y-m-d H:i:s", time()));
        $order_condition['create_time'] = [
            'between',
            [
                $start_date,
                $end_date
            ]
        ];
        
        $order_condition["shop_id"] = $condition["shop_id"];
        $goods_calculate = new GoodsCalculate();
        // 得到条件内的订单项
        $order_goods_list = $goods_calculate->getOrderGoodsSelect($order_condition);
        // 遍历商品
        foreach ($goods_list["data"] as $k => $v) {
            $data = array();
            $goods_sales_num = $goods_calculate->getGoodsSalesNum($order_goods_list, $v["goods_id"]);
            $goods_sales_money = $goods_calculate->getGoodsSalesMoney($order_goods_list, $v["goods_id"]);
            $data["sales_num"] = $goods_sales_num;
            $data["sales_money"] = $goods_sales_money;
            $goods_list["data"][$k]["sales_info"] = $data;
        }
        return $goods_list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::getShopGoodsSalesAll()
     */
    public function getShopGoodsSalesQuery($shop_id, $start_date, $end_date, $condition)
    {
        // TODO Auto-generated method stub
        // 商品
        $goods_model = new NsGoodsModel();
        $goods_list = $goods_model->getQuery($condition, "*", '');
        // 订单项
        $condition['create_time'] = [
            'between',
            [
                $start_date,
                $end_date
            ]
        ];
        $order_condition["create_time"] = [
            [
                ">=",
                $start_date
            ],
            [
                "<=",
                $end_date
            ]
        ];
        $order_condition['order_status'] = array(
            'NEQ',
            0
        );
        $order_condition['order_status'] = array(
            'NEQ',
            5
        );
        if ($shop_id != '') {
            $order_condition["shop_id"] = $shop_id;
        }
        $goods_calculate = new GoodsCalculate();
        $order_goods_list = $goods_calculate->getOrderGoodsSelect($order_condition);
        // 遍历商品
        foreach ($goods_list as $k => $v) {
            $data = array();
            $goods_sales_num = $goods_calculate->getGoodsSalesNum($order_goods_list, $v["goods_id"]);
            $goods_sales_money = $goods_calculate->getGoodsSalesMoney($order_goods_list, $v["goods_id"]);
            $goods_list[$k]["sales_num"] = $goods_sales_num;
            $goods_list[$k]["sales_money"] = $goods_sales_money;
        }
        return $goods_list;
    }

    /**
     * 查询一段时间内的店铺下单金额
     *
     * @param unknown $shop_id            
     * @param unknown $start_date            
     * @param unknown $end_date            
     * @return Ambigous <\data\service\Order\unknown, number, unknown>
     */
    public function getShopSaleSum($condition)
    {
        $order_account = new OrderAccount();
        $sales_num = $order_account->getShopSaleSum($condition);
        return $sales_num;
    }

    /**
     * 查询一段时间内的店铺下单量
     *
     * @param unknown $shop_id            
     * @param unknown $start_date            
     * @param unknown $end_date            
     * @return unknown
     */
    public function getShopSaleNumSum($condition)
    {
        $order_account = new OrderAccount();
        $sales_num = $order_account->getShopSaleNumSum($condition);
        return $sales_num;
    }

    /**
     * ***********************************************店铺账户--Start******************************************************
     */
    /**
     * 订单支付的时候 调整店铺账户
     *
     * @param string $order_out_trade_no            
     * @param number $order_id            
     */
    private function dealShopAccount_OrderPay($order_out_trade_no = "", $order_id = 0)
    {}

    /**
     * 订单完成的时候调整账户金额
     *
     * @param string $order_out_trade_no            
     * @param number $order_id            
     */
    private function dealShopAccount_OrderComplete($order_out_trade_no = "", $order_id = 0)
    {}

    /**
     * 订单支付
     *
     * @param unknown $order_id            
     */
    private function updateShopAccount_OrderPay($order_id)
    {
        $order_model = new NsOrderModel();
        $shop_account = new ShopAccount();
        $order = new OrderBusiness();
        $order_model->startTrans();
        try {
            $order_obj = $order_model->get($order_id);
            // 订单的实际付款金额
            $pay_money = $order->getOrderRealPayMoney($order_id);
            // 订单的支付方式
            $payment_type = $order_obj["payment_type"];
            // 店铺id
            $shop_id = $order_obj["shop_id"];
            // 订单号
            $order_no = $order_obj["order_no"];
            // 处理订单的营业总额
            $shop_account->addShopAccountProfitRecords(getSerialNo(), $shop_id, $pay_money, 1, $order_id, "店铺订单支付金额" . $pay_money . "元, 订单号为：" . $order_no . ", 支付方式【线下支付】。");
            if ($payment_type != ORDER_REFUND_STATUS) {
                // 在线支付 处理店铺的入账总额
                $shop_account->addShopAccountMoneyRecords(getSerialNo(), $shop_id, $pay_money, 1, $order_id, "店铺订单支付金额" . $pay_money . "元, 订单号为：" . $order_no . ", 支付方式【在线支付】, 已入店铺账户。");
            }
            // 处理平台的利润分成
            $this->addShopOrderAccountRecords($order_id, $order_no, $shop_id, $pay_money);
            $order_model->commit();
        } catch (\Exception $e) {
            $order_model->rollback();
        }
    }

    /**
     * 订单项退款
     *
     * @param unknown $order_goods_id            
     */
    private function updateShopAccount_OrderRefund($order_goods_id)
    {}

    /**
     * 订单完成
     *
     * @param unknown $order_id            
     */
    private function updateShopAccount_OrderComplete($order_id)
    {}

    /**
     * ***********************************************店铺账户--End******************************************************
     */
    
    /**
     * ***********************************************平台账户计算--Start******************************************************
     */
    /**
     * 订单支付时处理 平台的账户
     *
     * @param string $order_out_trade_no            
     * @param number $order_id            
     */
    public function dealPlatformAccountOrderPay($order_out_trade_no = "", $order_id = 0)
    {}

    /**
     * 处理平台的利润抽成
     *
     * @param unknown $order_id            
     * @param unknown $order_no            
     * @param unknown $shop_id            
     * @param unknown $pay_money            
     */
    private function addShopOrderAccountRecords($order_id, $order_no, $shop_id, $pay_money)
    {}

    /**
     * 订单退款 更新平台抽取提成
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     * @param unknown $shop_id            
     */
    private function updateShopOrderGoodsReturnRecords($order_id, $order_goods_id, $shop_id)
    {}

    /**
     * 订单支付成功后处理 平台账户
     *
     * @param unknown $orderid            
     */
    private function updateAccountOrderPay($order_id)
    {
        $order_model = new NsOrderModel();
        $shop_account = new ShopAccount();
        $order = new OrderBusiness();
        $order_model->startTrans();
        try {
            $order_obj = $order_model->get($order_id);
            // 订单的实际付款金额
            $pay_money = $order->getOrderRealPayMoney($order_id);
            // 订单的支付方式
            $payment_type = $order_obj["payment_type"];
            // 店铺id
            $shop_id = $order_obj["shop_id"];
            // 订单号
            $order_no = $order_obj["order_no"];
            if ($payment_type != ORDER_REFUND_STATUS) {
                // 在线支付 处理平台的资金账户
                $shop_account->addAccountOrderRecords($shop_id, $pay_money, 1, $order_id, "店铺订单支付金额" . $pay_money . "元, 订单号为：" . $order_no . ", 支付方式【在线支付】。");
            }
            $order_model->commit();
        } catch (\Exception $e) {
            $order_model->rollback();
        }
    }

    /**
     * 订单完成时 处理平台的利润抽成
     *
     * @param unknown $order_id            
     */
    private function updateAccountOrderComplete($order_id)
    {}

    /**
     * 订单退款 更细平台的订单支付金额
     *
     * @param unknown $order_goods_id            
     */
    private function updateAccountOrderRefund($order_goods_id)
    {}

    /**
     * ***********************************************平台账户计算--End******************************************************
     */
    
    /**
     * ***********************************************订单的佣金计算--Start******************************************************
     */
    
    /**
     * 支付后续佣金操作
     *
     * @param unknown $order_out_trade_no            
     * @param unknown $order_id            
     */
    private function orderCommissionCalculate($order_out_trade_no = "", $order_id = 0)
    {
        // 针对非基础电商版
        if (NS_VERSION != NS_VER_B2C) {
            if ($order_out_trade_no != "" && $order_id == 0) {
                $order_model = new NsOrderModel();
                $condition = " out_trade_no=" . $order_out_trade_no;
                $order_list = $order_model->getQuery($condition, "order_id", "");
                foreach ($order_list as $k => $v) {
                    $this->oneOrderCommissionCalculate($v["order_id"]);
                }
            } else 
                if ($order_out_trade_no == "" && $order_id != 0) {
                    $this->oneOrderCommissionCalculate($order_id);
                }
        }
    }

    /**
     * 处理单个 订单佣金计算
     *
     * @param unknown $order_id            
     */
    private function oneOrderCommissionCalculate($order_id)
    {
        if (NS_VERSION != NS_VER_B2C) {
            $commissionCalculate = new NfxCommissionCalculate($order_id);
            // 分销佣金计算
            $res = $commissionCalculate->orderdistributionCommission();
            // 区域代理计算
            $res = $commissionCalculate->orderRegionAgentCommission();
            // 股东分红计算
            $res = $commissionCalculate->orderPartnerCommission();
        }
    }

    public function partent_test()
    {
        $commissionCalculate = new NfxCommissionCalculate(476);
        $res = $commissionCalculate->orderPartnerCommission();
    }

    /**
     * 订单退款成功后需要重新计算订单的佣金
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     */
    public function updateCommissionMoney($order_id, $order_goods_id)
    {
        // 单店基础版不进行计算
        if (NS_VERSION != NS_VER_B2C) {
            $commissionCalculate = new NfxCommissionCalculate($order_id, $order_goods_id);
            // 重新计算分销佣金
            $commissionCalculate->updateOrderDistributionCommission();
            // 重新计算股东分红
            $commissionCalculate->updateOrderPartnerCommission();
            // 重新计算区域代理佣金
            $commissionCalculate->updateOrderRegionAgentCommission();
            // 订单退款成功后 发放佣金
            $this->updateOrderCommission($order_id);
        }
    }

    /**
     * 订单完成交易进行 佣金结算
     *
     * @param unknown $order_id            
     */
    private function updateOrderCommission($order_id)
    {
        if (NS_VERSION != NS_VER_B2C) {
            $order_model = new NsOrderModel();
            $order_model->startTrans();
            try {
                $shop_obj = $order_model->get($order_id);
                $order_sataus = $shop_obj["order_status"];
                // 判断当前订单的状态是否 已经交易完成 或者 已退款的状态
                if ($order_sataus == ORDER_COMPLETE_SUCCESS || $order_sataus == ORDER_COMPLETE_REFUND || $order_sataus == ORDER_COMPLETE_SHUTDOWN) {
                    // 得到订单的店铺id
                    $shop_id = $shop_obj["shop_id"];
                    // 得到订单用户id
                    $uid = $shop_obj["buyer_id"];
                    $user_service = new NfxUser();
                    // 发放订单的三级分销佣金
                    $user_service->updateCommissionDistributionIssue($order_id);
                    // 更新当前用户的分销商等级
                    $user_service->updatePromoterLevel($uid, $shop_id);
                    // /发放订单的区域代理佣金
                    $user_service->updateCommissionRegionAgentIssue($order_id);
                    // 发放订单的股东分红佣金
                    $user_service->updateCommissionPartnerIssue($order_id);
                    // 更新用户的股东等级
                    $user_service->updatePartnerLevel($uid, $shop_id);
                }
                $order_model->commit();
            } catch (\Exception $e) {
                $order_model->rollback();
            }
        }
    }

    /**
     * ***********************************************订单的佣金计算--End******************************************************
     */
    
    /**
     * ***********************************************招商员的账户计算--Start******************************************************
     */
    /**
     * 招商员的订单佣金计算
     *
     * @param string $order_out_trade_no            
     * @param number $order_id            
     */
    private function AssistantOrderCommissionCalculate($order_out_trade_no = "", $order_id = 0)
    {}

    /**
     * 订单退款 更新佣金金额
     *
     * @param unknown $order_id            
     */
    private function UpdateAssistantOrderCommissionRefund($order_id)
    {
        $Assistant_account_service = new NbsBusinessAssistantAccount();
        $Assistant_account_service->updateOrderBusinessAssistant($order_id);
    }

    /**
     * 订单交易完成发放订单的佣金
     *
     * @param unknown $order_id            
     */
    private function UpdateAssistantOrderCommission($order_id)
    {}

    /**
     * ***********************************************招商员的账户计算--End******************************************************
     */
    /**
     * 查询店铺的退货设置
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopReturnSet()
     */
    public function getShopReturnSet($shop_id)
    {
        $shop_return = new NsOrderShopReturnModel();
        $shop_return_obj = $shop_return->get($shop_id);
        if (empty($shop_return_obj)) {
            $data = array(
                "shop_id" => $shop_id,
                "create_time" => time()
            );
            $shop_return->save($data);
            $shop_return_obj = $shop_return->get($shop_id);
        }
        return $shop_return_obj;
    }

    /**
     *
     * 更新店铺的退货信息
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::updateShopReturnSet()
     */
    public function updateShopReturnSet($shop_id, $address, $real_name, $mobile, $zipcode)
    {
        $shop_return = new NsOrderShopReturnModel();
        $data = array(
            "shop_address" => $address,
            "seller_name" => $real_name,
            "seller_mobile" => $mobile,
            "seller_zipcode" => $zipcode,
            "modify_time" => time()
        );
        $result_id = $shop_return->save($data, [
            "shop_id" => $shop_id
        ]);
        return $result_id;
    }

    /**
     * 得到订单的发货信息
     *
     * @param unknown $order_ids            
     */
    public function getOrderGoodsExpressDetail($order_ids, $shop_id)
    {
        $order_goods_model = new NsOrderGoodsModel();
        $order_model = new NsOrderModel();
        $order_goods_express = new NsOrderGoodsExpressModel();
        // 查询订单的订单项的商品信息
        $order_goods_list = $order_goods_model->where(" order_id in ($order_ids)")->select();
        
        for ($i = 0; $i < count($order_goods_list); $i ++) {
            $order_id = $order_goods_list[$i]["order_id"];
            $order_goods_id = $order_goods_list[$i]["order_goods_id"];
            $order_obj = $order_model->get($order_id);
            $order_goods_list[$i]["order_no"] = $order_obj["order_no"];
            $goods_express_obj = $order_goods_express->where("FIND_IN_SET($order_goods_id,order_goods_id_array)")->select();
            if (! empty($goods_express_obj)) {
                $order_goods_list[$i]["express_company"] = $goods_express_obj[0]["express_company"];
                $order_goods_list[$i]["express_no"] = $goods_express_obj[0]["express_no"];
            } else {
                $order_goods_list[$i]["express_company"] = "";
                $order_goods_list[$i]["express_no"] = "";
            }
        }
        return $order_goods_list;
    }

    /**
     * 通过订单id 得到 该订单的发货物流
     *
     * @param unknown $order_id            
     */
    public function getOrderGoodsExpressList($order_id)
    {
        $order_goods_express_model = new NsOrderGoodsExpressModel();
        $express_list = $order_goods_express_model->getQuery([
            "order_id" => $order_id
        ], "*", "");
        return $express_list;
    }

    /**
     * 订单提货(non-PHPdoc)
     *
     * @see \data\api\IOrder::pickupOrder()
     */
    public function pickupOrder($order_id, $buyer_name, $buyer_phone, $remark)
    {
        $order = new OrderBusiness();
        $retval = $order->pickupOrder($order_id, $buyer_name, $buyer_phone, $remark);
        return $retval;
    }

    /**
     * 查询订单项的物流信息
     *
     * @param unknown $order_goods_id            
     */
    public function getOrderGoodsExpressMessage($express_id)
    {
        try {
            $order_express_model = new NsOrderGoodsExpressModel();
            $express_obj = $order_express_model->get($express_id);
            if (! empty($express_obj)) {
                $order_id = $express_obj["order_id"];
                $order_model = new NsOrderModel();
                // 订单编号
                $order_obj = $order_model->get($order_id);
                $order_no = $order_obj["order_no"];
                $shop_id = $order_obj["shop_id"];
                // 物流公司信息
                $express_company_id = $express_obj["express_company_id"];
                $express_company_model = new NsOrderExpressCompanyModel();
                $express_company_obj = $express_company_model->get($express_company_id);
                // 快递公司编号
                $express_no = $express_company_obj["express_no"];
                // 物流编号
                $send_no = $express_obj["express_no"];
                $kdniao = new Kdniao($shop_id);
                $data = array(
                    "OrderCode" => $order_no,
                    "ShipperCode" => $express_no,
                    "LogisticCode" => $send_no
                );
                $result = $kdniao->getOrderTracesByJson(json_encode($data));
                return json_decode($result, true);
            } else {
                return array(
                    "Success" => false,
                    "Reason" => "订单物流信息有误!"
                );
            }
        } catch (\Exception $e) {
            return array(
                "Success" => false,
                "Reason" => "订单物流信息有误!"
            );
        }
    }

    /**
     * 添加卖家对订单的备注
     *
     * @param unknown $order_goods_id            
     */
    public function addOrderSellerMemo($order_id, $memo)
    {
        $order = new NsOrderModel();
        $data = array(
            'seller_memo' => $memo
        );
        $retval = $order->save($data, [
            'order_id' => $order_id
        ]);
        return $retval;
    }

    /**
     * 获取订单备注信息
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::getOrderRemark()
     */
    public function getOrderSellerMemo($order_id)
    {
        $order = new NsOrderModel();
        $res = $order->getQuery([
            'order_id' => $order_id
        ], "seller_memo", '');
        $seller_memo = "";
        if (! empty($res[0]['seller_memo'])) {
            $seller_memo = $res[0]['seller_memo'];
        }
        return $seller_memo;
    }
    /**
     * 得到订单的收货地址
     * @param unknown $order_id
     * @return unknown
     */
    public function getOrderReceiveDetail($order_id){
        $order = new NsOrderModel();
        $res = $order->getInfo([
            'order_id' => $order_id
        ], "order_id,receiver_mobile,receiver_province,receiver_city,receiver_district,receiver_address,receiver_zip,receiver_name", '');
        return $res;
    }
    /**
     * 更新订单的收货地址
     * @param unknown $order_id
     * @param unknown $receiver_mobile
     * @param unknown $receiver_province
     * @param unknown $receiver_city
     * @param unknown $receiver_district
     * @param unknown $receiver_address
     * @param unknown $receiver_zip
     * @param unknown $receiver_name
     */
    public function updateOrderReceiveDetail($order_id, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name){
        $order = new NsOrderModel();
        $data = array(
            'receiver_mobile' => $receiver_mobile,
            'receiver_province' => $receiver_province,
            'receiver_city' => $receiver_city,
            'receiver_district' => $receiver_district,
            'receiver_address' => $receiver_address,
            'receiver_zip' => $receiver_zip,
            'receiver_name' => $receiver_name
        );
        $retval = $order->save($data, [
            'order_id' => $order_id
        ]);
        return $retval;
    }
    /**
     * 获取自提点运费
     * @param unknown $goods_sku_list
     */
    public function getPickupMoney($goods_sku_list_price)
    {
        $goods_preference = new GoodsPreference();
        $pick_money = $goods_preference->getPickupMoney($goods_sku_list_price);
         return $pick_money;
    
    }
    
    public function getOrderNumByOrderStatu($condition){
        $order = new NsOrderModel();
        return $order->getCount($condition);
    }
    
    /**
     * 评论送积分
     */
    public function commentPoint($order_id){
        //给记录表添加记录
        $goods_comment = new NsGoodsCommentModel();
        $rewardRule = new PromoteRewardRule();
        //查询评论赠送积分数量，然后叠加
        $shop_id = $this->instance_id;
        $uid = $this->uid;
        $info = $rewardRule->getRewardRuleDetail($shop_id);
        $data = array(
            'shop_id'     =>   $shop_id,
            'uid'         =>   $uid,
            'order_id'    =>   $order_id,
            'status'      =>   1,
            'number'      =>   $info['comment_point'],
            'create_time' =>   time()
        );
        $retval = $goods_comment->save($data);
        if($retval>0){
            //给总记录表加记录
            $result = $rewardRule->addMemberPointData($shop_id, $uid, $info['comment_point'], 20, '评论赠送积分');
        
        }
    }
    /**
     * 
     * 查询会员的某个订单的条数
     * (non-PHPdoc)
     * @see \data\api\IOrder::getUserOrderDetailCount()
     */
    public function getUserOrderDetailCount($user_id, $order_id){
        $order_count=0;
        $orderModel=new NsOrderModel();
        $condition=array(
          "buyer_id"=>$user_id,
          "order_id"=>$order_id
        );
        $order_count=$orderModel->getCount($condition);
        return $order_count;
    }
    
    /**
     * 查询会员某个条件的订单的条数
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getUserOrderCountByCondition($condition){
        $order_count=0;
        $orderModel=new NsOrderModel();
        $order_count=$orderModel->getCount($condition);
        return $order_count;
    }
    /**
     * 查询会员某个条件下的订单商品数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getUserOrderGoodsCountByCondition($condition){
        $order_goods = new NsOrderGoodsModel();
        $order_count = $order_goods -> getCount($condition);
        return $order_count;
    }
    
}