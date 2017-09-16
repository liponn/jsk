<?php
namespace data\model;

use data\model\BaseModel as BaseModel;

class JsChanPinLeiBieModel extends BaseModel {

    // protected $table = 'ns_goods_category';
    protected $table = 'jc_chanpinleibie';
    protected $rule = [
        '代码'  =>  '',
        // 'category_id'  =>  '',
    ];
    protected $msg = [
        '代码'  =>  '',
        // 'category_id'  =>  '',
    ];
}