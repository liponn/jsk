<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商品相关属性表
 * @author Administrator
 *
 */
class NsAttributeModel extends BaseModel {

    protected $table = 'ns_attribute';
    protected $rule = [
        'attr_id'  =>  '',
    ];
    protected $msg = [
        'attr_id'  =>  '',
    ];

}