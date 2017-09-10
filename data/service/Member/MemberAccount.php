<?php
namespace data\service\Member;
/**
 * 会员流水账户
 */
use data\service\BaseService;
use data\model\NsMemberAccountRecordsModel;
use data\model\NsMemberAccountModel;
use data\model\NsPointConfigModel;
use data\service\Config;
use data\model\ConfigModel;
use data\service\shopaccount\ShopAccount;
use data\service\User;
use think\Log;
class MemberAccount extends BaseService
{ 
    function __construct(){
        parent::__construct();
    }
    /**
     * 添加会员消费
     * @param unknown $shop_id
     * @param unknown $uid
     * @param unknown $consum
     */
    public function addMmemberConsum($shop_id, $uid, $consum){
        $account_statistics = new NsMemberAccountModel();
        $acount_info = $account_statistics->getInfo(['uid'=> $uid, 'shop_id' => $shop_id], '*');
        $data = array(
            'member_cunsum' => $acount_info['member_cunsum'] + $consum
        );
        $retval = $account_statistics->save($data, ['uid'=> $uid, 'shop_id' => $shop_id]);
        return $retval;
        
    }
	/**
     * 添加账户流水
     *  @param unknown $shop_id
     * @param unknown $account_type
     * @param unknown $uid
     * @param unknown $sign
     * @param unknown $number(+-)
     * @param unknown $from_type
     * @param unknown $data_id
     * @param unknown $text
     */
    public function addMemberAccountData($shop_id, $account_type, $uid, $sign, $number, $from_type, $data_id,$text)
    {
         if($account_type == 1)
        {
            $point_config = new NsPointConfigModel();
            $config_info = $point_config->getInfo(['shop_id' => $shop_id], 'is_open');
            /* if($config_info['is_open'] == 0)
            {
                //店铺关闭了积分兑换余额功能
                return CLOSE_POINT;
            } */
            
        } 
        
        
        $member_account = new NsMemberAccountRecordsModel();
        $member_account->startTrans();
        try{
        $data = array(
            'shop_id' => $shop_id,
            'account_type' => $account_type,
            'uid' => $uid,
            'sign' => $sign,
            'number' => $number,
            'from_type' => $from_type,
            'data_id' => $data_id,
            'text' => $text,
            'create_time' => time()
        );
        $retval = $member_account->save($data);
        //更新对应会员账户
            if($account_type == 1)
            {
                //积分
                $account = $member_account->where(['uid'=>$uid, 'shop_id' => $shop_id, 'account_type' => $account_type])->sum('number');
                if($account < 0)
                {
                    $member_account->rollback();
                    return LOW_POINT;
                }
                    
                $account_statistics = new NsMemberAccountModel();
                
              
                $count = $account_statistics->where(['uid'=> $uid, 'shop_id' => $shop_id])->count();
                if($count == 0)
                {
                    $data = array(
                        'uid' => $uid,
                         'shop_id' => $shop_id,
                         'point' => $account,
                         'member_sum_point' => $account
                    );
                    $account_statistics->save($data);
                }else{
                    $all_info = $account_statistics->getInfo(['uid'=> $uid, 'shop_id' => $shop_id], '*');
                    $data_member = array(
                        'point' => $account,
                       
                    );
                    if($number > 0)
                    {
                        //计算会员累计积分
                        $data_member['member_sum_point'] = $all_info['member_sum_point'] + $number;
                    }
                    $account_statistics->save($data_member,['uid'=> $uid, 'shop_id' => $shop_id]);
                }
                try {
                    $user_service=new User();
                    $user_service->updateUserLevel($shop_id, $uid);
                } catch (\Exception $e) {
                    Log::write($e->getMessage());
                }
            }
            if($account_type == 2)
            {
                
                //余额
              $account = $member_account->where(['uid'=>$uid, 'shop_id' => 0, 'account_type' => $account_type])->sum('number');
                if($account < 0)
                {
                    $member_account->rollback();
                    return LOW_BALANCE;
                }
                    
                $account_statistics = new NsMemberAccountModel();
                
              
                $count = $account_statistics->where(['uid'=> $uid, 'shop_id' => 0])->count();
                if($count == 0)
                {
                    $data = array(
                        'uid' => $uid,
                         'shop_id' => 0,
                         'balance' => $account
                    );
                    $account_statistics->save($data);
                }else{
                    $data_member = array(
                        'balance' => $account
                    );
                    $account_statistics->save($data_member,['uid'=> $uid, 'shop_id' => 0]);
                }
            }
            if($account_type == 3)
            {
                //购物币
                $account = $member_account->where(['uid'=>$uid, 'shop_id' => 0, 'account_type' => $account_type])->sum('number');
                if($account < 0)
                {
                    $member_account->rollback();
                    return LOW_COIN;
                }
            
                $account_statistics = new NsMemberAccountModel();
            
            
                $count = $account_statistics->where(['uid'=> $uid, 'shop_id' => 0])->count();
                if($count == 0)
                {
                    $data = array(
                        'uid' => $uid,
                        'shop_id' => 0,
                        'coin' => $account
                    );
                    $account_statistics->save($data);
                }else{
                    $data_member = array(
                        'coin' => $account
                    );
                    $account_statistics->save($data_member,['uid'=> $uid, 'shop_id' => 0]);
                }
            }
            $member_account->commit();
            return 1;
        } catch (\Exception $e)
        {
            $member_account->rollback();
            return $e->getMessage();
        }
        // TODO Auto-generated method stub
        
    }

