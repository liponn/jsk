<?php

namespace data\model;
use think\Db;
use data\model\BaseModel as BaseModel;
/**
 * 用户日志模型
 */
class UserLogModel extends BaseModel {

    protected $table = '__SYS_USER_LOG__';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
    
}