<?php
namespace app\wap\controller;

use data\service\Platform;

/**
 * 帮助中心
 * 创建人：李志伟
 * 创建时间：2017年2月17日20:12:50
 */
class Helpcenter extends BaseController
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
        // $class_id = isset($_GET['class_id'])?$_GET['class_id']:'';
        $platform = new Platform();
        $platform_help_class = $platform->getPlatformHelpClassList(1, 0, '', 'sort');
        $this->assign('platform_help_class', $platform_help_class['data']); // 帮助中心分类列表
        
        $platform_help_document = $platform->getPlatformHelpDocumentList(1, 0, '', 'sort');
        $this->assign('platform_help_document', $platform_help_document['data']); // 帮助中心列表
        if (empty($document_id)) {
            $help_document_info = array(
                'title' => '帮助中心',
                'content' => "1、下完订单后在账户里看不见相关信息怎么办？<br/>您可能在{$this->shop_name}有多个账户，建议您核实一下当时下订单的具体账户，如有疑问您可致电客服400-99-00001，帮您核查。<br/>2、网站显示有赠品为何下单后没有收到赠品？<br/>赠品的配送是和您的收货地址有关的，若您在浏览商品时用的地址非最终的收货地址，有可能出现下单后没有赠品的情况；您所在的地址是否支持赠品配送，请以结算页面的购物明细为准，谢谢。;"
            );
            $this->assign('help_document_info', $help_document_info); // 帮助中心信息详情
        } else {
            $help_document_info = $platform->getPlatformHelpDocumentList(1, 0, [
                'id' => $document_id
            ], 'sort');
            return $help_document_info;
            // dump($help_document_info['data']);
            // $this->assign('help_document_info',$help_document_info['data'][0]); //帮助中心信息详情
            // $this->assign('class_id',$help_document_info['data'][0]['class_id']);
        }
        // $this->assign('document_id',$document_id);
        // $this->assign('class_id',$class_id);
        return view($this->style . '/Helpcenter/index');
    }
}