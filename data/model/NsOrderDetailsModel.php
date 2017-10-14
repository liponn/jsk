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


    public function _getOrderDetailList($order_id){
        $sql = "SELECT *,商品单价*分配数量 as all_price FROM dd_order_details WHERE 订单编号 = '{$order_id}'";
        $res = $this->sqlQuery($sql);
        return $res;
    }
    public function addNweDetails($data){
        $sql = "SELECT * FROM dd_order_details WHERE 订单编号 = {$data['订单编号']} AND 产品编号 = '{$data['产品编号']}'";
        $res = $this->sqlQuery($sql);
        if(!empty($res)){
            //update
            $sql = "UPDATE dd_order_details SET 产品名称 = '{$data['产品名称']}' , 商品单价 = '{$data['商品单价']}',实际商品单价 = '{$data['实际商品单价']}',预定数量 = '{$data['预定数量']}',分配数量 = '{$data['分配数量']}' WHERE 编号 = {$res[0]['编号']}";
            // exit($sql);
            $res = $this->sqlQuery($sql);
        }else{
            //$sql = "";
            $this->addDetails($data);
        }
    }

}