<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 图文消息明细表
 *
 */
class WeixinMediaItemModel extends BaseModel {
    protected $table = 'sys_weixin_media_item';
    protected $rule = [
        'id'  =>  '',
        'content'  =>  'no_html_parse',
    ];
    protected $msg = [
        'id'  =>  '',
        'content'  =>  '',
    ];
}