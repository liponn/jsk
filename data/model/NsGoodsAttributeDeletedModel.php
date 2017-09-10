<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商品属性 回收站表
 * @author Administrator
 *
 */
class NsGoodsAttributeDeletedModel extends BaseModel {

    protected $table = 'ns_goods_attribute_deleted';
    protected $rule = [
        'attr_id'  =>  '',
    ];
    protected $msg = [
        'attr_id'  =>  '',
    ];

}