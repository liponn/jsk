<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 商品表
 * @author Administrator
 *
 */
class NsGoodsModel extends BaseModel {

    protected $table = 'jc_cpwhb';
    protected $rule = [
        'goods_id'  =>  '',
        'description'  =>  'no_html_parse',
        'goods_spec_format'  =>  'no_html_parse'
    ];
    protected $msg = [
        'goods_id'  =>  '',
        'description'  =>  '',
        'goods_spec_format'  =>  ''
    ];
    
    public function _getGoodsInfo($sku){
        $sql = "SELECT * FROM jc_cpwhb WHERE 产品编号 = '$sku'";
        $res = $this->sqlQuery($sql);
        return $res[0];
        // return $this->where(['产品编号' => $sku])->select();
    }
}