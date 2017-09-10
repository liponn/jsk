<?php
namespace data\model;

use data\model\BaseModel as BaseModel;

class NsConsultModel extends BaseModel {

    protected $table = 'ns_consult';
    protected $rule = [
        'consult_id'  =>  '',
    ];
    protected $msg = [
        'consult_id'  =>  '',
    ];

}