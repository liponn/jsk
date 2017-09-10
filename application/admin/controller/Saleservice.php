<?php
namespace app\admin\controller;

use data\service\Goods;

/**
 * 咨询控制器
 */
class Saleservice extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 咨询 管理
     */
    public function consultList()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $type = request()->post('type', '');
            $ct_id = request()->post('ct_id', '');
            $start_date = request()->post('start_date') == '' ? 0 : getTimeTurnTimeStamp(request()->post('start_date'));
            $end_date = request()->post('end_date') == '' ? 0 : getTimeTurnTimeStamp(request()->post('end_date'));
            $goods_name = request()->post('goods_name', '');
            $condition = array();
            if (! empty($ct_id)) {
                $condition['ct_id'] = $ct_id;
            }
            if ($type == 'to_reply') {
                $condition['consult_reply'] = '';
            } else 
                if ($type == 'replied') {
                    $condition['consult_reply'] = array(
                        'NEQ',
                        ''
                    );
                }
            if($start_date != 0 && $end_date != 0){
                $condition["consult_addtime"] = [
                    [
                        ">",
                        $start_date
                    ],
                    [
                        "<",
                        $end_date
                    ]
                ];
            }elseif($start_date != 0 && $end_date == 0){
                $condition["consult_addtime"] = [
                    [
                        ">",
                        $start_date
                    ]
                ];
            }elseif($start_date == 0 && $end_date != 0){
                $condition["consult_addtime"] = [
                    [
                        "<",
                        $end_date
                    ]
                ];
            }
            if (! empty($goods_name)) {
                $condition["goods_name"] = array(
                    "like",
                    "%" . $goods_name . "%"
                );
            }
            $goods = new Goods();
            $list = $goods->getConsultList($page_index, $page_size, $condition, 'consult_addtime desc');
            return $list;
        }
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $child_menu_list = array();
        $child_menu_list[0] = array(
            'url' => "Saleservice/consultList",
            'menu_name' => '全部咨询',
            "active" => $type == '' ? 1 : 0
        );
        $child_menu_list[1] = array(
            'url' => "Saleservice/consultList?type=to_reply",
            'menu_name' => '未回复咨询',
            "active" => $type == 'to_reply' ? 1 : 0
        );
        $child_menu_list[2] = array(
            'url' => "Saleservice/consultList?type=replied",
            'menu_name' => '已回复咨询',
            "active" => $type == 'replied' ? 1 : 0
        );
        $goods = new Goods();
        $ct_list = $goods->getConsultTypeList();
        $this->assign('ct_list', $ct_list['data']);
        $this->assign('type', $type);
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . 'Saleservice/consultList');
    }

    /**
     * 咨询管理后台 回复
     */
    public function replyConsultAjax()
    {
        $consult_id = isset($_POST['consult_id']) ? $_POST['consult_id'] : '';
        $consult_reply = isset($_POST['consult_reply']) ? $_POST['consult_reply'] : '';
        $consult = new Goods();
        $res = $consult->replyConsult($consult_id, $consult_reply);
        return AjaxReturn($res);
    }

    /**
     * 删除 咨询
     */
    public function deleteConsult()
    {
        $consult_id = isset($_POST['consult_id']) ? $_POST['consult_id'] : '';
        $consult = new Goods();
        $res = $consult->deleteConsult($consult_id);
        return AjaxReturn($res);
    }
}