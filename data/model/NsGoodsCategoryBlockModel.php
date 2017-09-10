<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商城首页分类楼层模块
 * @author Administrator
 *
 */
class NsGoodsCategoryBlockModel extends BaseModel {

    protected $table = 'ns_goods_category_block';
    protected $rule = [
        'ad_picture'  =>  'no_html_parse'
    ];
    protected $msg = [
        'ad_picture'  =>  ''
    ];
    

}