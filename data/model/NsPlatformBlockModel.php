<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 平台友情链接
 * @author Administrator
 *
 */
class NsPlatformBlockModel extends BaseModel {

    protected $table = 'ns_platform_block';
    protected $rule = [
        'block_id'  =>  '',
        'block_data'  =>  'no_html_parse',
    ];
    protected $msg = [
        'block_id'  =>  '',
        'block_data'  =>  '',
    ];

}