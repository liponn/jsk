<?php
namespace app\wap\controller;

use data\extend\WchatOauth;
use data\extend\QyWchatOauth;
use data\service\Goods as GoodsService;
use data\service\Member as Member;
use data\service\Shop;
use data\service\Address;
use data\service\Config;
use data\service\User;
use data\service\WebSite as WebSite;
use data\service\Config as WebConfig;
use think\Controller;
use think\Session;
\think\Loader::addNamespace('data', 'data/');

class BaseController extends Controller
{

    public $user;

    protected $uid;

    protected $instance_id;

    protected $is_member;

    protected $shop_name;

    protected $user_name;

    protected $shop_id;

    /**
     * 平台logo
     *
     * @var unknown
     */
    protected $logo;

    public $web_site;

    public $style;

    public $app_login_name;

    public $app_login_password;

    public function __construct()
    {
        Session::delete('default_client');
        $this->app_login_name = ! empty($_GET['login_name']) ? $_GET['login_name'] : "";
        $this->app_login_password = ! empty($_GET['login_password']) ? $_GET['login_password'] : "";
        if (! empty($this->app_login_name) && ! empty($this->app_login_password)) {
            $this->user = new Member();
            $retval = $this->user->login($this->app_login_name, $this->app_login_password);
        }
        // $cookie = request()->cookie('default_client', '');
        
        // getWapCache();//开启缓存
        parent::__construct();
        $this->initInfo();
    }

    public function initInfo()
    {
        $this->user = new Member();
        // $this->web_site = new WebSite();
        // $web_info = $this->web_site->getWebSiteInfo();
        $web_info = [
            'website_id' => '1',
            'title' => 'jsk微信端',
            'logo' => 'public/static/images/adv_position/logo.png',
            'web_desc' => '',
            'key_words' => '',
            'web_icp' => '1111111',
            'style_id_pc' => '2',
            'web_address' => '北京市',
            'web_qrcode' => 'public/static/images/adv_position/qrcode.png',
            'web_url' => 'http://baidu.com',
            'web_email' => '331644741@qq.com',
            'web_phone' => '',
            'web_qq' => '331644741',
            'web_weixin' => 'echototo666',
            'web_status' => '1',
            'third_count' => '',
            'close_reason' => '对不起，维护中，大家敬请期待...',
            'wap_status' => '1',
            'style_id_admin' => '4',
            'create_time' => '1477452112',
            'modify_time' => '1498896658',
            'url_type' => '0',
        ];
        if ($web_info['wap_status'] == 2) {
            webClose($web_info['close_reason']);
        }

        $this->uid = $this->user->getSessionUid();
        $this->instance_id = $this->user->getSessionInstanceId();
        $this->shop_id = 0;
        $this->shop_name = $this->user->getInstanceName();
        $this->logo = $web_info['logo'];
        
        // $config = new Config();
        // // 使用那个手机模板
        // $use_wap_template = $config->getUseWapTemplate($this->instance_id);
        
        if (empty($use_wap_template)) {
            $use_wap_template['value'] = 'default';
        }
        $this->style = "wap/" . $use_wap_template['value'] . "/";
        $this->assign("style", "wap/" . $use_wap_template['value']);
        
        // 自定义模板信息
        if (! request()->isAjax()) {
            if (empty($this->uid)) {
                //微信登录时 会自动检测登录
                $this->wchatLogin();
            }
            $this->assign("uid", $this->uid);
            $this->assign("shop_id", $this->instance_id);
            $this->assign("title", $web_info['title']);
            $this->assign("platform_shopname", $this->shop_name); // 平台店铺名称
            // $seoconfig = $config->getSeoConfig($this->instance_id);print_r($seoconfig);exit;
            $seoconfig = [
                'seo_title' => '',
                'seo_meta' => '',
                'seo_desc' => '',
                'seo_other' => '',
            ];
            $this->assign("seoconfig", $seoconfig);
        }
        
        // $this->memberTelIsBind();
    }

