<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
class NsOrderDetailsModel extends BaseModel {

    protected $table = 'dd_order_details';
    protected $rule = [
        '编号'  =>  '',
    ];
    protected $msg = [
        '编号'  =>  '',
    ];

    public function addDetails($data_details){
    	$sql = "INSERT INTO dd_order_details(订单编号,产品编号,产品名称,商品单价,实际商品单价,预定数量,分配数量,订单金额,商品类型) VALUES('{$data_details['订单编号']}','{$data_details['产品编号']}','{$data_details['产品名称']}','{$data_details['商品单价']}','{$data_details['实际商品单价']}','{$data_details['预定数量']}','{$data_details['分配数量']}','{$data_details['订单金额']}','{$data_details['商品类型']}')";
    	// $sql = "INSERT INTO dd_order_details(订单编号,产品编号,产品名称,商品单价,实际商品单价,预定数量,分配数量,订单金额,商品类型) VALUES('{$data_details['订单编号']}',)";
    	$res = $this->sqlQuery($sql);
    	return $res;
    }

}