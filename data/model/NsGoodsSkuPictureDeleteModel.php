<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商品sku图片备份表
 * @author Administrator
 *
 */
class NsGoodsSkuPictureDeleteModel extends BaseModel {

    protected $table = 'ns_goods_sku_picture_delete';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}