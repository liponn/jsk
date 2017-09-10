<?php

namespace data\model;
use data\model\BaseModel as BaseModel;
/**
 * 版本补丁
 *
 */
class VersionPatchModel extends BaseModel {
    protected $table = 'sys_version_patch';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}
