<?php
namespace data\api;
/**
 * 商品品牌接口
 */
interface IGoodsBrand
{
    /**
     * 获取商品品牌列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    function getGoodsBrandList($page_index=1, $page_size=0, $condition = '', $order = '', $field = '*');
    
    /**
     * 添加或修改品牌
     * @param unknown $brand_id
     * @param unknown $shop_id
     * @param unknown $brand_name
     * @param unknown $brand_initial
     * @param unknown $brand_class
     * @param unknown $brand_pic
     * @param unknown $brand_recommend
     * @param unknown $sort
     * @param unknown $brand_category_name
     */
    function addOrUpdateGoodsBrand($brand_id, $shop_id, $brand_name, $brand_initial, $brand_class, $brand_pic, $brand_recommend, $sort, $brand_category_name = '', $category_id_array = '', $brand_ads, $category_name, $category_id_1, $category_id_2, $category_id_3);
    
    /**
     * 修改品牌排序号
     * @param unknown $brand_id
     * @param unknown $sort
     */
    function ModifyGoodsBrandSort($brand_id, $sort);
    
    /**
     * 修改品牌推荐
     * @param unknown $brand_id
     * @param unknown $brand_recommend
     */
    function ModifyGoodsBrandRecomend($brand_id, $brand_recommend);
    
    /**
     * 删除品牌
     * @param unknown $brand_id_array
     */
    function deleteGoodsBrand($brand_id_array);
}

?>