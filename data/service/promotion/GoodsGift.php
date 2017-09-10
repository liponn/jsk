<?php
namespace data\service\promotion;
use data\service\BaseService;
use data\model\NsPromotionGiftGoodsModel;
use data\model\AlbumPictureModel;
/**
 * 商品总赠品管理
 */
class GoodsGift extends BaseService{
    /**
     * 查询赠品的商品信息
     * @param unknown $gift_id
     */
    public function getGiftGoodsInfo($gift_id)
    {
        $gift = new NsPromotionGiftGoodsModel();
        $goods_info = $gift->getInfo(['gift_id' => $gift_id], 'goods_id,goods_name,goods_picture');
        $picture = new AlbumPictureModel();
        $picture_info = $picture->getInfo(['pic_id' => $goods_info['goods_picture']], '*');
        $goods_info['picture_info'] = $picture_info;
        return $goods_info;
    }
    public function userAchieveGift($uid, $gift_id, $order){
        
    }
}