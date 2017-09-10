<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 系统通知项
 */
class NoticeTemplateItemModel extends BaseModel {
    protected $table = 'sys_notice_template_item';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}