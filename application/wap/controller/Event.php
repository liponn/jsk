<?php
namespace app\wap\controller;

use data\service\Events;
use think\Controller;
\think\Loader::addNamespace('data', 'data/');

class Event extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 自动执行函数
     */
    public function index()
    {
        $event = new Events();
        $retval_order_close = $event->ordersClose();
        echo "订单关闭";
        var_dump($retval_order_close);
        $retval_mansong_operation = $event->mansongOperation();
        echo "满减送操作";
        var_dump($retval_mansong_operation);
        $retval_discount_operation = $event->discountOperation();
        echo "限时折扣操作";
        var_dump($retval_discount_operation);
        $retval_order_complete = $event->ordersComplete();
        echo "订单完成";
        var_dump($retval_order_complete);
    }
}