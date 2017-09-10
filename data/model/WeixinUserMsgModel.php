<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 微信用户消息表
 *
 */
class WeixinUserMsgModel extends BaseModel {
    protected $table = 'sys_weixin_user_msg';
    protected $rule = [
        'msg_id'  =>  '',
    ];
    protected $msg = [
        'msg_id'  =>  '',
    ];
}