<?php
namespace data\api;
/**
 * 计划任务接口
 *
 */
interface IEvents{
    /**
     * 订单长时间未付款自动交易关闭
     */
    function ordersClose();
    /**
     * 订单收货后7天自动交易完成
     */
    function ordersComplete();
    /**
     * 满减送超过期限自动关闭, 进入时间自动开始
     */
    function mansongOperation();
    /**
     * 赠品超过有效期限自动取消
     */
    function giftClose();
    /**
     * 限时折扣自动开始以及自动关闭
     */
    function discountOperation();
    /**
     * 自动收货
     */
    function autoDeilvery();
    /**
     * 优惠券自动过期
     */
    function autoCouponClose();
}