	/**
     * 获取会员在一段时间之内的账户
     * @param unknown $uid
     * @param unknown $account_type
     * @param unknown $start_time
     * @param unknown $end_time
     */
    public function getMemberAccount($shop_id, $uid, $account_type, $start_time='', $end_time='')
    {
        $start_time = ($start_time == '') ? '2010-1-1' : $start_time;
        $end_time = ($end_time == '') ? 'now' : $end_time;
        $condition = array(
            'create_time' => array('EGT', getTimeTurnTimeStamp($start_time)),
            'create_time' => array('LT', getTimeTurnTimeStamp($end_time)),
            'account_type'=> $account_type,
            'uid'         => $uid,
            'shop_id'     => $shop_id
        );
        $member_account = new NsMemberAccountRecordsModel();
        $account = $member_account->where($condition)->sum('number');
        if(empty($account))
        {
            $account = 0;
        }
        return $account;
        // TODO Auto-generated method stub
        
    }

	 /**
     * 获取在一段时间之内用户的账户流水
     * @param unknown $uid
     * @param unknown $account_type
     * @param unknown $start_time
     * @param unknown $end_time
     */
    public function getMemberAccountList($shop_id, $uid, $account_type, $start_time, $end_time)
    {
        $start_time = ($start_time == '') ? '2010-1-1' : $start_time;
        $end_time = ($end_time == '') ? 'now' : $end_time;
        $condition = array(
            'create_time' => array('EGT', $start_time),
            'create_time' => array('LT', $end_time),
            'account_type'=> $account_type,
            'uid'         => $uid,
            'shop_id'     => $shop_id
        );
        $member_account = new NsMemberAccountRecordsModel();
        $list = $member_account->getQuery($condition, '*', 'create_time desc');
        return $list;
        // TODO Auto-generated method stub
        
    }
    /**
     * 积分转换成余额
     * @param unknown $point    积分
     * @param unknown $convert_rate 积分/余额
     */
    public function pointToBalance($point,$shop_id){
        $point_config = new NsPointConfigModel();
        $convert_rate = $point_config->getInfo(['shop_id'=>$shop_id, 'is_open'=>1],'convert_rate');
        if(!$convert_rate || $convert_rate == ''){
            $convert_rate = 0;
        }
//         var_dump($convert_rate);
        $balance = $point * $convert_rate["convert_rate"];
        return $balance;
    }
    /**
     * 获取兑换比例
     * @param unknown $shop_id 店铺名
     */
    public function getConvertRate($shop_id){
        $point_config = new NsPointConfigModel();
        $convert_rate = $point_config->getInfo(['shop_id'=>$shop_id, 'is_open'=>1],'convert_rate');
        return $convert_rate;
    }
    /**
     * 获取购物币余额转化关系
     */
    public function getCoinConvertRate(){
        $config = new ConfigModel();
        $config_rate = $config->getInfo(['key' => 'COIN_CONFIG'], '*');
        if(empty($config_rate))
        {
            return 1;
        }else{
            $rate = json_decode($config_rate['value'], true);
            return $rate['convert_rate'];
        }
    }
    /**
     * 获取会员余额数
     * @param unknown $uid
     */
    public function getMemberBalance($uid)
    {
        $member_account = new NsMemberAccountModel();
        $balance = $member_account->getInfo(['uid'=> $uid, 'shop_id' => 0], 'balance');
        if(!empty($balance))
        {
            return $balance['balance'];
        }else{
            return 0.00;
        }
    }
    /**
     * 获取会员购物币
     * @param unknown $uid
     * @return unknown|number
     */
    public function getMemberCoin($uid)
    {
        $member_account = new NsMemberAccountModel();
        $coin = $member_account->getInfo(['uid'=> $uid, 'shop_id' => 0], 'coin');
        if(!empty($coin))
        {
            return $coin['coin'];
        }else{
            return 0.00;
        }
    }
    public function getMemberPoint($uid, $shop_id='')
    {
        $member_account = new NsMemberAccountModel();
        if($shop_id == '')
        {
            //查询全部积分
            $point = $member_account->where(['uid'=> $uid])->sum('point');
            if(!empty($point))
            {
                return $point;
            }else{
                return 0;
            }
        }else{
            $point = $member_account->getInfo(['uid'=> $uid, 'shop_id' => $shop_id], 'point');
            if(!empty($point))
            {
                return $point['point'];
            }else{
                return 0;
            }
        }
    }
    public static function getMemberAccountRecordsName($from_type)
    {
        switch($from_type)
        {
       
                case 1:
                    $type_name = '商城订单';
                break;
                case 2:
                    $type_name = '订单退还';
                break;
                case 3:
                    $type_name = '兑换';
                    break;
                case 4:
                    $type_name = '充值';
                    break;
                case 5:
                    $type_name = '签到';
                    break;
                    
                case 6:
                    $type_name = '分享';
                    break;
                case 7:
                    $type_name = '注册';
                    break;
                case 8:
                    $type_name = '提现';
                    break;
                case 9:
                    $type_name = '提现退还';
                    break;
                case 10:
                    $type_name = '调整';
                    break;
               case 19:
                    $type_name = '点赞';
                    break;
               case 20:
                    $type_name = '评论';
                    break;
                default:
                    $type_name = '';
                    break;
        }
        return $type_name;

    }
    /**
     * 余额兑换为积分
     * @param unknown $balance  余额
     * @param unknown $convert_rate 余额/积分
     */
 /*    public function balanceToPoint($balance,$shop_id){
        $point_config = new NsPointConfigModel();
        $convert_rate = $point_config->getInfo(['shop_id'=>$shop_id, 'is_open'=>1],'convert_rate');
        if(!$convert_rate || $convert_rate == ''){
            $convert_rate = 0;
        }
        //         var_dump($convert_rate);
        $point  = $balance / $convert_rate["convert_rate"];
        return $point;
    } */
}