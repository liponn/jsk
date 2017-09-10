<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 图文消息主表
 *
 */
class WeixinMediaModel extends BaseModel {
    protected $table = 'sys_weixin_media';
    protected $rule = [
        'media_id'  =>  '',
    ];
    protected $msg = [
        'media_id'  =>  '',
    ];
}