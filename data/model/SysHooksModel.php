<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 后台用户表
 */
class SysHooksModel extends BaseModel {
    
    protected $table = 'sys_hooks';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}