<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 运费模板主表
 * @author Administrator
 *
 */
class NsOrderShippingFeeModel extends BaseModel {

    protected $table = 'ns_order_shipping_fee';
    protected $rule = [
        'shipping_fee_id'  =>  '',
    ];
    protected $msg = [
        'shipping_fee_id'  =>  '',
    ];

}