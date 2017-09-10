<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 订单操作表
 *  action_id int(11) NOT NULL AUTO_INCREMENT COMMENT '动作id',
 order_id int(11) NOT NULL COMMENT '订单id',
 action varchar(255) NOT NULL DEFAULT '' COMMENT '动作内容',
 uid int(11) NOT NULL DEFAULT 0 COMMENT '操作人id',
 user_name varchar(50) NOT NULL DEFAULT '' COMMENT '操作人',
 order_status int(11) NOT NULL COMMENT '订单大状态',
 order_status_text varchar(255) NOT NULL DEFAULT '' COMMENT '订单状态名称',
 action_time datetime NOT NULL COMMENT '操作时间',
 PRIMARY KEY (action_id)
 */
class NsOrderActionModel extends BaseModel {

    protected $table = 'ns_order_action';
    protected $rule = [
        'action_id'  =>  '',
    ];
    protected $msg = [
        'action_id'  =>  '',
    ];

}