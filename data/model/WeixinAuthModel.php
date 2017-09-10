<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 微信公众号授权
 *
 */
class WeixinAuthModel extends BaseModel {
    protected $table = 'sys_weixin_auth';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}