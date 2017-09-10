<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 店铺账户表
 * @author Administrator
 *
 */
class NsShopAccountModel extends BaseModel {

    protected $table = 'ns_shop_account';
    protected $rule = [
        'shop_id'  =>  '',
    ];
    protected $msg = [
        'shop_id'  =>  '',
    ];

}