<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商品赠品表
 * id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id ',
      gift_id int(10) UNSIGNED NOT NULL COMMENT '赠品id ',
      goods_id int(10) UNSIGNED NOT NULL COMMENT '商品id',
      goods_name varchar(50) NOT NULL COMMENT '商品名称',
      goods_picture varchar(100) NOT NULL COMMENT '商品图片',
 */
class NsPromotionGiftGoodsModel extends BaseModel {

    protected $table = 'ns_promotion_gift_goods';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
    

}