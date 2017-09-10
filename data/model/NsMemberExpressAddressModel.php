<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 会员物流地址管理
 *  id int(11) NOT NULL AUTO_INCREMENT,
  uid int(11) NOT NULL COMMENT '会员基本资料表ID',
  consigner varchar(255) NOT NULL DEFAULT '' COMMENT '收件人',
  mobile varchar(11) NOT NULL DEFAULT '' COMMENT '手机',
  phone varchar(20) NOT NULL DEFAULT '' COMMENT '固定电话',
  province int(11) NOT NULL DEFAULT 0 COMMENT '省',
  city int(11) NOT NULL DEFAULT 0 COMMENT '市',
  district int(11) NOT NULL DEFAULT 0 COMMENT '区县',
  address varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  zip_code varchar(6) NOT NULL DEFAULT '' COMMENT '邮编',
  alias varchar(50) NOT NULL DEFAULT '' COMMENT '地址别名',
  is_default bit(1) NOT NULL DEFAULT b'0' COMMENT '默认收货地址',
 * @author Administrator
 *
 */
class NsMemberExpressAddressModel extends BaseModel {
    protected $table = 'ns_member_express_address';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
}