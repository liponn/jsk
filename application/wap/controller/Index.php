<?php
namespace app\wap\controller;

use data\service\Goods;
use data\service\GoodsBrand as GoodsBrand;
use data\service\GoodsCategory;
use data\service\Member;
use data\service\Member as MemberService;
use data\service\Platform;
use data\service\promotion\PromoteRewardRule;

class Index extends BaseController
{

    /**
     * 平台端首页
     *
     */
    public function index()
    {
        //重定向到分类列表
        
    }
    public function index_old()
    {
        // exit("get in index");
        // 轮播图
        $plat_adv_list['adv_list'] = array(
                0 => array(
                    'adv_url' => "#",
                    'adv_image' => "public/static/images/jsk.jpg",
                    ),
            );
        $this->assign('plat_adv_list', $plat_adv_list);

        // 首页楼层版块 常用商品
        // $good_category = new GoodsCategory();

        $block_list = [
                        [
                        'goods_id' => 12,
                        'goods_name' => '三黄鸡（0.8-1.0）（原料）',
                        'pic_cover_mid' => 'upload/goods/sanhuangji.jpg',
                        'price' => '154.00',
                        ],
                        [
                        'goods_id' => 12,
                        'goods_name' => '分割鸡（1.0-以上）（原料）',
                        'pic_cover_mid' => 'upload/goods/default.jpg',
                        'price' => '154.00',
                        ],

                        [
                        'goods_id' => 12,
                        'goods_name' => '肉馅(原料)',
                        'pic_cover_mid' => 'upload/goods/default.jpg',
                        'price' => '14.00',
                        ]
        ];
        // var_dump($block_list);
        $this->assign('block_list', $block_list);

        // 获取当前时间
        $current_time = $this->getCurrentTime();
        $this->assign('ms_time', $current_time);
        
        // 公众号配置查询

        $wchat_config = [
            'value' => [
                'appid' => '',
                'appsecret' => '',
            ],
        ];
        $is_subscribe = 1; // 标识：是否显示顶部关注 0：[隐藏]，1：[显示]
                           // 检查是否配置过微信公众号
        if (! empty($wchat_config['value'])) {
            if (! empty($wchat_config['value']['appid']) && ! empty($wchat_config['value']['appsecret'])) {
                // 如何判断是否关注
                if (isWeixin()) {
                    if (! empty($this->uid)) {
                        // 检查当前用户是否关注
                        $user_sub = $this->user->checkUserIsSubscribeInstance($this->uid, $this->instance_id);
                        if ($user_sub == 0) {
                            // 未关注
                            $is_subscribe = 1;
                        }
                    }
                }
            }
        }
        $this->assign("is_subscribe", $is_subscribe);
        // 公众号二维码获取
        // $this->web_site = new WebSite();
        // $web_info = $this->web_site->getWebSiteInfo();
        // $this->assign('web_info', $web_info);
        $member = new MemberService();
        $source_user_name = "";
        $source_img_url = "";
        if (! empty($_GET['source_uid'])) {
            $_SESSION['source_uid'] = $_GET['source_uid'];
            $user_info = $member->getUserInfoByUid($_SESSION['source_uid']);
            if (! empty($user_info)) {
                $source_user_name = $user_info["nick_name"];
                if (! empty($user_info["user_headimg"])) {
                    $source_img_url = $user_info["user_headimg"];
                }
            }
        }

        $notice_arr = [
            'id' => 7,
            'shopid' => 0,
            'notice_message' => '家顺康微信营销系统',
            'is_enable' => 1,
        ];

        $this->assign('notice', $notice_arr);
        $this->assign('source_user_name', $source_user_name);
        $this->assign('source_img_url', $source_img_url);
        
        // $member = new Member();
        // $coupon_list = $member->getMemberCouponTypeList($this->instance_id, $this->uid);
        // $this->assign('coupon_list', $coupon_list);
        $this->assign("footer_check", 'home_check');//底部导航选中参数
        // 判断是否开启了自定义模块
        // 判断是否显示商品分类
        return view($this->style . 'Index/index');
        if (hook_is_exist("customtemplate")) {
            // 获取自定义模板信息
            return view($this->style . 'Index/customTemplateIndex');
        } else {}
    }

    /**
     * 得到当前时间戳的毫秒数
     * @return number
     */
    public function getCurrentTime()
    {
        $time = time();
        $time = $time * 1000;
        return $time;
    }

    
    /**
     * 设置页面打开cookie
     */
    public function setClientCookie()
    {
        $client = request()->post('client', '');
        setcookie('default_client', $client);
        // $cookie = request()->cookie('default_client', '');
        // return $cookie;
        return AjaxReturn(1);
    }

}
