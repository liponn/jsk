<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商品属性值
 * @author Administrator
 *
 */
class NsAttributeValueModel extends BaseModel {

    protected $table = 'ns_attribute_value';
    protected $rule = [
        'attr_value_id'  =>  '',
    ];
    protected $msg = [
        'attr_value_id'  =>  '',
    ];

}