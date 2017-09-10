<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 店铺退货设置
 * @author Administrator
 *
 */
class NsOrderShopReturnModel extends BaseModel {

    protected $table = 'ns_order_shop_return';
    protected $rule = [
        'shop_id'  =>  '',
    ];
    protected $msg = [
        'shop_id'  =>  '',
    ];
    
}