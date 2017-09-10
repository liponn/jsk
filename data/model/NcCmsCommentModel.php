<?php

namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * CMS评论表
 *  comment_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '评论编号',
  text varchar(2000) NOT NULL COMMENT '评论内容',
  uid int(10) UNSIGNED NOT NULL COMMENT '评论人编号',
  quote_comment_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评论引用',
  up int(10) UNSIGNED NOT NULL COMMENT '点赞数量',
  comment_time int(10) UNSIGNED NOT NULL COMMENT '评论时间',
 *
 */
class NcCmsCommentModel extends BaseModel{
    protected $table = 'nc_cms_comment';
    protected $rule = [
        'comment_id'  =>  '',
    ];
    protected $msg = [
        'comment_id'  =>  '',
    ];
}