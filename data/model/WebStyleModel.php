<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 网页基本信息
 */
class WebStyleModel extends BaseModel
{
    protected $table = '__SYS_WEB_STYLE__';
    protected $rule = [
        'style_id'  =>  '',
    ];
    protected $msg = [
        'style_id'  =>  '',
    ];
    
}