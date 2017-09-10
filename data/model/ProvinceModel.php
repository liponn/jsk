<?php

namespace data\model;
use think\Db;
use data\model\BaseModel as BaseModel;
/**
 * 地区省表
 */
class ProvinceModel extends BaseModel {

    protected $table = 'sys_province';
    protected $rule = [
        'province_id'  =>  '',
    ];
    protected $msg = [
        'province_id'  =>  '',
    ];
}