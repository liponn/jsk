<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 平台广告位
 * @author Administrator
 *
 */
class NsPlatformAdvPositionModel extends BaseModel {

    protected $table = 'ns_platform_adv_position';
    protected $rule = [
        'ap_id'  =>  '',
    ];
    protected $msg = [
        'ap_id'  =>  '',
    ];

}