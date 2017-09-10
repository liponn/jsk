<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 物流模版打印项库
 * @author Administrator
 */
class NsExpressShippingModel extends BaseModel {
    
    protected $table = 'ns_express_shipping';
    protected $rule = [
        'sid'  =>  '',
    ];
    protected $msg = [
        'sid'  =>  '',
    ];
}