<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 购物车
 *  cart_id int(11) NOT NULL AUTO_INCREMENT COMMENT '购物车id',
  buyer_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '买家id',
  shop_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '店铺id',
  shop_name varchar(100) NOT NULL DEFAULT '' COMMENT '店铺名称',
  goods_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品id',
  goods_name varchar(200) NOT NULL COMMENT '商品名称',
  sku_id int(11) NOT NULL DEFAULT 0 COMMENT '商品的skuid',
  sku_name varchar(200) NOT NULL DEFAULT '' COMMENT '商品的sku名称',
  price decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '商品价格',
  num smallint(5) UNSIGNED NOT NULL DEFAULT 1 COMMENT '购买商品数量',
  goods_picture int(11) NOT NULL DEFAULT 0 COMMENT '商品图片',
  bl_id mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '组合套装ID',
  PRIMARY KEY (cart_id),
 * @author Administrator
 *
 */
class NsCartModel extends BaseModel {

    protected $table = 'dd_cart';
    protected $rule = [
        'cart_id'  =>  '',
    ];
    protected $msg = [
        'cart_id'  =>  '',
    ];

}