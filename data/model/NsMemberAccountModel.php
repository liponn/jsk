<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 会员账户表(积分，余额)
 * @author Administrator
 *
 */
class NsMemberAccountModel extends BaseModel {
    protected $table = 'ns_member_account';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}