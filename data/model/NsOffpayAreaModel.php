<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 货到付款支持地区表
 *    
 * shop_id int(11) DEFAULT NULL COMMENT '店铺Id',
   area_id text DEFAULT NULL COMMENT '省市县Id组合'
 * @author Administrator
 *
 */

class NsOffpayAreaModel extends BaseModel
{
    protected $table = 'ns_offpay_area';
    protected $rule = [
        'shop_id'  =>  '',
    ];
    protected $msg = [
        'shop_id'  =>  '',
    ];
}