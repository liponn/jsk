<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商品sku表
 * @author Administrator
 *
 */
class NsGoodsSkuModel extends BaseModel {

    protected $table = 'ns_goods_sku';
    protected $rule = [
        'sku_id'  =>  '',
    ];
    protected $msg = [
        'sku_id'  =>  '',
    ];

}