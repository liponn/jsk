<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 地区市表
 */

class CityModel extends BaseModel {

    protected $table = 'sys_city';
    protected $rule = [
        'city_id'  =>  '',
    ];
    protected $msg = [
        'city_id'  =>  '',
    ];
}