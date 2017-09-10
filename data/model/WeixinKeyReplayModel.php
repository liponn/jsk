<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 微信关键词回复
 *
 */
class WeixinKeyReplayModel extends BaseModel {
    protected $table = 'sys_weixin_key_replay';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}