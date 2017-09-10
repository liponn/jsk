<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商品品牌表
 *    brand_id bigint(20) NOT NULL COMMENT '索引ID',
      shop_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '店铺ID',
      brand_name varchar(100) NOT NULL COMMENT '品牌名称',
      brand_initial varchar(1) NOT NULL COMMENT '品牌首字母',
      brand_pic varchar(100) NOT NULL DEFAULT '' COMMENT '图片',
      brand_recommend tinyint(1) NOT NULL DEFAULT 0 COMMENT '推荐，0为否，1为是，默认为0',
      sort tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
      brand_category_name varchar(50) NOT NULL DEFAULT '' COMMENT '类别名称',
      category_id_array varchar(1000) NOT NULL DEFAULT '' COMMENT '所属分类id',
      PRIMARY KEY (brand_id)
 * @author Administrator
 *
 */
class NsGoodsBrandModel extends BaseModel {

    protected $table = 'ns_goods_brand';
    protected $rule = [
        'brand_id'  =>  '',
    ];
    protected $msg = [
        'brand_id'  =>  '',
    ];

}