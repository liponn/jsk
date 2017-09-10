<?php
namespace app\api\controller;



use data\service\User as userservice;
use data\service\Member\MemberAccount;
use data\service\Member;
use data\service\promotion\PromoteRewardRule;

/**
 * 后台主界面
 *
 * @author Administrator
 *        
 */
class User extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // 获取信息
        $uid = ! empty($_POST['uid']) ? $_POST['uid'] : '';
        $instance_id = ! empty($_POST['instance_id']) ? $_POST['instance_id'] : '';
        // 处理信息
        $user = new userservice();
        $res = $user->getUserInfoByUid($uid);
        $member_account = new MemberAccount();
        // 积分
        $res['point'] = $member_account->getMemberPoint($uid, '');
        // 余额
        $res['balance'] = $member_account->getMemberBalance($uid);
        // 购物币
        $res['coin'] = $member_account->getMemberCoin($uid);
        // 是否签到
        $rewardRule = new Member();
        $is_signIn = $rewardRule->getIsMemberSign($uid, $instance_id);
        $res['is_signIn'] = $is_signIn;
        // dump($res);
        // $this->outMessage($user);
        // 返回信息
        if ($res) {
            return $this->outMessage('niu_index_response', $res);
        } else {
            return $this->outMessage('niu_index_response', $res, - 50, '失败！');
        }
    }

    public function signIn()
    {
        // 接收信息
        $uid = ! empty($_POST['uid']) ? $_POST['uid'] : '';
        $instance_id = ! empty($_POST['instance_id']) ? $_POST['instance_id'] : '';
        // 处理信息
        $rewardRule = new PromoteRewardRule();
        $res = $rewardRule->memberSign($uid, $instance_id);
        // 返回信息
        if ($res != 1) {
            return $this->outMessage('niu_index_response', $res);
        } else {
            return $this->outMessage('niu_index_response', $res, - 50, '失败！');
        }
    }
}
