<?php
namespace app\api\controller;
use data\service\niushop\Pay\WeiXinPay;
/**
 * 后台主界面
 * 
 * @author Administrator
 *        
 */
class Pay extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //获取信息
        $out_trade_no = !empty($_POST['out_trade_no'])? $_POST['out_trade_no']:'';
        $weixin_pay = new WeiXinPay();
        //随机字符串
        $res['string'] = $weixin_pay->getNonceStr();
        $res['time'] = time();
        //dump($res);
        //返回信息
        if($res){
            return $this->outMessage('niu_index_response', $res);
        }else{
            return $this->outMessage('niu_index_response', $res, -50, '失败！');
        }
        
    }   
}
