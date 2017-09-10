<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 地区表
 */
class AreaModel extends BaseModel {

    protected $table = 'sys_area';
    protected $rule = [
        'area_id'  =>  '',
    ];
    protected $msg = [
        'area_id'  =>  '',
    ];
}