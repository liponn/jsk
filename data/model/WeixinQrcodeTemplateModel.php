<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 用户组
 *
 */
class WeixinQrcodeTemplateModel extends BaseModel {
    
    protected $table = 'sys_weixin_qrcode_template';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}