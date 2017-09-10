<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 店铺表
 * @author Administrator
 *
 */
class NsShopModel extends BaseModel {

    protected $table = 'ns_shop';
    protected $rule = [
        'shop_id'  =>  '',
    ];
    protected $msg = [
        'shop_id'  =>  '',
    ];

}