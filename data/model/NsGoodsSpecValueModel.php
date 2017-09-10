<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商品属性规格值表
 * @author Administrator
 *
 */
class NsGoodsSpecValueModel extends BaseModel {

    protected $table = 'ns_goods_spec_value';
    protected $rule = [
        'spec_value_id'  =>  '',
    ];
    protected $msg = [
        'spec_value_id'  =>  '',
    ];

}