<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 供货商表
 */
class NsSupplierModel extends BaseModel {
    
    protected $table = 'ns_supplier';
    protected $rule = [
        'supplier_id'  =>  '',
    ];
    protected $msg = [
        'supplier_id'  =>  '',
    ];
}