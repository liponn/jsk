<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 微信默认回复
 *
 */
class WeixinDefaultReplayModel extends BaseModel {
    protected $table = 'sys_weixin_default_replay';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}