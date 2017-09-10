<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 微信用户消息回复表
 *
 */
class WeixinUserMsgReplayModel extends BaseModel {
    protected $table = 'sys_weixin_user_msg_replay';
    protected $rule = [
        'replay_id'  =>  '',
    ];
    protected $msg = [
        'replay_id'  =>  '',
    ];
}