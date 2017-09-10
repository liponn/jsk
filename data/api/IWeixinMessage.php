<?php
namespace data\api;

/**
 * 微信消息接口       
 */
interface IWeixinMessage
{
    /**
     * 获取微信模板消息
     */
    function getWeixinInstanceMsg($instance_id);
    /**
     * 更新微信模板消息内容
     * @param unknown $instance_id
     */
    function updateWeixinInstanceMessage($instance_id);
    /**
     * 获取微信消息模板
     */
    function getWeixinMsgTemplate();
    /**
     * 发送订单提交创建消息
     * @param unknown $order_id
     */
    function sendWeixinOrderCreateMessage($order_id);
    /**
     * 发送订单支付消息
     * @param unknown $order_id
     */
    function sendWeixinOrderPayMessage($order_id);
    /**
     * 发送订单退款申请
     * @param unknown $order_id
     * @param unknown $order_goods_id
     */
    function sendWeixinOrderRefundApply($order_id, $order_goods_id);
    /**
     * 发送订单退款结果通知
     * @param unknown $order_id
     * @param unknown $order_goods_id
     */
    function sendWeixinOrderRefundMessage($order_id, $order_goods_id);
    /**
     * 发送订单发货通知
     * @param unknown $order_id
     */
    function sendWeixinOrderDeliverMessage($order_id);
    /**
     * 给用户发送消息
     * @param unknown $openid
     */
    function sendMessageToUser($openid, $msg_type, $content);
}