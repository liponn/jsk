<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
class NsOrderModel extends BaseModel {

    protected $table = 'dd_order';
    protected $rule = [
        '编号'  =>  '',
    ];
    protected $msg = [
        '编号'  =>  '',
    ];

}