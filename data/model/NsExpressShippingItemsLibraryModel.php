<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 物流模版打印项库
 * @author Administrator
 */
class NsExpressShippingItemsLibraryModel extends BaseModel {
    
    protected $table = 'ns_express_shipping_items_library';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}