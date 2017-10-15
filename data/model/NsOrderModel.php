<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
class NsOrderModel extends BaseModel {

    protected $table = 'dd_order';
    protected $rule = [
        '编号'  =>  '',
    ];
    protected $msg = [
        '编号'  =>  '',
    ];
    public function _getOrderListByUid($page_index, $page_size, $condition, $order, $filed = '*'){
        $sql = "SELECT {$filed} FROM dd_order WHERE 客户ID = '{$condition}'";
    	// $sql = "SELECT {$filed} FROM dd_order WHERE 微信ID = {$condition}";
    	$res = $this->sqlQuery($sql);
    	// exit($sql);
    	return $res;
    }


    public function _getOrderByDay($uid){
        $date = date("Y-m-d");
        $sql = "SELECT * FROM dd_order WHERE 客户ID = '{$uid}' AND 下单日期 = '{$date}'";
        // exit($sql);
        $res = $this->sqlQuery($sql);
        return $res;
    }
    public function _orderUpdate($data,$condition){
        $sql = "UPDATE `dd_order`  SET `下单日期`= '{$data['下单日期']}',`客户ID`='{$data['客户ID']}',`微信ID`='{$data['微信ID']}',`下单时间`='{$data['下单时间']}' WHERE  `订单编号` = '{$condition}'  ";
        // exit($sql);
        $res = $this->sqlQuery($sql);
        return $res;
    }
}