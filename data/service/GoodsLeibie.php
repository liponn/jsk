<?php
namespace data\service;

use data\service\BaseService as BaseService;
use data\model\JsChanPinLeiBieModel as JsChanPinLeiBieModel;
use data\api\IGoodsCategory as IGoodsCategory;
//cop GoodsCategory
class GoodsLeibie extends BaseService implements IGoodsCategory 
{
    private $goods_leibie;
    function __construct(){
        parent:: __construct();
        $this->goods_leibie = new JsChanPinLeiBieModel();
    }


    function getGoodsCategoryList($page_index=1, $page_size=0, $condition = '', $order = '', $field = '*'){
    	// $list = $this->goods_leibie->pageQuery($page_index, $page_size, $condition, $order, $field);
    	$list = $this->goods_leibie->sqlQuery($condition);
        return $list;
    }
    
    /**
     * 获取商品分类的子分类(一级)
     * @param unknown $pid
     */
    function getGoodsCategoryListByParentId($pid){}
    
    /**
     * 添加或者修改商品分类信息
     * @param unknown $goods_classid  添加时$goods_classid=0
     * @param unknown $data
     */
    function addOrEditGoodsCategory($category_id, $category_name, $short_name, $pid, $is_visible, $keywords='', $description='', $sort=0, $category_pic, $attr_id=0, $attr_name=''){}
    
    /**
     * 删除商品分类信息
     * @param unknown $goods_classid_array
     */
    function deleteGoodsCategory($category_id){}
    
    /**
     * 获取商品分类树形列表
     * @param unknown $show_deep  深度
     * @param unknown $condition  条件
     */
    function getTreeCategoryList($show_deep,$condition){}
    
    /**
     * 获取分类关键词
     * @param unknown $goods_classid
     */
    function getKeyWords($category_id){}
    
    /**
     * 获取指定商品分类的详情
     * @param unknown $goods_classid
     */
    function getGoodsCategoryDetail($category_id){}
    
    /**
     * 获取分类级次
     * @param unknown $category_id
     */
    function getLevel($category_id){}
    
    /**
     * 获取分类名称
     * @param unknown $category_id
     */
    function getName($category_id){}
	
	/**
	 * 获取分类树，（暂时是查询两级）
	 */
	function getGoodsCategoryTree($pid){}
	
    /**
	 * 修改单个 字段
	 */
	function ModifyGoodsCategoryField($category_id, $field_name, $field_value){}
	/**
	 * 获取商品分类下的品牌列表
	 * @param unknown $category_id
	 */
	function getGoodsCategoryBrands($category_id){}
	/**
	 * 获取商品分类下的价格区间
	 * @param unknown $category_id
	 */
	function getGoodsCategoryPriceGrades($category_id){}
	/**
	 * 计算商品分类销量
	 */
	function getGoodsCategorySaleNum(){}
	/**
	 * 获取商品分类的子项列
	 * @param unknown $category_id
	 * @return string|unknown
	 */
	function getCategoryTreeList($category_id){}
	/**
	 * 获取分类的父级分类
	 * @param unknown $category_id
	 */
	function getCategoryParentQuery($category_id){}
	/**
	 * 得到上级分类信息
	 * @param unknown $category_id
	 */
	function getParentCategory($category_id){}
	/**
	 * 商品分类楼层配置信息
	 * @param unknown $shop_id
	 */
	function getGoodsCategoryBlock($shop_id){}
	/**
	 * 修改商品分类楼层配置
	*/
	function setGoodsCategoryBlock($id, $shop_id, $data){}
	/**
	 * 添加商品份分类楼层
	 * @param unknown $category_id
	*/
	function addGoodsCategoryBlock($category_id, $shop_id){}
	/**
	 * 商品分类楼层数据(pc)
	 */
	function getGoodsCategoryBlockList($shop_id){}
	/**
	 * 商品分类楼层数据(wap)
	 * @param unknown $shop_id
	 */
	function getGoodsCategoryBlockQuery($shop_id){}
	/**
	 * 删除分类商品楼层
	 * @param unknown $category_id
	 */
	function deleteGoodsCategoryBlock($category_id){}
}

?>