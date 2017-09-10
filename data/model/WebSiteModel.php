<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
use think\Validate;
/**
 * 网页基本信息
 */
class WebSiteModel extends BaseModel
{
    // protected $table = '__SYS_WEBSITE__';//这种写法 待看TP MODEL基类
    protected $table = 'sys_website';
    protected $rule = [
        'website_id'  =>  '',
        'third_count'  =>  'no_html_parse',
    ];
    protected $msg = [
        'website_id'  =>  '',
        'third_count'  =>  '',
    ];
    
}