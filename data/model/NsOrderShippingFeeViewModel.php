<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 售卖区域表视图（运费模板）
 * @author Administrator
 *
 */
class NsOrderShippingFeeViewModel extends BaseModel {

    protected $table = 'ns_order_shipping_fee';
    /**
     * 获取列表返回数据格式
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return unknown
     */
    public function getViewList($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getViewQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getViewCount($condition);
        $list = $this->setReturnList($queryList, $queryCount, $page_size);
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
    public function getViewQuery($page_index, $page_size, $condition, $order)
    {
        //设置查询视图
        $viewObj = $this->alias('nosf')
        ->join('ns_order_shipping_fee_extend nosfe','nosf.shipping_fee_id = nosfe.shipping_fee_id','left')
        ->field('nosf.shipping_fee_name, nosf.shipping_fee_id, nosf.shop_id, nosf.create_time, nosf.update_time, nosfe.province_id, nosfe.city_id, nosfe.snum, nosfe.sprice, nosfe.xnum, nosfe.xprice');
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);
        return $list;
    }
    /**
     * 获取列表数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getViewCount($condition)
    {
         $viewObj = $this->alias('nosf')
        ->join('ns_order_shipping_fee_extend nosfe','nosf.shipping_fee_id = nosfe.shipping_fee_id','left')
        ->field('nosf.shipping_fee_name, nosf.shipping_fee_id, nosf.shop_id, nosf.create_time, nosf.update_time, nosfe.province_id, nosfe.city_id, nosfe.snum, nosfe.sprice, nosfe.xnum, nosfe.xprice');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }

}