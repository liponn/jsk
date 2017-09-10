<?php
namespace app\wap\controller;

use data\service\Platform;
use data\service\Article;

/**
 * 帮助中心
 * 创建人：李志伟
 * 创建时间：2017年2月17日20:12:50
 */
class Articlecenter extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 首页
     */
    public function index()
    {
        $document_id = isset($_POST['id']) ? $_POST['id'] : '';
        $article = new Article();
        $platform_help_class = $article->getArticleClassQuery();
        $this->assign('platform_help_class', $platform_help_class); // 文章一级分类列表
        
        if (empty($document_id)) {
            $help_document_info = array(
                'title' => '文章中心',
                'content' => "1、下完订单后在账户里看不见相关信息怎么办？<br/>您可能在{$this->shop_name}有多个账户，建议您核实一下当时下订单的具体账户，如有疑问您可致电客服400-99-00001，帮您核查。<br/>2、网站显示有赠品为何下单后没有收到赠品？<br/>赠品的配送是和您的收货地址有关的，若您在浏览商品时用的地址非最终的收货地址，有可能出现下单后没有赠品的情况；您所在的地址是否支持赠品配送，请以结算页面的购物明细为准，谢谢。;"
            );
            $this->assign('help_document_info', $help_document_info); // 文章详情
        } else {
            $help_document_info = $article->getArticleDetail($document_id);
            return $help_document_info;

        }
        
        return view($this->style . '/Articlecenter/index');
    }
    
    /**
     * 获取分类下文章列表
     */
    public function getArticleList(){
        $class_id = request()->post('class_id','');
        $article = new Article();
        $article_list =  $article->getArticleList(1, 0, ['nca.class_id'=>$class_id], 'nca.sort ASC');
        return $article_list;  
    }
    
    /**
     * 文章内容
     */
    public function articleContent(){
        $article_id = request()->get('article_id','');
        $article = new Article();
        $article_info = $article->getArticleDetail($article_id);
        $this->assign('article_info',$article_info);
        return view($this->style . '/Articlecenter/articleContent');
    }
}