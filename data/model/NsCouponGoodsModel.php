<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 优惠券商品表
 * @author Administrator
 *
 */
class NsCouponGoodsModel extends BaseModel {

    protected $table = 'ns_coupon_goods';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];
    /**
     * 获取对应优惠券类型的相关商品列表
     * @param unknown $coupon_type_id
     */
    public function getCouponTypeGoodsList($coupon_type_id)
    {
        $list = $this->alias('ncg')
                ->join('ns_goods ng','ncg.goods_id = ng.goods_id','left')
                ->field(' ncg.coupon_type_id, ncg.goods_id, ng.goods_name, ng.stock, ng.picture, ng.shop_id, ng.price')
                ->where(['coupon_type_id'=>$coupon_type_id])->select();
        return $list;    
    }

}