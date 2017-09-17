<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
use data\model\NsGoodsGroupModel as NsGoodsGroupModel;
use data\model\NsGoodsSkuModel as NsGoodsSkuModel;
/**
 * 商品表视图
 * @author Administrator
 *
 */
class NsGoodsViewModel extends BaseModel {

    protected $table = 'jc_cpwhb';
    
    /**
     * 获取列表返回数据格式
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return unknown
     */
    public function getGoodsViewList($page_index, $page_size, $condition, $order){
        $condition = "SELECT 产品编号,产品名称,全名,单价1 FROM jc_cpwhb WHERE LEFT(`产品编号`,7) = '$condition'";
        $queryList = $this->sqlQuery($condition);
        // $queryCount = $this->getGoodsrViewCount($condition);
        // $list = $this->setReturnList($queryList, $queryCount, $page_size);
        return $queryList;
    }
    /**
     * 查询商品的视图
     * @param unknown $condition
     * @param unknown $field
     * @param unknown $order
     * @return unknown
     */
    public function getGoodsViewQueryField($condition, $field, $order=""){
        $viewObj = $this->alias('ng')
            ->join('ns_goods_category ngc','ng.category_id = ngc.category_id','left')
            ->join('ns_goods_brand ngb','ng.brand_id = ngb.brand_id','left')
            ->join('sys_album_picture sap','ng.picture = sap.pic_id', 'left')
            ->field($field);
        $list = $viewObj->where($condition)
        ->order($order)
        ->select();
        return $list;
    }
    /**
     * 获取列表
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return \data\model\multitype:number
     */
    public function getGoodsViewQuery($page_index, $page_size, $condition, $order)
    {
        $list = $this->sqlQuery($condition);
        return $list;
    }
    /**
     * 获取列表数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getGoodsrViewCount($condition)
    {
        $viewObj = $this->alias('ng')
        ->join('ns_goods_category ngc','ng.category_id = ngc.category_id','left')
        ->join('ns_goods_brand ngb','ng.brand_id = ngb.brand_id','left')
        ->join('sys_album_picture sap','ng.picture = sap.pic_id', 'left')
        ->field('ng.goods_id');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }
}