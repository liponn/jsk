<?php

namespace data\model;
use think\Db;
use data\model\BaseModel as BaseModel;
/**
 * 系统实例类型表(店铺类型)
 */
class InstanceTypeModel extends BaseModel {

    protected $table = 'sys_instance_type';
    protected $rule = [
        'instance_typeid'  =>  '',
    ];
    protected $msg = [
        'instance_typeid'  =>  '',
    ];
}