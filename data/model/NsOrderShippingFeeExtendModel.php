<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 运费模板扩展表
 * @author Administrator
 *
 */
class NsOrderShippingFeeExtendModel extends BaseModel {

    protected $table = 'ns_order_shipping_fee_extend';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}