<?php

namespace data\model;
use think\Db;
use data\model\BaseModel as BaseModel;
/**
 * 用户表
 */
class UserModel extends BaseModel {
    protected $table = 'sys_user';
    protected $rule = [
        'uid'  =>  '',
    ];
    protected $msg = [
        'uid'  =>  '',
    ];
 
}
