<?php
namespace data\service\Member;
/**
 * 会员流水账户
 */
use data\service\BaseService;
use data\model\NsCouponModel as NsCouponModel;
use data\model\NsConsultTypeModel;
use data\model\NsCouponTypeModel;
class MemberCoupon extends BaseService
{
    function __construct(){
        parent::__construct();
    }
    /**
     * 使用优惠券
     * @param unknown $uid
     * @param unknown $coupon_id
     */
    public function useCoupon($uid, $coupon_id, $order_id)
    {
        $coupon = new NsCouponModel();
        $data = array(
            'use_order_id' => $order_id,
            'state' => 2
        );
        $res = $coupon->save($data, ['coupon_id' => $coupon_id]);
        return $res;
    
    }
    /**
     * 用户获取优惠券
     * @param unknown $uid
     * $get_type获取方式
     * @param unknown $coupon_type_id
     */
    public function UserAchieveCoupon($uid, $coupon_type_id, $get_type)
    {
        $coupon = new NsCouponModel();
        $count = $coupon->where(['coupon_type_id'=>$coupon_type_id, 'uid'=> 0])->count();
        if($count > 0)
        {
            $data = array(
                'uid' => $uid,
                'state'=> 1,
                'get_type' => $get_type,
                'fetch_time' => time()
            );
            $retval = $coupon->where(['coupon_type_id'=>$coupon_type_id, 'uid'=> 0])->limit(1)->update($data);
        }else{
            $retval = NO_COUPON;
        }
        return $retval;
    
    }
    /**
     * 订单返还会员优惠券
     * @param unknown $coupon_id
     */
    public function UserReturnCoupon($coupon_id){
        $coupon = new NsCouponModel();
        $data = array(
            'state' => 1
    
        );
        $retval = $coupon->save($data,['coupon_id' => $coupon_id]);
        return $retval;
    }
    /**
     * 获取优惠券金额
     * @param unknown $coupon_id
     */
    public function getCouponMoney($coupon_id){
        $coupon = new NsCouponModel();
        $money = $coupon->getInfo(['coupon_id' => $coupon_id],'money');
        if(!empty($money['money']))
        {
            return $money['money'];
        }else{
            return 0;
        }
    }
    /**
     * 查询当前会员优惠券列表
     * @param unknown $type  1已领用（未使用） 2已使用 3已过期
     */
    public function getUserCouponList($type = '',$shop_id='')
    {
        $time = time();
        $condition['uid'] = $this->uid;
        switch ($type)
        {
            case 1:
                //未使用，已领用,未过期
               // $condition['start_time'] = array('ELT', $time);
                $condition['end_time'] = array('GT', $time);
                $condition['state'] = 1;
				break;
            case 2:
                //已使用
                $condition['state'] = 2;
				break;
            case 3:
                //$condition['end_time'] = array('ELT', $time);
                $condition['state'] = 3;
				break;
			default:
			    break;
        }
        if(!empty($shop_id)){
            $condition['shop_id'] = $shop_id;
        }
        $coupon = new NsCouponModel();
        $coupon_list = $coupon->getQuery($condition, '*', 'money desc');
        if(!empty($coupon_list))
        {
            $coupon_type_model = new NsCouponTypeModel();
            foreach ($coupon_list as $k => $v)
            {
                $type_info = $coupon_type_model->getInfo(['coupon_type_id' => $v['coupon_type_id']], 'coupon_name,at_least');
                $coupon_list[$k]['coupon_name'] = $type_info['coupon_name'];
                $coupon_list[$k]['at_least'] = $type_info['at_least'];
            }
        }
        
        return $coupon_list;
    }
}