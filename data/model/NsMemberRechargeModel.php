<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 会员等级列表
 * @author Administrator
 *
 */
class NsMemberRechargeModel extends BaseModel {
    protected $table = 'ns_member_recharge';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}