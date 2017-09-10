-- 创建商品分类楼层表
CREATE TABLE ns_goods_category_block (
  id int(11) NOT NULL AUTO_INCREMENT,
  shop_id int(11) NOT NULL DEFAULT 0 COMMENT '实例id',
  category_name varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  category_id int(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  category_alias varchar(50) NOT NULL DEFAULT '' COMMENT '分类别名',
  color varchar(255) NOT NULL DEFAULT '#333333' COMMENT '颜色',
  is_show int(11) NOT NULL DEFAULT 1 COMMENT '是否显示 1显示 0 不显示',
  is_show_lower_category int(11) NOT NULL DEFAULT 0 COMMENT '是否显示下级分类',
  is_show_brand int(11) NOT NULL DEFAULT 0 COMMENT '是否显示品牌',
  sort int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  ad_picture varchar(255) NOT NULL DEFAULT '' COMMENT '广告图  {["title":"","subtitle":"","picture":"","url":"","background":""]}',
  create_time int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  modify_time int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 3
AVG_ROW_LENGTH = 8192
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '商品分类楼层表';


-- 同步分类楼层表数据
INSERT INTO ns_goods_category_block (category_id, category_name, category_alias) select category_id, category_name, category_name from ns_goods_category where pid = 0;


-- 相册图片表添加字段
alter TABLE sys_album_picture add upload_type int(11) DEFAULT 1 COMMENT '图片外链';
alter TABLE sys_album_picture add domain varchar(255) DEFAULT '' COMMENT '图片外链';
alter TABLE sys_album_picture add bucket  varchar(255) DEFAULT '' COMMENT '存储空间名称';

-- 模块的显示与隐藏
update sys_module set is_menu = 0 where  module_id in (390, 392);
insert into  sys_module (module_name, module, controller, method, pid, level, url, is_menu, is_control_auth, sort) 
values("附件上传", "admin", "config", "uploadtype", "218", "2", "config/uploadtype", "1", "1",10),
("商品楼层", "admin", "system", "goodscategoryblock", "477", "2", "system/goodscategoryblock", "1", "1",10);
DELETE FROM sys_module  WHERE method = 'customtemplatelist' AND url = 'config/customtemplatelist' AND controller = 'config';
DELETE FROM  sys_module WHERE method = 'customtemplate' AND url = 'config/customtemplate' AND controller = 'config';