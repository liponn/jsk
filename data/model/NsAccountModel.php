<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商城金额统计
 * @author Administrator
 *
 */
class NsAccountModel extends BaseModel {

    protected $table = 'ns_account';
    protected $rule = [
        'account_id'  =>  '',
    ];
    protected $msg = [
        'account_id'  =>  '',
    ];

}