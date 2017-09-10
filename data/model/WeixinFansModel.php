<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 微信公众号粉丝管理
 *
 */
class WeixinFansModel extends BaseModel {
    protected $table = 'sys_weixin_fans';
    protected $rule = [
        'fans_id'  =>  '',
    ];
    protected $msg = [
        'fans_id'  =>  '',
    ];
}