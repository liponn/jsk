<?php
namespace app\api\controller;
use data\service\system\User  as userservice;

/**
 * 后台主界面
 * 
 * @author Administrator
 *        
 */
class Member extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    function personaldata(){
        $uid = $this->user->uid;
       // dump($this->user->uid);
        $shop_id = isset($this->user->instance_id) ? $this->user->instance_id : 0;
        $member = new userservice;
        $member_info = $member->getUserInfoByUid($uid);
        //var_dump($member_info);
        $this->assign('member_info',$member_info);
        //dump($shop_id);
        $this->assign('shop_id',$shop_id);
        return view($this->style . "/Member/personalData");
    }    
}
