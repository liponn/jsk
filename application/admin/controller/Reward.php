<?php
namespace app\admin\controller;

use app\admin\controller\BaseController;
use data\service\promotion\PromoteRewardRule;
use data\service\Promotion;

/**
 *
 * @author Administrator
 *         奖励规则
 *        
 */
class Reward extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        switch (NS_VERSION) {
            case NS_VER_B2C:
                $retval = $this->IndexFx1(); // 单店基础版
                break;
            case NS_VER_B2C_FX:
                $retval = $this->IndexFx2(); // 单店分销版
                break;
        }
        return $retval;
    }

    /**
     * 单店基础版积分奖励
     */
    public function IndexFx1()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $sign_point = isset($_POST['sign_point']) ? $_POST['sign_point'] : 0;
            $share_point = isset($_POST['share_point']) ? $_POST['share_point'] : 0;
            $reg_member_self_point = isset($_POST['reg_member_self_point']) ? $_POST['reg_member_self_point'] : 0;
            $reg_member_one_point = 0;
            $reg_member_two_point = 0;
            $reg_member_three_point = 0;
            $reg_promoter_self_point = 0;
            $reg_promoter_one_point = 0;
            $reg_promoter_two_point = 0;
            $reg_promoter_three_point = 0;
            $reg_partner_self_point = 0;
            $reg_partner_one_point = 0;
            $reg_partner_two_point = 0;
            $reg_partner_three_point = 0;
            $rewardRule = new PromoteRewardRule();
            $res = $rewardRule->setPointRewardRule($shop_id, $sign_point, $share_point, $reg_member_self_point, $reg_member_one_point, $reg_member_two_point, $reg_member_three_point, $reg_promoter_self_point, $reg_promoter_one_point, $reg_promoter_two_point, $reg_promoter_three_point, $reg_partner_self_point, $reg_partner_one_point, $reg_partner_two_point, $reg_partner_three_point);
            return AjaxReturn($res);
        }
        $rewardRule = new PromoteRewardRule();
        $res = $rewardRule->getRewardRuleDetail($this->instance_id);
        $this->assign("res", $res);
        return view($this->style . "/Reward/index");
    }

    /**
     * 单店带分销版
     *
     * @return unknown[]
     */
    public function IndexFx2()
    {
        $child_menu_list = array(
            array(
                'url' => "Reward/index",
                'menu_name' => "积分奖励",
                "active" => 1
            ),
            array(
                'url' => "Reward/coupon",
                'menu_name' => "优惠劵奖励",
                "active" => 0
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $sign_point = isset($_POST['sign_point']) ? $_POST['sign_point'] : 0;
            $share_point = isset($_POST['share_point']) ? $_POST['share_point'] : 0;
            $reg_member_self_point = isset($_POST['reg_member_self_point']) ? $_POST['reg_member_self_point'] : 0;
            $reg_member_one_point = isset($_POST['reg_member_one_point']) ? $_POST['reg_member_one_point'] : 0;
            $reg_member_two_point = isset($_POST['reg_member_two_point']) ? $_POST['reg_member_two_point'] : 0;
            $reg_member_three_point = isset($_POST['reg_member_three_point']) ? $_POST['reg_member_three_point'] : 0;
            $reg_promoter_self_point = isset($_POST['reg_promoter_self_point']) ? $_POST['reg_promoter_self_point'] : 0;
            $reg_promoter_one_point = isset($_POST['reg_promoter_one_point']) ? $_POST['reg_promoter_one_point'] : 0;
            $reg_promoter_two_point = isset($_POST['reg_promoter_two_point']) ? $_POST['reg_promoter_two_point'] : 0;
            $reg_promoter_three_point = isset($_POST['reg_promoter_three_point']) ? $_POST['reg_promoter_three_point'] : 0;
            $reg_partner_self_point = isset($_POST['reg_partner_self_point']) ? $_POST['reg_partner_self_point'] : 0;
            $reg_partner_one_point = isset($_POST['reg_partner_one_point']) ? $_POST['reg_partner_one_point'] : 0;
            $reg_partner_two_point = isset($_POST['reg_partner_two_point']) ? $_POST['reg_partner_two_point'] : 0;
            $reg_partner_three_point = isset($_POST['reg_partner_three_point']) ? $_POST['reg_partner_three_point'] : 0;
            $rewardRule = new PromoteRewardRule();
            $res = $rewardRule->setPointRewardRule($shop_id, $sign_point, $share_point, $reg_member_self_point, $reg_member_one_point, $reg_member_two_point, $reg_member_three_point, $reg_promoter_self_point, $reg_promoter_one_point, $reg_promoter_two_point, $reg_promoter_three_point, $reg_partner_self_point, $reg_partner_one_point, $reg_partner_two_point, $reg_partner_three_point);
            return AjaxReturn($res);
        }
        $rewardRule = new PromoteRewardRule();
        $res = $rewardRule->getRewardRuleDetail($this->instance_id);
        $this->assign("res", $res);
        return view($this->style . "/Reward/indexFx");
    }

    /**
     * 优惠劵奖励
     */
    public function coupon()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $into_store_coupon = isset($_POST['into_store_coupon']) ? $_POST['into_store_coupon'] : 0;
            $share_coupon = isset($_POST['share_coupon']) ? $_POST['share_coupon'] : 0;
            $rewardRule = new PromoteRewardRule();
            $res = $rewardRule->setCouponRewardRule($shop_id, $into_store_coupon, $share_coupon);
            return AjaxReturn($res);
        }
        // 查询该店奖励规则
        $rewardRule = new PromoteRewardRule();
        $res = $rewardRule->getRewardRuleDetail($this->instance_id);
        $this->assign("res", $res);
        // 获取未过期的优惠劵
        $coupon = new Promotion();
        $condition['shop_id'] = $this->instance_id;
        $nowTime = date("Y-m-d H:i:s");
        $condition['end_time'] = array(
            ">",
            getTimeTurnTimeStamp($nowTime)
        );
        $list = $coupon->getCouponTypeList(1, 0, $condition);
        $this->assign("coupon", $list['data']);
        // 侧边导航
        return view($this->style . "/Reward/coupon");
    }
}
