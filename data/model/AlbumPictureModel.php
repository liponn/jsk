<?php

namespace data\model;
use think\Db;
use data\model\BaseModel as BaseModel;
/**
 * 图片model
 */
class AlbumPictureModel extends BaseModel {

    protected $table = 'sys_album_picture';
    protected $rule = [
        'pic_id'  =>  '', 
        'pic_tag'  =>  'no_html_parse',
        'pic_name'  =>  'no_html_parse',
        'pic_cover'  =>  'no_html_parse',
        'pic_cover_big'  =>  'no_html_parse',
        'pic_cover_mid'  =>  'no_html_parse',
        'pic_cover_small'  =>  'no_html_parse',
        'pic_cover_micro'  =>  'no_html_parse'
    ];
    protected $msg = [
        'pic_id'  =>  '',
        'pic_tag'  =>  '',
        'pic_name'  =>  '',
        'pic_cover'  =>  '',
        'pic_cover_big'  =>  '',
        'pic_cover_mid'  =>  '',
        'pic_cover_small'  =>  '',
        'pic_cover_micro'  =>  ''
    ];
}