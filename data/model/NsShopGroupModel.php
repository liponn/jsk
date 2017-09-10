<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 店铺分组表
 * @author Administrator
 *
 */
class NsShopGroupModel extends BaseModel {

    protected $table = 'ns_shop_group';
    protected $rule = [
        'express_address_id'  =>  '',
    ];
    protected $msg = [
        'express_address_id'  =>  '',
    ];

}