    /**
     * 判断当前用户是否需要绑定
     */
    // public function memberTelIsBind()
    // {
    // // 获取登录用户手机号是否存在
    // $user = new User();
    // $webconfig = new WebConfig();
    // $uid = $this->uid;
    // $tel_is_bind = 0;
    // if (! empty($uid)) {
    // $is_tel = $user->getUserInfoByUid($uid);
    // // 注册与访问的配置
    // $register_and_visit = $webconfig->getRegisterAndVisit($this->shop_id);
    // $register_config = json_decode($register_and_visit['value'], true);
    // $notify_list = $webconfig->getMobileConfig($this->shop_id);
    // $this->assign("notify_list", $notify_list[0]);
    // if (! empty($is_tel) && isset($is_tel["user_tel"]) && empty($is_tel["user_tel"]) && $register_config["is_requiretel"] == 1) {
    // $tel_is_bind = 1;
    // }
    // }
    // $this->assign('tel_is_bind', $tel_is_bind);
    // }
    
    /**
     * 检测微信浏览器并且自动登录
     */
    public function wchatLogin()
    {
        
        // 微信浏览器自动登录
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            
            if (empty($_SESSION['request_url'])) {
                $_SESSION['request_url'] = request()->url(true);
            }
            // $config = new Config();
            // $wchat_config = $config->getInstanceWchatConfig(0);//获取 公众号  appid
            // if (empty($wchat_config['value']['appid'])) {
            //     return false;
            // }
            $wchat_oauth = new QyWchatOauth();
            // $domain_name = \think\Request::instance()->domain();
            // if (! empty($_COOKIE[$domain_name . "member_access_token"])) {
            //     $token = json_decode($_COOKIE[$domain_name . "member_access_token"], true);
            // } else {
            //     $token = $wchat_oauth->get_member_access_token();
            //     if (! empty($token['access_token'])) {
            //         setcookie($domain_name . "member_access_token", json_encode($token));
            //     }
            // }
            $token = $wchat_oauth->get_member_access_token();
            // var_dump($token);exit;
            if($token['errcode'] == 0){
                $wx_qy_userid = $token['UserId'];
                $retval = $this->user->wchatUnionLogin($wx_qy_userid);
                if ($retval == 1) {
                        //加入过企业id后的
                        $this->user->modifyUserWxhatLogin($token['openid'], $wx_unionid);
                    } elseif ($retval == USER_LOCK) {
                        $redirect = __URL(__URL__ . "/wap/login/userlock");
                        $this->redirect($redirect);
                    } elseif($retval == USER_NBUND) {
                        //注册企业userid
                        //获取企业用户信息
                        $info = $wchat_oauth->get_oauth_qy_member_info($token['UserId']);
                        header("Content-Type: text/html; charset=utf-8");
                        var_dump($info);exit;
                        $result = $this->user->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid);

                    } else {
                        $retval = $this->user->wchatLogin($token['openid']);
                        if ($retval == USER_NBUND) {
                            $info = $wchat_oauth->get_oauth_member_info($token);
                            $result = $this->user->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid);
                        } elseif ($retval == USER_LOCK) {
                            // 锁定跳转
                            $redirect = __URL(__URL__ . "/wap/login/userlock");
                            $this->redirect($redirect);
                        }
                    }
            }
            if (! empty($token['openid'])) {
                if (! empty($token['unionid'])) {
                    $wx_unionid = $token['unionid'];
                    $retval = $this->user->wchatUnionLogin($wx_unionid);
                    if ($retval == 1) {
                        $this->user->modifyUserWxhatLogin($token['openid'], $wx_unionid);
                    } elseif ($retval == USER_LOCK) {
                        $redirect = __URL(__URL__ . "/wap/login/userlock");
                        $this->redirect($redirect);
                    } else {
                        $retval = $this->user->wchatLogin($token['openid']);
                        if ($retval == USER_NBUND) {
                            $info = $wchat_oauth->get_oauth_member_info($token);
                            
                            $result = $this->user->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid);
                        } elseif ($retval == USER_LOCK) {
                            // 锁定跳转
                            $redirect = __URL(__URL__ . "/wap/login/userlock");
                            $this->redirect($redirect);
                        }
                    }
                } else {
                    $wx_unionid = '';
                    $retval = $this->user->wchatLogin($token['openid']);
                    if ($retval == USER_NBUND) {
                        $info = $wchat_oauth->get_oauth_member_info($token);
                        
                        $result = $this->user->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid);
                    } elseif ($retval == USER_LOCK) {
                        // 锁定跳转
                        $redirect = __URL(__URL__ . "/wap/login/userlock");
                        $this->redirect($redirect);
                    }
                }
                
                echo "<script language=JavaScript> window.location.href='" . $_SESSION['request_url'] . "'</script>";
                exit();
            }
        }
    }

    /**
     * 获取分享相关信息
     * 首页、商品详情、推广二维码、店铺二维码
     *
     * @return multitype:string unknown
     */
    public function getShareContents()
    {
        $shop_id = $this->shop_id;
        // 标识当前分享的类型[shop、goods、qrcode_shop、qrcode_my]
        $flag = isset($_POST["flag"]) ? $_POST["flag"] : "shop";
        $goods_id = isset($_POST["goods_id"]) ? $_POST["goods_id"] : "";
        
        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $this->logo; // 分享时，用到的logo，默认是平台logo
        $shop = new Shop();
        $config = $shop->getShopShareConfig($shop_id);
        
        // 当前用户名称
        $current_user = "";
        $user_info = null;
        if (empty($goods_id)) {
            switch ($flag) {
                case "shop":
                    if (! empty($this->uid)) {
                        
                        $user = new User();
                        $user_info = $user->getUserInfoByUid($this->uid);
                        $share_url = __URL__ . '/wap/index?source_uid=' . $this->uid;
                        $current_user = "分享人：" . $user_info["nick_name"];
                    } else {
                        $share_url = 'http://' . $_SERVER['HTTP_HOST'] . '/wap/index';
                    }
                    break;
                case "qrcode_shop":
                    
                    $user = new User();
                    $user_info = $user->getUserInfoByUid($this->uid);
                    $share_url = __URL__ . '/wap/Login/getshopqrcode?source_uid=' . $this->uid;
                    $current_user = "分享人：" . $user_info["nick_name"];
                    break;
                case "qrcode_my":
                    
                    $user = new User();
                    $user_info = $user->getUserInfoByUid($this->uid);
                    $share_url = __URL__ . '/wap/Login/getWchatQrcode?source_uid=' . $this->uid;
                    $current_user = "分享人：" . $user_info["nick_name"];
                    break;
            }
        } else {
            if (! empty($this->uid)) {
                $user = new User();
                $user_info = $user->getUserInfoByUid($this->uid);
                $share_url = __URL__ . '/wap/Goods/goodsDetail?id=' . $goods_id . '&source_uid=' . $this->uid;
                $current_user = "分享人：" . $user_info["nick_name"];
            } else {
                $share_url = __URL__ . '/wap/Goods/goodsDetail?id=' . $goods_id;
            }
        }
        
        // 店铺分享
        $shop_name = $this->shop_name;
        $share_content = array();
        switch ($flag) {
            case "shop":
                $share_content["share_title"] = $config["shop_param_1"] . $shop_name;
                $share_content["share_contents"] = $config["shop_param_2"] . " " . $config["shop_param_3"];
                $share_content['share_nick_name'] = $current_user;
                break;
            case "goods":
                // 商品分享
                $goods = new GoodsService();
                $goods_detail = $goods->getGoodsDetail($goods_id);
                $share_content["share_title"] = $goods_detail["goods_name"];
                $share_content["share_contents"] = $config["goods_param_1"] . "￥" . $goods_detail["price"] . ";" . $config["goods_param_2"];
                $share_content['share_nick_name'] = $current_user;
                if (count($goods_detail["img_list"]) > 0) {
                    $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $goods_detail["img_list"][0]["pic_cover_mid"]; // 用商品的第一个图片
                }
                break;
            case "qrcode_shop":
                // 二维码分享
                if (! empty($user_info)) {
                    $share_content["share_title"] = $shop_name . "二维码分享";
                    $share_content["share_contents"] = $config["qrcode_param_1"] . ";" . $config["qrcode_param_2"];
                    $share_content['share_nick_name'] = '分享人：' . $user_info["nick_name"];
                    if (! empty($user_info['user_headimg'])) {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $user_info['user_headimg'];
                    } else {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__TEMP__') . '/wap/' . NS_TEMPLATE . '/public/images/member_default.png';
                    }
                }
                break;
            case "qrcode_my":
                // 二维码分享
                if (! empty($user_info)) {
                    $share_content["share_title"] = $shop_name . "二维码分享";
                    $share_content["share_contents"] = $config["qrcode_param_1"] . ";" . $config["qrcode_param_2"];
                    $share_content['share_nick_name'] = '分享人：' . $user_info["nick_name"];
                    if (! empty($user_info['user_headimg'])) {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $user_info['user_headimg'];
                    } else {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__TEMP__') . '/wap/' . NS_TEMPLATE . '/public/images/member_default.png';
                    }
                }
                break;
        }
        $share_content["share_url"] = $share_url;
        $share_content["share_img"] = $share_logo;
        return $share_content;
    }

    /**
     * 获取分享相关票据
     */
    public function getShareTicket()
    {
        $config = new Config();
        $auth_info = $config->getInstanceWchatConfig($this->instance_id);
        // 获取票据
        if (isWeixin() && ! empty($auth_info['value']['appid'])) {
            // 针对单店版获取微信票据
            $wexin_auth = new WchatOauth();
            $signPackage['appId'] = $auth_info['value']['appid'];
            $signPackage['jsTimesTamp'] = time();
            $signPackage['jsNonceStr'] = $wexin_auth->get_nonce_str();
            $jsapi_ticket = $wexin_auth->jsapi_ticket();
            $signPackage['ticket'] = $jsapi_ticket;
            $url = request()->url(true);
            $Parameters = "jsapi_ticket=" . $signPackage['ticket'] . "&noncestr=" . $signPackage['jsNonceStr'] . "&timestamp=" . $signPackage['jsTimesTamp'] . "&url=" . $url;
            $signPackage['jsSignature'] = sha1($Parameters);
            return $signPackage;
        } else {
            $signPackage = array(
                'appId' => '',
                'jsTimesTamp' => '',
                'jsNonceStr' => '',
                'ticket' => '',
                'jsSignature' => ''
            );
            return $signPackage;
        }
    }

    /**
     * 获取省列表
     */
    public function getProvince()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        return $province_list;
    }

    /**
     * 获取城市列表
     *
     * @return Ambigous <multitype:\think\static , \think\false, \think\Collection, \think\db\false, PDOStatement, string, \PDOStatement, \think\db\mixed, boolean, unknown, \think\mixed, multitype:, array>
     */
    public function getCity()
    {
        $address = new Address();
        $province_id = isset($_POST['province_id']) ? $_POST['province_id'] : 0;
        $city_list = $address->getCityList($province_id);
        return $city_list;
    }

    /**
     * 获取区域地址
     */
    public function getDistrict()
    {
        $address = new Address();
        $city_id = isset($_POST['city_id']) ? $_POST['city_id'] : 0;
        $district_list = $address->getDistrictList($city_id);
        return $district_list;
    }
}