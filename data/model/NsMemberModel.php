<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 前台会员表
 * @author Administrator
 *
 */
class NsMemberModel extends BaseModel {
    protected $table = 'ns_member';
    protected $rule = [
        'uid'  =>  '',
    ];
    protected $msg = [
        'uid'  =>  '',
    ];

}
