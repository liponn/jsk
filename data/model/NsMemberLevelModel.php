<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 会员等级列表
 * @author Administrator
 *
 */
class NsMemberLevelModel extends BaseModel {
    protected $table = 'ns_member_level';
    protected $rule = [
        'level_id'  =>  '',
    ];
    protected $msg = [
        'level_id'  =>  '',
    ];
}