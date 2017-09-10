<?php

namespace data\model;

use data\model\BaseModel as BaseModel;

class NcCmsArticleViewModel extends BaseModel
{
    protected $table = 'nc_cms_article';
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
        $viewObj = $this->alias('nca')
        ->join('nc_cms_article_class ncac','nca.class_id = ncac.class_id','left')
        ->field('nca.article_id, nca.title, nca.class_id, nca.short_title, nca.source, nca.url, nca.author, nca.summary, nca.content, nca.image, nca.keyword, nca.article_id_array, nca.click, nca.sort, nca.commend_flag, nca.comment_flag, nca.status, nca.attachment_path, nca.tag, nca.comment_count, nca.last_comment_time, nca.share_count, nca.publisher_name, nca.uid, nca.public_time, nca.create_time, nca.modify_time, ncac.name');
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
        $viewObj = $this->alias('nca')
         ->join('nc_cms_article_class ncac','nca.class_id = ncac.class_id','left')
         ->field('nca.article_id');
        $count = $this->viewCount($viewObj,$condition);
        return $count;
    }
}

?>