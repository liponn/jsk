<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 满额包邮活动表
 */
class NsPromotionFullMailModel extends BaseModel {

    protected $table = 'ns_promotion_full_mail';
    protected $rule = [
        'mail_id'  =>  '',
    ];
    protected $msg = [
        'mail_id'  =>  '',
    ];

}