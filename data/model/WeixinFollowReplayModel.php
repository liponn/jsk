<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 微信关注时回复
 *
 */
class WeixinFollowReplayModel extends BaseModel {
    protected $table = 'sys_weixin_follow_replay';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}