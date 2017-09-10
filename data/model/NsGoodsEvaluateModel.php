<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商品评价表
 * @author Administrator
 *
 */
class NsGoodsEvaluateModel extends BaseModel {

    protected $table = 'ns_goods_evaluate';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}