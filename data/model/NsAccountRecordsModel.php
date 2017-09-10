<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商城的资金记录
 * @author Administrator
 *
 */
class NsAccountRecordsModel extends BaseModel {

    protected $table = 'ns_account_records';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}