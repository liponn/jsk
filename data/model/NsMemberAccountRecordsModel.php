<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 会员账户流水表(积分，余额)
 * @author Administrator
 *
 */
class NsMemberAccountRecordsModel extends BaseModel {
    protected $table = 'ns_member_account_records';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}