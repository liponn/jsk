<?php
namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 赠品发放记录
 * @author Administrator
 *
 */
class NsGiftGrantRecordsModel extends BaseModel {

    protected $table = 'ns_gift_grant_records';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}