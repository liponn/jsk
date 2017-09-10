<?php
namespace app\api\controller;
use data\service\Member;
use app\api\controller\BaseController;
/**
 * 注册
 * 
 * @author Administrator
 *        
 */
class Register extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //获取信息
        $user_name = !empty($_POST['user_name'])? $_POST['user_name']:'';
        $password = !empty($_POST['password'])? $_POST['password'] :'';
        //处理信息
        $member = new Member();
        $res = $member->registerMember($user_name, $password, '', '', '', '', '', '', '');    
        //返回信息
        if($res == -2004){
            return $this->outMessage('niu_index_response', $res, -50, '账号已被注册过');  
        }elseif($res == -2006 ){
            return $this->outMessage('niu_index_response', $res, -50, '用户名必须是英文字母或英文字母汉字组合');
        }else{
            return $this->outMessage('niu_index_response', $res);
        }
        
    }

    
}
