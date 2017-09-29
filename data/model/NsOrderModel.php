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
    	$sql = "SELECT {$filed} FROM dd_order WHERE 客户ID = {$condition}";
    	$res = $this->sqlQuery($sql);
    	// exit($sql);
    	return $res;
    }

}