<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 插件表
 */
class SysAddonsModel extends BaseModel {
    
    protected $table = 'sys_addons';
    protected $rule = [
        'id'  =>  '',
        'config'  =>  'no_html_parse',
    ];
    protected $msg = [
        'id'  =>  '',
        'config'  =>  '',
    ];
}