<?php
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 平台说明内容表
 * @author Administrator
 *
 */
class NsPlatformHelpDocumentModel extends BaseModel {

    protected $table = 'ns_platform_help_document';
    protected $rule = [
        'id'  =>  '',
        'content'  =>  'no_html_parse',
    ];
    protected $msg = [
        'id'  =>  '',
        'content'  =>  '',
    ];
    /**
     * 获取列表返回数据格式
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
     * @return unknown
     */
    public function getPlatformHelpDocumentViewList($page_index, $page_size, $condition, $order){
    
        $queryList = $this->getPlatformHelpDocumentViewQuery($page_index, $page_size, $condition, $order);
        $queryCount = $this->getGoodsrViewCount($condition);
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
    public function getPlatformHelpDocumentViewQuery($page_index, $page_size, $condition, $order)
    {
        $viewObj = $this->alias('np')
        ->join('ns_platform_help_class nph','np.class_id = nph.class_id','left')
        ->field('np.id,np.uid,np.class_id,np.title,np.link_url,np.sort,np.content,np.image,np.create_time,np.modufy_time,nph.class_name');
        $list = $this->viewPageQuery($viewObj, $page_index, $page_size, $condition, $order);
        return $list;
    }

    /**
     * 获取列表数量
     * @param unknown $condition
     * @return \data\model\unknown
     */
    public function getGoodsrViewCount($condition)
    {
        $viewObj = $this->alias('np')
        ->join('ns_platform_help_class nph','np.class_id = nph.class_id','left')
        ->field('np.id,np.uid,np.class_id,np.title,np.link_url,np.sort,np.content,np.image,np.create_time,np.modufy_time,nph.class_name');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }
    

}