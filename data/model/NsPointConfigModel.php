<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 积分设置表
 */
class NsPointConfigModel extends BaseModel {

    protected $table = 'ns_point_config';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}