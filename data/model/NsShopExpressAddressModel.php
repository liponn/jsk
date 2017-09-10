<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 店铺物流公司地址表
 *    express_address_id int(11) NOT NULL AUTO_INCREMENT COMMENT '物流地址id',
      shop_id int(11) NOT NULL COMMENT '商铺id',
      contact varchar(50) NOT NULL DEFAULT '' COMMENT '联系人',
      mobile varchar(50) NOT NULL DEFAULT '' COMMENT '手机',
      phone varchar(50) NOT NULL DEFAULT '' COMMENT '电话',
      company_name varchar(100) NOT NULL DEFAULT '' COMMENT '公司名称',
      province smallint(6) NOT NULL DEFAULT 0 COMMENT '所在地省',
      city smallint(6) NOT NULL DEFAULT 0 COMMENT '所在地市',
      district smallint(6) NOT NULL DEFAULT 0 COMMENT '所在地区县',
      zipcode varchar(6) NOT NULL DEFAULT '' COMMENT '邮编',
      address varchar(100) NOT NULL DEFAULT '' COMMENT '详细地址',
      is_consigner tinyint(2) NOT NULL DEFAULT 0 COMMENT '发货地址标记',
      is_receiver tinyint(2) NOT NULL DEFAULT 0 COMMENT '收货地址标记',
      create_date datetime DEFAULT NULL COMMENT '创建日期',
      modify_date datetime DEFAULT NULL COMMENT '修改日期',
 * @author Administrator
 *
 */
class NsShopExpressAddressModel extends BaseModel {

    protected $table = 'ns_shop_express_address';
    protected $rule = [
        'express_address_id'  =>  '',
    ];
    protected $msg = [
        'express_address_id'  =>  '',
    ];

}