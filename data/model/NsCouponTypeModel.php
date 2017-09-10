<?php
namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 优惠券类型表
 * @author Administrator
 *
 */
class NsCouponTypeModel extends BaseModel {

    protected $table = 'ns_coupon_type';
    protected $rule = [
        'coupon_type_id'  =>  '',
    ];
    protected $msg = [
        'coupon_type_id'  =>  '',
    ];

}