<?php
namespace app\admin\controller;

use app\api\controller\User;
use data\extend\database;
use data\extend\Send;
use data\service\Address as DataAddress;
use data\service\Config as WebConfig;
use data\service\GoodsCategory;
use data\service\Platform;
use data\service\Promotion;
use data\service\Shop as Shop;
use think\Config as thinkConfig;
use Qiniu\json_decode;

/**
 * 网站设置模块控制器
 *
 * @author Administrator
 *        
 */
class Config extends BaseController
{

    public $backup_path = "runtime/dbsql/";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 网站设置
     */
    public function webConfig()
    {
        if ($_POST) {
            // 网站设置
            $title = $_POST['title']; // 网站标题
            $logo = $_POST['logo']; // 网站logo
            $web_desc = $_POST['web_desc']; // 网站描述
            $key_words = $_POST['key_words']; // 网站关键字
            $web_icp = $_POST['web_icp']; // 网站备案号
            $web_style_pc = $_POST['web_style_pc']; // 前台网站风格
            $web_style_admin = $_POST['web_style_admin']; // 后台网站风格
            $visit_pattern = request()->post('visit_pattern', '');
            $web_qrcode = $_POST['web_qrcode']; // 网站公众号二维码
            $web_url = $_POST['web_url']; // 店铺网址
            $web_phone = $_POST['web_phone']; // 网站联系方式
            $web_email = $_POST['web_email']; // 网站邮箱
            $web_qq = $_POST['web_qq']; // 网站qq号
            $web_weixin = $_POST['web_weixin']; // 网站微信号
            $web_address = $_POST['web_address']; // 网站联系地址
            
            $web_status = request()->post("web_status", ''); // 网站运营状态
            $wap_status = request()->post("wap_status", ''); // 手机端网站运营状态
            $third_count = request()->post("third_count", ''); // 第三方统计
            $close_reason = request()->post("close_reason", ''); // 站点关闭原因
            
            $retval = $this->website->updateWebSite($title, $logo, $web_desc, $key_words, $web_icp, $web_style_pc, $web_style_admin, $visit_pattern, $web_qrcode, $web_url, $web_phone, $web_email, $web_qq, $web_weixin, $web_address, $web_status, $wap_status, $third_count, $close_reason);
            return AjaxReturn($retval);
        } else {
            
            $child_menu_list = array(
                array(
                    'url' => "config/webconfig",
                    'menu_name' => "网站设置",
                    "active" => 1
                ),
                array(
                    'url' => "config/seoConfig",
                    'menu_name' => "SEO设置",
                    "active" => 0
                ),
                array(
                    'url' => "config/codeconfig",
                    'menu_name' => "验证码设置",
                    "active" => 0
                ),
                array(
                    'url' => "config/shopset",
                    'menu_name' => "购物设置",
                    "active" => 0
                ),
                array(
                    'url' => "config/expressmessage",
                    'menu_name' => "物流跟踪设置",
                    "active" => 0
                ),
                array(
                    'url' => "config/copyrightinfo",
                    'menu_name' => "版权设置",
                    "active" => 0
                )
            );
            
            $this->assign('child_menu_list', $child_menu_list);
            $list = $this->website->getWebSiteInfo();
            $style_list_pc = $this->website->getWebStyleList([
                'type' => 1
            ]); // 前台网站风格
            $style_list_admin = $this->website->getWebStyleList([
                'type' => 2
            ]); // 后台网站风格
            $path = "";
            $path = getQRcode(thinkConfig::get('view_replace_str.APP_MAIN'), 'upload/qrcode', 'url');
            $this->assign('style_list_pc', $style_list_pc);
            $this->assign('style_list_admin', $style_list_admin);
            $this->assign("website", $list);
            $this->assign("qrcode_path", $path);
            return view($this->style . "Config/webConfig");
        }
    }

    /**
     * seo设置
     */
    public function seoConfig()
    {
        $child_menu_list = array(
            array(
                'url' => "config/webconfig",
                'menu_name' => "网站设置",
                "active" => 0
            ),
            array(
                'url' => "config/seoConfig",
                'menu_name' => "SEO设置",
                "active" => 1
            ),
            array(
                'url' => "config/codeconfig",
                'menu_name' => "验证码设置",
                "active" => 0
            ),
            array(
                'url' => "config/shopset",
                'menu_name' => "购物设置",
                "active" => 0
            ),
            array(
                'url' => "config/expressmessage",
                'menu_name' => "物流跟踪设置",
                "active" => 0
            ),
            array(
                'url' => "config/copyrightinfo",
                'menu_name' => "版权设置",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        
        $Config = new WebConfig();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $seo_title = request()->post("seo_title", '');
            $seo_meta = request()->post("seo_meta", '');
            $seo_desc = request()->post("seo_desc", '');
            $seo_other = request()->post("seo_other", '');
            $retval = $Config->SetSeoConfig($shop_id, $seo_title, $seo_meta, $seo_desc, $seo_other);
            return AjaxReturn($retval);
        } else {
            $shop_id = $this->instance_id;
            $shopSet = $Config->getSeoConfig($shop_id);
            $this->assign("info", $shopSet);
        }
        return view($this->style . "Config/seoConfig");
    }

    /**
     * 版权设置
     */
    public function copyrightinfo()
    {
        $child_menu_list = array(
            array(
                'url' => "config/webconfig",
                'menu_name' => "网站设置",
                "active" => 0
            ),
            array(
                'url' => "config/seoConfig",
                'menu_name' => "SEO设置",
                "active" => 0
            ),
            array(
                'url' => "config/codeconfig",
                'menu_name' => "验证码设置",
                "active" => 0
            ),
            array(
                'url' => "config/shopset",
                'menu_name' => "购物设置",
                "active" => 0
            ),
            array(
                'url' => "config/expressmessage",
                'menu_name' => "物流跟踪设置",
                "active" => 0
            ),
            array(
                'url' => "config/copyrightinfo",
                'menu_name' => "版权设置",
                "active" => 1
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        $Config = new WebConfig();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $copyright_logo = request()->post("copyright_logo", '');
            $copyright_meta = request()->post("copyright_meta", '');
            $copyright_link = request()->post("copyright_link", '');
            $copyright_desc = request()->post("copyright_desc", '');
            $copyright_companyname = request()->post("copyright_companyname", '');
            $retval = $Config->SetCopyrightConfig($shop_id, $copyright_logo, $copyright_meta, $copyright_link, $copyright_desc, $copyright_companyname);
            return AjaxReturn($retval);
        } else {
            $shop_id = $this->instance_id;
            $shopSet = $Config->getCopyrightConfig($shop_id);
            $this->assign("info", $shopSet);
        }
        
        return view($this->style . "Config/copyrightinfo");
    }

    /**
     * qq登录配置
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function loginQQConfig()
    {
        $appkey = isset($_POST['appkey']) ? $_POST['appkey'] : '';
        $appsecret = isset($_POST['appsecret']) ? $_POST['appsecret'] : '';
        $url = isset($_POST['url']) ? $_POST['url'] : '';
        $call_back_url = isset($_POST['call_back_url']) ? $_POST['call_back_url'] : '';
        $is_use = isset($_POST['is_use']) ? $_POST['is_use'] : 0;
        $web_config = new WebConfig();
        // 获取数据
        $retval = $web_config->setQQConfig($this->instance_id, $appkey, $appsecret, $url, $call_back_url, $is_use);
        return AjaxReturn($retval);
    }

    /**
     * 微信登录配置
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function loginWeixinConfig()
    {
        $appid = isset($_POST['appkey']) ? $_POST['appkey'] : '';
        $appsecret = isset($_POST['appsecret']) ? $_POST['appsecret'] : '';
        $url = isset($_POST['url']) ? $_POST['url'] : '';
        $call_back_url = isset($_POST['call_back_url']) ? $_POST['call_back_url'] : '';
        $is_use = isset($_POST['is_use']) ? $_POST['is_use'] : 0;
        $web_config = new WebConfig();
        // 获取数据
        $retval = $web_config->setWchatConfig($this->instance_id, $appid, $appsecret, $url, $call_back_url, $is_use);
        return AjaxReturn($retval);
    }

    /**
     * 第三方登录 页面显示
     */
    public function loginConfig()
    {
        $type = isset($_GET["type"]) ? $_GET["type"] : "qq";
        if ($type == "qq") {
            $child_menu_list = array(
                array(
                    'url' => "config/loginconfig?type=qq",
                    'menu_name' => "QQ登录",
                    "active" => 1
                )
            );
        } else {
            $child_menu_list = array(
                array(
                    'url' => "config/loginconfig?type=wchat",
                    'menu_name' => "微信登录",
                    "active" => 1
                )
            );
        }
        $this->assign('child_menu_list', $child_menu_list);
        $this->assign("type", $type);
        $web_config = new WebConfig();
        // qq登录配置
        // 获取当前域名
        $domain_name = \think\Request::instance()->domain();
        // 获取回调域名qq回调域名
        $qq_call_back = __URL(__URL__ . '/wap/login/callback');
        // 获取qq配置信息
        $qq_config = $web_config->getQQConfig($this->instance_id);
        $qq_config['value']["AUTHORIZE"] = $domain_name;
        $qq_config['value']["CALLBACK"] = $qq_call_back;
        $this->assign("qq_config", $qq_config);
        // 微信登录配置
        // 微信登录返回
        $wchat_call_back = __URL(__URL__ . '/wap/login/callback');
        $wchat_config = $web_config->getWchatConfig($this->instance_id);
        $wchat_config['value']["AUTHORIZE"] = $domain_name;
        $wchat_config['value']["CALLBACK"] = $wchat_call_back;
        $this->assign("wchat_config", $wchat_config);
        
        return view($this->style . "Config/loginConfig");
    }

    /**
     * 支付配置--微信
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function payConfig()
    {
        if (request()->isAjax()) {
            $type = isset($_POST['type']) ? $_POST['type'] : '';
            if ($type == 'wchat') {
                // 微信支付
                $appkey = str_replace(' ', '', request()->post('appkey', ''));
                $appsecret = str_replace(' ', '', request()->post('appsecret', ''));
                $paySignKey = str_replace(' ', '', request()->post('paySignKey', ''));
                $MCHID = str_replace(' ', '', request()->post('MCHID', ''));
                $is_use = isset($_POST['is_use']) ? $_POST['is_use'] : 0;
                $web_config = new WebConfig();
                // 获取数据
                $retval = $web_config->setWpayConfig($this->instance_id, $appkey, $appsecret, $MCHID, $paySignKey, $is_use);
                return AjaxReturn($retval);
            }
        } else {
            $type = isset($_GET['type']) ? $_GET['type'] : 'wchat';
            if ($type == 'wchat') {
                $web_config = new WebConfig();
                $data = $web_config->getWpayConfig($this->instance_id);
                $this->assign("config", $data);
                // 获取当前域名
                $this->assign('root_url', __URL(__URL__ . "/wap/pay/"));
                return view($this->style . "Config/payConfig");
            }
        }
    }

    /**
     * 支付宝配置
     */
    public function payAliConfig()
    {
        if (request()->isAjax()) {
            // 支付宝
            $partnerid = str_replace(' ', '', request()->post('partnerid', ''));
            $seller = str_replace(' ', '', request()->post('seller', ''));
            $ali_key = str_replace(' ', '', request()->post('ali_key', ''));
            $is_use = isset($_POST['is_use']) ? $_POST['is_use'] : 0;
            $web_config = new WebConfig();
            // 获取数据
            $retval = $web_config->setAlipayConfig($this->instance_id, $partnerid, $seller, $ali_key, $is_use);
            return AjaxReturn($retval);
        }
        $web_config = new WebConfig();
        $data = $web_config->getAlipayConfig($this->instance_id);
        $this->assign("config", $data);
        return view($this->style . "Config/payAliConfig");
    }

    /**
     * 设置微信和支付宝开关状态是否启用
     */
    public function setStatus()
    {
        $web_config = new WebConfig();
        if (request()->isAjax()) {
            $is_use = request()->post("is_use", '');
            $type = request()->post("type", '');
            $retval = $web_config->setWpayStatusConfig($this->instance_id, $is_use, $type);
            return AjaxReturn($retval);
        }
    }

    /**
     * 广告列表
     */
    public function shopAdList()
    {
        if (request()->isAjax()) {
            $shop_ad = new Shop();
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $list = $shop_ad->getShopAdList($page_index, $page_size, [
                'shop_id' => $this->instance_id
            ], 'sort');
            return $list;
        }
        return view($this->style . "Config/shopAdList");
    }

    /**
     * 添加店铺广告
     *
     * @return \think\response\View
     */
    public function addShopAd()
    {
        if (request()->isAjax()) {
            $ad_image = isset($_POST['ad_image']) ? $_POST['ad_image'] : '';
            $link_url = isset($_POST['link_url']) ? $_POST['link_url'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : 0;
            $type = isset($_POST['type']) ? $_POST['type'] : 0;
            $background = isset($_POST['background']) ? $_POST['background'] : '#FFFFFF';
            $shop_ad = new Shop();
            $res = $shop_ad->addShopAd($ad_image, $link_url, $sort, $type, $background);
            return AjaxReturn($res);
        }
        return view($this->style . "Config/addShopAd");
    }

    /**
     * 修改店铺广告
     */
    public function updateShopAd()
    {
        if (request()->isAjax()) {
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $ad_image = isset($_POST['ad_image']) ? $_POST['ad_image'] : '';
            $link_url = isset($_POST['link_url']) ? $_POST['link_url'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : 0;
            $type = isset($_POST['type']) ? $_POST['type'] : 0;
            $background = isset($_POST['background']) ? $_POST['background'] : '#FFFFFF';
            $shop_ad = new Shop();
            $res = $shop_ad->updateShopAd($id, $ad_image, $link_url, $sort, $type, $background);
            return AjaxReturn($res);
        }
        $shop_ad = new Shop();
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $info = $shop_ad->getShopAdDetail($id);
        $this->assign('info', $info);
        return view($this->style . "Config/updateShopAd");
    }

    public function delShopAd()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $res = 0;
        if (! empty($id)) {
            $shop_ad = new Shop();
            $res = $shop_ad->delShopAd($id);
        }
        return AjaxReturn($res);
    }

    /**
     * 店铺导航列表
     */
    public function shopNavigationList()
    {
        if (request()->isAjax()) {
            $shop = new Shop();
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $list = $shop->ShopNavigationList($page_index, $page_size, '', 'sort');
            return $list;
        } else {
            $this->pcConfigChildMenuList(1);
            return view($this->style . "Config/shopNavigationList");
        }
    }

    /**
     * 店铺导航添加
     *
     * @return multitype:unknown
     */
    public function addShopNavigation()
    {
        $shop = new Shop();
        if (request()->isAjax()) {
            $nav_title = isset($_POST['nav_title']) ? $_POST['nav_title'] : '';
            $nav_url = isset($_POST['nav_url']) ? $_POST['nav_url'] : '';
            $type = isset($_POST['type']) ? $_POST['type'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
            $align = isset($_POST['align']) ? $_POST['align'] : '';
            $nav_type = request()->post("nav_type", '');
            $is_blank = request()->post("is_blank", '');
            $template_name = request()->post("template_name", '');
            $retval = $shop->addShopNavigation($nav_title, $nav_url, $type, $sort, $align, $nav_type, $is_blank, $template_name);
            return AjaxReturn($retval);
        } else {
            $shopNavTemplate = $shop->getShopNavigationTemplate(1);
            $this->assign("shopNavTemplate", $shopNavTemplate);
            return view($this->style . "Config/addShopNavigation");
        }
    }

    /**
     * 修改店铺导航
     *
     * @return multitype:unknown
     */
    public function updateShopNavigation()
    {
        $shop = new Shop();
        if (request()->isAjax()) {
            $nav_id = isset($_POST['nav_id']) ? $_POST['nav_id'] : '';
            $nav_title = isset($_POST['nav_title']) ? $_POST['nav_title'] : '';
            $nav_url = isset($_POST['nav_url']) ? $_POST['nav_url'] : '';
            $type = isset($_POST['type']) ? $_POST['type'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
            $align = isset($_POST['align']) ? $_POST['align'] : '';
            $nav_type = request()->post("nav_type", '');
            $is_blank = request()->post("is_blank", '');
            $template_name = request()->post("template_name", '');
            $retval = $shop->updateShopNavigation($nav_id, $nav_title, $nav_url, $type, $sort, $align, $nav_type, $is_blank, $template_name);
            return AjaxReturn($retval);
        } else {
            $nav_id = isset($_GET['nav_id']) ? $_GET['nav_id'] : '';
            $data = $shop->shopNavigationDetail($nav_id);
            $this->assign('data', $data);
            $shopNavTemplate = $shop->getShopNavigationTemplate(1);
            $this->assign("shopNavTemplate", $shopNavTemplate);
            return view($this->style . "Config/updateShopNavigation");
        }
    }

    /**
     * 删除店铺导航
     *
     * @return multitype:unknown
     */
    public function delShopNavigation()
    {
        if (request()->isAjax()) {
            $shop = new Shop();
            $nav_id = isset($_POST['nav_id']) ? $_POST['nav_id'] : '';
            $retval = $shop->delShopNavigation($nav_id);
            return AjaxReturn($retval);
        }
    }

    /**
     * 修改店铺导航排序
     *
     * @return multitype:unknown
     */
    public function modifyShopNavigationSort()
    {
        if (request()->isAjax()) {
            $shop = new Shop();
            $nav_id = isset($_POST['nav_id']) ? $_POST['nav_id'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
            $retval = $shop->modifyShopNavigationSort($nav_id, $sort);
            return AjaxReturn($retval);
        }
    }

    /**
     * 友情链接列表
     *
     * @return unknown[]
     */
    public function linkList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = isset($_POST['search_text']) ? $_POST['search_text'] : '';
            $platform = new Platform();
            $list = $platform->getLinkList($page_index, $page_size, [
                'link_title' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            ], 'link_sort ASC');
            return $list;
        }
        $this->pcConfigChildMenuList(5);
        return view($this->style . "Config/linkList");
    }

    /**
     * 添加友情链接
     *
     * @return unknown[]
     */
    public function addLink()
    {
        if (request()->isAjax()) {
            $link_title = isset($_POST["link_title"]) ? $_POST["link_title"] : '';
            $link_url = isset($_POST["link_url"]) ? $_POST["link_url"] : '';
            $link_pic = isset($_POST["link_pic"]) ? $_POST["link_pic"] : '';
            $link_sort = isset($_POST["link_sort"]) ? $_POST["link_sort"] : 0;
            $is_blank = request()->post("is_blank", '');
            $is_show = request()->post("is_show", '');
            $platform = new Platform();
            $res = $platform->addLink($link_title, $link_url, $link_pic, $link_sort, $is_blank, $is_show);
            return AjaxReturn($res);
        }
        return view($this->style . "Config/addLink");
    }

    /**
     * 修改友情链接
     */
    public function updateLink()
    {
        if (request()->isAjax()) {
            $link_id = isset($_POST["link_id"]) ? $_POST["link_id"] : '';
            $link_title = isset($_POST["link_title"]) ? $_POST["link_title"] : '';
            $link_url = isset($_POST["link_url"]) ? $_POST["link_url"] : '';
            $link_pic = isset($_POST["link_pic"]) ? $_POST["link_pic"] : '';
            $link_sort = isset($_POST["link_sort"]) ? $_POST["link_sort"] : 0;
            $is_blank = request()->post("is_blank", '');
            $is_show = request()->post("is_show", '');
            $platform = new Platform();
            $res = $platform->updateLink($link_id, $link_title, $link_url, $link_pic, $link_sort, $is_blank, $is_show);
            return AjaxReturn($res);
        }
        $link_id = isset($_GET["link_id"]) ? $_GET["link_id"] : '';
        $platform = new Platform();
        $link_info = $platform->getLinkDetail($link_id);
        $this->assign('link_info', $link_info);
        return view($this->style . "Config/updateLink");
    }

    /**
     * 删除友情链接
     *
     * @return unknown[]
     */
    public function delLink()
    {
        $link_id = isset($_POST["link_id"]) ? $_POST["link_id"] : '';
        $platform = new Platform();
        $res = $platform->deleteLink($link_id);
        return AjaxReturn($res);
    }

    /**
     * 搜索设置
     */
    public function searchConfig()
    {
        $type = isset($_GET["type"]) ? $_GET["type"] : "hot";
        if ($type == "hot") {
            $this->pcConfigChildMenuList(6);
        } else {
            $this->pcConfigChildMenuList(7);
        }
        $web_config = new WebConfig();
        // 热门搜索
        $keywords_array = $web_config->getHotsearchConfig($this->instance_id);
        if (! empty($keywords_array)) {
            $keywords = implode(",", $keywords_array);
        } else {
            $keywords = '';
        }
        $this->assign('hot_keywords', $keywords);
        // 默认搜索
        $default_keywords = $web_config->getDefaultSearchConfig($this->instance_id);
        $this->assign('default_keywords', $default_keywords);
        $this->assign('type', $type);
        return view($this->style . "Config/searchConfig");
    }

    /**
     * 热门搜索 提交修改
     */
    public function hotSearchConfig()
    {
        $keywords = isset($_POST["keywords"]) ? $_POST["keywords"] : '';
        if (! empty($keywords)) {
            $keywords_array = explode(",", $keywords);
        } else {
            $keywords_array = array();
        }
        $web_config = new WebConfig();
        $res = $web_config->setHotsearchConfig($this->instance_id, $keywords_array, 1);
        return AjaxReturn($res);
    }

    /**
     * 默认搜索 提交修改
     */
    public function defaultSearchConfig()
    {
        $keywords = isset($_POST["default_keywords"]) ? $_POST["default_keywords"] : '';
        $web_config = new WebConfig();
        $res = $web_config->setDefaultSearchConfig($this->instance_id, $keywords, 1);
        return AjaxReturn($res);
    }

    /**
     * 验证码设置
     *
     * @return \think\response\View
     */
    public function codeConfig()
    {
        $child_menu_list = array(
            array(
                'url' => "config/webconfig",
                'menu_name' => "网站设置",
                "active" => 0
            ),
            array(
                'url' => "config/seoConfig",
                'menu_name' => "SEO设置",
                "active" => 0
            ),
            array(
                'url' => "config/codeconfig",
                'menu_name' => "验证码设置",
                "active" => 1
            ),
            array(
                'url' => "config/shopset",
                'menu_name' => "购物设置",
                "active" => 0
            ),
            array(
                'url' => "config/expressmessage",
                'menu_name' => "物流跟踪设置",
                "active" => 0
            ),
            array(
                'url' => "config/copyrightinfo",
                'menu_name' => "版权设置",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        
        $webConfig = new WebConfig();
        if (request()->isAjax()) {
            $platform = 0; // isset($_POST['platform']) ? $_POST['platform'] : '';
            $admin = isset($_POST['adminCode']) ? $_POST['adminCode'] : 0;
            $pc = isset($_POST['pcCode']) ? $_POST['pcCode'] : 0;
            $res = $webConfig->setLoginVerifyCodeConfig($this->instance_id, $platform, $admin, $pc);
            return AjaxReturn($res);
        }
        $code_config = $webConfig->getLoginVerifyCodeConfig($this->instance_id);
        $this->assign('code_config', $code_config["value"]);
        return view($this->style . 'Config/codeConfig');
    }

    /**
     * 邮件短信接口设置
     */
    public function messageConfig()
    {
        $type = isset($_GET["type"]) ? $_GET["type"] : "email";
        if ($type == 'email') {
            $child_menu_list = array(
                array(
                    'url' => "Config/messageConfig?type=email",
                    'menu_name' => "邮箱设置",
                    "active" => 1
                ),
                array(
                    'url' => "Config/messageConfig?type=sms",
                    'menu_name' => "短信设置",
                    "active" => 0
                )
            );
        } else {
            $child_menu_list = array(
                array(
                    'url' => "Config/messageConfig?type=email",
                    'menu_name' => "邮箱设置",
                    "active" => 0
                ),
                array(
                    'url' => "Config/messageConfig?type=sms",
                    'menu_name' => "短信设置",
                    "active" => 1
                )
            );
        }
        $config = new WebConfig();
        $email_message = $config->getEmailMessage($this->instance_id);
        $this->assign('email_message', $email_message);
        $mobile_message = $config->getMobileMessage($this->instance_id);
        $this->assign('mobile_message', $mobile_message);
        $this->assign('child_menu_list', $child_menu_list);
        $this->assign('type', $type);
        return view($this->style . 'Config/messageConfig');
    }

    /**
     * ajax 邮件接口
     */
    public function setEmailMessage()
    {
        $email_host = request()->post('email_host', '');
        $email_port = request()->post('email_port', '');
        $email_addr = request()->post('email_addr', '');
        $email_id = request()->post('email_id', '');
        $email_pass = request()->post('email_pass', '');
        $is_use = request()->post('is_use', 0);
        $email_is_security = request()->post('email_is_security', false);
        $config = new WebConfig();
        $res = $config->setEmailMessage($this->instance_id, $email_host, $email_port, $email_addr, $email_id, $email_pass, $is_use, $email_is_security);
        return AjaxReturn($res);
    }

    /**
     * ajax 短信接口
     *
     * @return unknown[]
     */
    public function setMobileMessage()
    {
        $app_key = request()->post('app_key', '');
        $secret_key = request()->post('secret_key', '');
        $free_sign_name = request()->post('free_sign_name', '');
        $is_use = request()->post('is_use', '');
        $user_type = request()->post('user_type', 0); // 用户类型 0:旧用户，1：新用户 默认是旧用户
        $config = new WebConfig();
        $res = $config->setMobileMessage($this->instance_id, $app_key, $secret_key, $free_sign_name, $is_use, $user_type);
        return AjaxReturn($res);
    }

    /**
     * 邮件发送测试接口
     *
     * @return unknown[]
     */
    public function testSend()
    {
        $is_socket = extension_loaded('sockets');
        $is_connect = function_exists("socket_connect");
        if ($is_socket && $is_connect) {
            $send = new Send();
            // $toemail = "854991437@qq.com";//$_POST['email_test'];
            $title = 'Niushop测试邮箱发送';
            $content = '测试邮箱发送成功不成功？';
            $email_host = request()->post('email_host', '');
            $email_port = request()->post('email_port', '');
            $email_addr = request()->post('email_addr', '');
            $email_id = request()->post('email_id', '');
            $email_pass = request()->post('email_pass', '');
            $email_is_security = request()->post('email_is_security', '');
            $toemail = request()->post('email_test', '');
            $res = emailSend($email_host, $email_id, $email_pass, $email_port, $email_is_security, $email_addr, $toemail, $title, $content, $this->instance_name);
            // $config = new WebConfig();
            // $email_message = $config->getEmailMessage($this->instance_id);
            // $email_value = $email_message["value"];
            // $res = emailSend($email_value['email_host'], $email_value['email_id'], $email_value['email_pass'], $email_value['email_addr'], $toemail, $title, $content);
            // var_dump($res);
            // exit;
            if ($res) {
                return AjaxReturn(1);
            } else {
                return AjaxReturn(- 1);
            }
        } else {
            return AjaxReturn(EMAIL_SENDERROR);
        }
    }

    /**
     * 帮助类型
     *
     * @return unknown
     */
    public function helpclass()
    {
        $child_menu_list = array(
            array(
                'url' => "config/helpdocument",
                'menu_name' => "帮助内容",
                "active" => 0
            ),
            array(
                'url' => "config/helpclass",
                'menu_name' => "帮助类型",
                "active" => 1
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $platform = new Platform();
            $list = $platform->getPlatformHelpClassList($page_index, $page_size, [
                'type' => 1
            ], 'sort');
            return $list;
        }
        return view($this->style . "Config/helpClass");
    }

    /**
     * 修改帮助类型
     * 任鹏强
     * 2017年2月18日14:26:20
     */
    public function updateClass()
    {
        if (request()->isAjax()) {
            $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
            $type = isset($_POST['type']) ? $_POST['type'] : 1;
            $class_name = isset($_POST['class_name']) ? $_POST['class_name'] : '';
            $parent_class_id = isset($_POST['parent_class_id']) ? $_POST['parent_class_id'] : 0;
            $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
            $platform = new Platform();
            $res = $platform->updatePlatformClass($class_id, $type, $class_name, $parent_class_id, $sort);
            return AjaxReturn($res);
        }
    }

    /**
     * 删除帮助类型
     */
    public function classDelete()
    {
        $class_id = request()->post('class_id', '');
        $platform = new Platform();
        $retval = $platform->deleteHelpClass($class_id);
        return AjaxReturn($retval);
    }

    /**
     * 添加 帮助类型
     */
    public function addHelpClass()
    {
        if (request()->isAjax()) {
            $class_name = isset($_POST['class_name']) ? $_POST['class_name'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
            $platform = new Platform();
            $res = $platform->addPlatformHelpClass(1, $class_name, 0, $sort);
            return AjaxReturn($res);
        }
        return view($this->style . 'Config/addHelpClass');
    }

    /**
     * 删除帮助内容标题
     *
     * @return unknown[]
     */
    public function titleDelete()
    {
        $id = request()->post('id', '');
        $platform = new Platform();
        $res = $platform->deleteHelpTitle($id);
        return AjaxReturn($res);
    }

    /**
     * 帮助内容
     *
     * @return multitype:number unknown |Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function helpDocument()
    {
        $child_menu_list = array(
            array(
                'url' => "config/helpdocument",
                'menu_name' => "帮助内容",
                "active" => 1
            ),
            array(
                'url' => "config/helpclass",
                'menu_name' => "帮助类型",
                "active" => 0
            )
        );
        $this->assign('child_menu_list', $child_menu_list);
        
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $platform = new Platform();
            $list = $platform->getPlatformHelpDocumentList($page_index, $page_size, '', 'sort');
            return $list;
        }
        return view($this->style . "Config/helpDocument");
    }

    /**
     * 修改内容
     */
    public function updateDocument()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $uid = $this->user->getSessionUid();
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
            $link_url = isset($_POST['link_url']) ? $_POST['link_url'] : '';
            $content = isset($_POST['content']) ? $_POST['content'] : '';
            $image = isset($_POST['image']) ? $_POST['image'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : 0;
            $revle = $platform->updatePlatformDocument($id, $uid, $class_id, $title, $link_url, $sort, $content, $image);
            return AjaxReturn($revle);
        } else {
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $this->assign('id', $id);
            $document_detail = $platform->getPlatformDocumentDetail($id);
            $document_detail["content"] = htmlspecialchars($document_detail["content"]);
            $this->assign('document_detail', $document_detail);
            $help_class_list = $platform->getPlatformHelpClassList();
            $this->assign('help_class_list', $help_class_list['data']);
            return view($this->style . 'Config/updateDocument');
        }
    }

    /**
     * 添加内容
     */
    public function addDocument()
    {
        $platform = new Platform();
        if (request()->isAjax()) {
            $uid = $this->user->getSessionUid();
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
            $link_url = isset($_POST['link_url']) ? $_POST['link_url'] : '';
            $content = isset($_POST['content']) ? $_POST['content'] : '';
            $image = isset($_POST['image']) ? $_POST['image'] : '';
            $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
            $result = $platform->addPlatformDocument($uid, $class_id, $title, $link_url, $sort, $content, $image);
            return AjaxReturn($result);
        } else {
            $help_class_list = $platform->getPlatformHelpClassList();
            $this->assign('help_class_list', $help_class_list['data']);
            return view($this->style . 'Config/addDocument');
        }
    }

    /**
     * pc端子菜单列表
     * 2017年7月24日 14:25:56 王永杰
     *
     * @param $flag 1:导航管理，2：促销板块，3：首页楼层，4：首页公告，5：友情链接，6：热门搜索，7：默认搜索，8：手机模板，9：自定义模板            
     */
    public function pcConfigChildMenuList($flag)
    {
        $child_menu_list = array(
            array(
                'url' => "config/shopnavigationlist",
                'menu_name' => "导航管理",
                "active" => 0,
                'flag' => 1
            ),
//             array(
//                 'url' => "system/goodsrecommendclass",
//                 'menu_name' => "促销版块",
//                 "active" => 0,
//                 'flag' => 2
//             ),
//             array(
//                 'url' => "system/blocklist",
//                 'menu_name' => "首页楼层",
//                 "active" => 0,
//                 'flag' => 3
//             ),
            array(
                'url' => "system/goodscategoryblock",
                'menu_name' => "分类商品楼层",
                "active" => 0,
                'flag' => 3
             ),
            array(
                'url' => "config/usernotice",
                'menu_name' => "首页公告",
                "active" => 0,
                'flag' => 4
            ),
            
            array(
                'url' => "config/linklist",
                'menu_name' => "友情链接",
                "active" => 0,
                'flag' => 5
            ),
            array(
                'url' => "config/searchConfig?type=hot",
                'menu_name' => "热门搜索",
                "active" => 0,
                'flag' => 6
            ),
            array(
                'url' => "config/searchConfig?type=default",
                'menu_name' => "默认搜索",
                "active" => 0,
                'flag' => 7
            )
        );
        
        foreach ($child_menu_list as $k => $v) {
            if ($v['flag'] == $flag) {
                $child_menu_list[$k]['active'] = 1;
            }
        }
        $this->assign('child_menu_list', $child_menu_list);
    }

    function getfiles($path)
    {
        try {
            
            $config_list = array();
            
            $k = 0;
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if ((is_dir($path . "/" . $file)) && $file != "." && $file != "..") {
                        // 当前目录问文件夹
                        $file_path = $path . '/' . $file . '/config.xml';
                        $file_path = str_replace("\\", "/", $file_path);
                        $config_list[$k]['file_path'] = $file_path; // 文件路径
                        $config_list[$k]['is_readable'] = is_readable($file_path); // 是否可读
                        $config_list[$k]['is_writable'] = is_writable($file_path); // 是否可写
                        $k ++;
                    }
                }
                closedir($dh);
            }
            $config_list = array_merge($config_list);
        } catch (\Exception $e) {
            echo $e;
        }
        return $config_list;
    }

    /**
     * 获取所有手机模板Xml
     * 2017年7月25日 10:25:04 王永杰
     *
     * @return string[] xml list
     */
    public function getWapTemplateXmlList()
    {
        $file_path = str_replace("\\", "/", ROOT_PATH . 'template/wap');
        $config_list = $this->getfiles($file_path);
        return $config_list;
    }

    /**
     * 手机模板
     * 2017年7月24日 14:48:40 王永杰
     */
    public function wapTemplate()
    {
        $config = new WebConfig();
        $use_wap_template = $config->getUseWapTemplate($this->instance_id);
        if (empty($use_wap_template)) {
            // 默认使用默认模板
            $res = $this->updateWapTemplateUse();
        }
        // XML标签配置
        $xmlTag = array(
            'folder',
            'theme',
            'preview',
            'advice',
            'use'
        );
        $xml = new \DOMDocument();
        $template_list = array();
        $config_list = $this->getWapTemplateXmlList();
        $wap_template_count = count($config_list); // 手机模板数量
        
        $not_readable_list = array(); // 文件不可读数量
        $not_writeable_list = array(); // 文件不可写数量
        foreach ($config_list as $k => $config) {
            if ($config['is_readable']) {
                
                // 获取xml文件内容
                $xml_txt = fopen($config['file_path'], "r,w");
                $xml_str = fread($xml_txt, filesize($config['file_path'])); // 指定读取大小，这里把整个文件内容读取出来
                $xml_text = str_replace("\r\n", "<br />", $xml_str);
                $xml->loadXML($xml_text);
                $template = $xml->getElementsByTagName('template'); // 最外层节点
                foreach ($template as $p) {
                    foreach ($xmlTag as $x) {
                        $node = $p->getElementsByTagName($x);
                        $template_list[$k][$x] = $node->item(0)->nodeValue;
                    }
                }
            }
            if (! $config['is_readable']) {
                $not_readable_list[] = $config['file_path'];
            }
            
            if (! $config['is_writable']) {
                $not_writeable_list[] = $config['file_path'];
            }
        }
        // 文件不可读数量及文件路径
        $this->assign("not_readable_count", count($not_readable_list));
        $this->assign("not_readable_list", $not_readable_list);
        
        // 文件不可写数量及文件路径
        $this->assign("not_writable_count", count($not_writeable_list));
        $this->assign("not_writeable_list", $not_writeable_list);
        
        $this->assign("wap_template_count", $wap_template_count);
        $this->assign("template_list", $template_list);
        // $this->pcConfigChildMenuList(8);
        
        $child_menu_list = array(
            array(
                'url' => "config/updatenotice",
                'menu_name' => "首页公告",
                "active" => 0
            ),
            array(
                'url' => "system/goodsrecommendclassmobile",
                'menu_name' => "促销版块",
                "active" => 0
            ),
            array(
                'url' => 'config/waptemplate',
                'menu_name' => '手机模板',
                'active' => 1,
                'flag' => 8
            ),
            array(
                'url' => 'config/fixedtemplate',
                'menu_name' => '固定模板',
                'active' => 0
            ),
            array(
                'url' => 'config/customtemplatelist',
                'menu_name' => '自定义模板',
                'active' => 0,
                'flag' => 9
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        
        return view($this->style . 'Config/wapTemplate');
    }

    /**
     * 更新手机端所使用的模板,修改对应的XML文件，已经数据库信息
     * 2017年7月25日 09:27:18 王永杰
     */
    public function updateWapTemplateUse()
    {
        $res = 0; // 返回值
        $folder = request()->post("folder", "default"); // default：默认模板
        /*
         * 修改XML
         * 1.找到要修改的XML
         * 2.修改use字段为1
         */
        $xml = new \DOMDocument();
        $template_list = array();
        $config_list = $this->getWapTemplateXmlList();
        foreach ($config_list as $k => $config) {
            if ($config['is_readable'] && $config['is_writable']) {
                // 获取xml文件内容
                $xml_txt = fopen($config['file_path'], "r,w");
                $xml_str = fread($xml_txt, filesize($config['file_path'])); // 指定读取大小，这里把整个文件内容读取出来
                $xml_text = str_replace("\r\n", "<br />", $xml_str);
                $xml->loadXML($xml_text);
                $template = $xml->getElementsByTagName('template');
                
                foreach ($template as $list) {
                    $folder_xml = $list->getElementsByTagName("folder");
                    $use = $list->getElementsByTagName("use");
                    if ($folder_xml->item(0)->nodeValue == $folder) {
                        $use->item(0)->nodeValue = 1;
                    } else {
                        $use->item(0)->nodeValue = 0;
                    }
                    $res = $xml->save($config['file_path']);
                }
            }
        }
        if ($res > 0) {
            $config = new WebConfig();
            $res = $config->setUseWapTemplate($this->instance_id, $folder);
        }
        return $res;
    }

    /**
     * 手机端自定义模板
     * 创建时间：2017年7月26日 19:13:08 王永杰
     * 更新时间：2017年8月16日 15:04:52 王永杰
     * 1、新增优惠券组件、富文本组件、自定义模块组件
     * 2、商品列表组件新增“购买按钮编辑”功能，完善数据结构
     *
     * @return \think\response\View
     */
    public function customTemplate()
    {
        $goods_category = new GoodsCategory();
        $web_config = new WebConfig();
        $promotion = new Promotion();
        $goods_category_list = $goods_category->getFormatGoodsCategoryList();
        // $this->pcConfigChildMenuList(9);
        
        $id = request()->get("id", 0);
        $menu_url = "config/customtemplate";
        if ($id > 0) {
            $menu_url = "config/customtemplate?id=" . $id;
        }
        $child_menu_list = array(
            array(
                'url' => $menu_url,
                'menu_name' => '编辑自定义模板',
                'active' => 1
            )
        );
        
        $url = ROOT_PATH . 'template/' . $this->style . "/Config/customTemplate.html";
        $url = str_replace("\\", "/", $url);
        if (! file_exists($url)) {
            $this->error("该功能需要授权才能使用！");
        }
        $this->assign('child_menu_list', $child_menu_list);
        
        $custom_template_info = $web_config->getCustomTemplateById($id);
        $template_name = $custom_template_info['template_name'];
        if (empty($template_name)) {
            $template_name = "模板名称";
        }
        // 获取所有模板列表，排除自己
        $template_list = $web_config->getCustomTemplateList(1, 0, [
            "id" => [
                "NEQ",
                $id
            ]
        ], "modify_time desc", "id,template_name,template_data");
        
        $template_data = $custom_template_info['template_data'];
        
        $condition = array(
            'start_time' => array(
                'ELT',
                time()
            ),
            'end_time' => array(
                'EGT',
                time()
            ),
            'is_show' => 1,
            'shop_id' => $this->instance_id
        );
        $coupon_type_list = $promotion->getCouponTypeList(1, 0, $condition, ""); // 优惠券
        
        $this->assign("id", $id);
        $this->assign("template_list", json_encode($template_list['data']));
        $this->assign("coupon_type_list", json_encode($coupon_type_list['data'])); // 优惠券集合
        $this->assign("goods_category_list", json_encode($goods_category_list));
        $this->assign("template_name", $template_name);
        $this->assign("template_data", $template_data);
        
        return view($this->style . 'Config/customTemplate');
    }

    /**
     * 手机端自定义模板列表
     * 创建时间：2017年8月16日 15:06:52 王永杰
     *
     * @return number[]|unknown[]|\think\response\View
     */
    public function customTemplateList()
    {
        $web_config = new WebConfig();
        $child_menu_list_old = $this->user->getchildModuleQuery($this->second_menu_id);
        $child_menu_list = [];
        foreach ($child_menu_list_old as $k => $v) {
            $child_menu_list[] = array(
                'url' => $v['url'],
                'menu_name' => $v['module_name'],
                'active' => 0
            );
        }
        // var_dump($child_menu_list);
        // $child_menu_list = array(
        // array(
        // 'url' => 'config/fixedtemplate',
        // 'menu_name' => '固定模板',
        // 'active' => 0
        // ),
        // array(
        // 'url' => 'config/customTemplatelist',
        // 'menu_name' => '自定义模板',
        // 'active' => 1,
        // 'flag' => 9
        // )
        // );
        if (request()->isAjax()) {
            $page_index = request()->post("page_index", 1);
            $page_size = request()->post("page_size", PAGESIZE);
            $template_name = request()->post("template_name", "");
            if (! empty($template_name)) {
                $condition["template_name"] = array(
                    "like",
                    "%" . $template_name . "%"
                );
            }
            $order = "is_default desc,modify_time desc";
            $field = "id,shop_id,template_name,create_time,modify_time,is_enable,is_default";
            $custom_template_list = $web_config->getCustomTemplateList($page_index, $page_size, $condition, $order, $field);
            return $custom_template_list;
        }
        $default_custom_template = $web_config->getDefaultCustomTemplate($this->instance_id);
        $is_default = 0;
        if (! empty($default_custom_template)) {
            $is_default = $default_custom_template['is_default'];
        }
        $this->assign("is_default", $is_default);
        $this->assign('child_menu_list', $child_menu_list);
        return view($this->style . 'Config/customTemplateList');
    }

    /**
     * 添加自定义模板
     * 2017年7月31日 11:38:58
     */
    public function addCustomTemplate()
    {
        $web_config = new WebConfig();
        $template_name = request()->post("template_name", ""); // 自定义模板名称，预览
        $template_data = request()->post("template_data", ""); // 模板数据
        $res = $web_config->addCustomTemplate($this->instance_id, $template_name, $template_data);
        return AjaxReturn($res);
    }

    /**
     * 修改自定义模板
     * 创建时间：2017年8月14日 14:47:48
     */
    public function updateCustomTemplate()
    {
        $web_config = new WebConfig();
        $id = request()->post("id", ""); // 主键id
        $template_name = request()->post("template_name", ""); // 自定义模板名称，预览
        $template_data = request()->post("template_data", ""); // 模板数据
        $res = $web_config->updateCustomTemplate($id, $this->instance_id, $template_name, $template_data);
        return AjaxReturn($res);
    }

    /**
     * 开启关闭自定义模板
     * 2017年8月9日 14:49:37
     */
    public function setIsEnableCustomTemplate()
    {
        $web_config = new WebConfig();
        $is_enable = request()->post("is_enable", 0);
        $res = $web_config->setIsEnableCustomTemplate($this->instance_id, $is_enable);
        return AjaxReturn($res);
    }

    /**
     * 设置默认模板
     * 创建时间：2017年8月14日 15:21:06
     */
    public function setDefaultCustomTemplate()
    {
        $id = request()->post("id", "");
        $web_config = new WebConfig();
        $res = $web_config->setDefaultCustomTemplate($id);
        return AjaxReturn($res);
    }

    /**
     * 根据id删除手机端自定义模板
     * 2017年8月14日 12:03:18
     *
     * @return number
     */
    public function deleteCustomTemplateById()
    {
        $id = request()->post("id", 0);
        $web_config = new WebConfig();
        $res = $web_config->deleteCustomTemplateById($id);
        return AjaxReturn($res);
    }

    /**
     * 固定模板
     */
    public function fixedtemplate()
    {
        $this->getThreeLevelModule();
        $web_config = new WebConfig();
        $platform = new Platform();
        $condition = [
            'class_type' => 2,
            'is_use' => 1,
            'show_type' => 1
        ];
        $goods_recommend_class = $platform->getPlatformGoodsRecommendClass($condition);
        $this->assign('goods_recommend_class', $goods_recommend_class);
        $goods_category = new GoodsCategory();
        $category_list_1 = $goods_category->getGoodsCategoryList(1, 0, [
            'is_visible' => 1,
            'level' => 1
        ]);
        $this->assign("show_type", 1);
        $this->assign('category_list_1', $category_list_1['data']);
        // 手机模板
        $config = new WebConfig();
        $use_wap_template = $config->getUseWapTemplate($this->instance_id);
        if (empty($use_wap_template)) {
            // 默认使用默认模板
            $res = $this->updateWapTemplateUse();
        }
        // XML标签配置
        $xmlTag = array(
            'folder',
            'theme',
            'preview',
            'advice',
            'use'
        );
        $xml = new \DOMDocument();
        $template_list = array();
        $config_list = $this->getWapTemplateXmlList();
        $wap_template_count = count($config_list); // 手机模板数量
        
        $not_readable_list = array(); // 文件不可读数量
        $not_writeable_list = array(); // 文件不可写数量
        
        foreach ($config_list as $k => $config) {
            if ($config['is_readable']) {
                
                // 获取xml文件内容
                $xml_txt = fopen($config['file_path'], "r,w");
                $xml_str = fread($xml_txt, filesize($config['file_path'])); // 指定读取大小，这里把整个文件内容读取出来
                $xml_text = str_replace("\r\n", "<br />", $xml_str);
                $xml->loadXML($xml_text);
                $template = $xml->getElementsByTagName('template'); // 最外层节点
                foreach ($template as $p) {
                    foreach ($xmlTag as $x) {
                        $node = $p->getElementsByTagName($x);
                        $template_list[$k][$x] = $node->item(0)->nodeValue;
                    }
                }
            }
            if (! $config['is_readable']) {
                $not_readable_list[] = $config['file_path'];
            }
            
            if (! $config['is_writable']) {
                $not_writeable_list[] = $config['file_path'];
            }
        }
        // 文件不可读数量及文件路径
        $this->assign("not_readable_count", count($not_readable_list));
        $this->assign("not_readable_list", $not_readable_list);
        
        // 文件不可写数量及文件路径
        $this->assign("not_writable_count", count($not_writeable_list));
        $this->assign("not_writeable_list", $not_writeable_list);
        
        $this->assign("wap_template_count", $wap_template_count);
        $this->assign("template_list", $template_list);
        // $this->pcConfigChildMenuList(8);
        // 首页公告
        $web_config = new WebConfig();
        $shopid = $this->instance_id;
        if (request()->isAjax()) {
            $notice_message = request()->post('notice_message', '');
            $is_enable = request()->post('is_enable', '');
            $res = $web_config->setNotice($shopid, $notice_message, $is_enable);
            return AjaxReturn($res);
        }
        
        $info = $web_config->getNotice($shopid);
        $this->assign('info', $info);
        // 查询商品促销板块是否显示配置
        $shop_id = $this->instance_id;
        $config_service = new WebConfig();
        $list = $config_service->getrecommendConfig($shop_id);
        if (empty($list)) {
            $list['id'] = '';
            $list['value']['is_recommend'] = '';
        }
        $lists = $config_service->getcategoryConfig($shop_id);
        $this->assign("list", $list);
        $this->assign("lists", $lists);
        return view($this->style . 'Config/fixedtemplate');
    }

    /**
     * 编辑促销版块
     */
    public function updateGoodsRecommendClass()
    {
        $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : 0;
        $class_name = isset($_POST['class_name']) ? $_POST['class_name'] : '';
        $goods_id_array = isset($_POST['goods_id_str']) ? $_POST['goods_id_str'] : '';
        $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
        $show_type = isset($_POST['show_type']) ? $_POST['show_type'] : '';
        $platform = new Platform();
        $res = $platform->updatePlatformGoodsRecommendClass($class_id, $class_name, $sort, $goods_id_array, $show_type);
        return AjaxReturn($res);
    }

    /**
     * 删除 促销版块
     *
     * @return unknown[]
     */
    public function delGoodsRecommendClass()
    {
        $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : 0;
        if ($class_id > 0) {
            $platform = new Platform();
            $res = $platform->deletePlatformGoodsRecommendClass($class_id);
            return AjaxReturn($res);
        } else {
            return AjaxReturn(0);
        }
    }

    /**
     * 首页公告 设置
     *
     * @return \think\response\View
     */
    public function userNotice()
    {
        $web_config = new WebConfig();
        if (request()->isAjax()) {
            $user_notice = isset($_POST['user_notice']) ? $_POST['user_notice'] : '';
            $res = $web_config->setUserNotice($this->instance_id, $user_notice, 1);
            return AjaxReturn($res);
        }
        $this->pcConfigChildMenuList(4);
        $user_notice = $web_config->getUserNotice($this->instance_id);
        $this->assign('user_notice', $user_notice);
        return view($this->style . 'Config/userNotice');
    }

    /**
     * 奖励管理
     */
    public function bonuses()
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
            $into_store_coupon = isset($_POST['into_store_coupon']) ? $_POST['into_store_coupon'] : 0;
            $share_coupon = isset($_POST['share_coupon']) ? $_POST['share_coupon'] : 0;
            $dataShop = new Shop();
            $res = $dataShop->setRewardRule($shop_id, $sign_point, $share_point, $reg_member_self_point, $reg_member_one_point, $reg_member_two_point, $reg_member_three_point, $reg_promoter_self_point, $reg_promoter_one_point, $reg_promoter_two_point, $reg_promoter_three_point, $reg_partner_self_point, $reg_partner_one_point, $reg_partner_two_point, $reg_partner_three_point, $into_store_coupon, $share_coupon);
            return AjaxReturn($res);
        }
        $dataShop = new Shop();
        $res = $dataShop->getRewardRuleDetail($this->instance_id); // 此处有问题 2017年7月17日 14:35:24
        $this->assign("res", $res);
        // 查询未过期的优惠劵
        $coupon = new Promotion();
        $condition['shop_id'] = $this->instance_id;
        $nowTime = date("Y-m-d H:i:s");
        $condition['end_time'] = array(
            ">",
            getTimeTurnTimeStamp($nowTime)
        );
        $list = $coupon->getCouponTypeList(1, 0, $condition);
        $this->assign("coupon", $list['data']);
        return view($this->style . 'Config/bonuses');
    }

    /**
     * 修改公告
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >|Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function updateNotice()
    {
        $child_menu_list = array(
            array(
                'url' => "config/updatenotice",
                'menu_name' => "首页公告",
                "active" => 1
            ),
            array(
                'url' => "system/goodsrecommendclassmobile",
                'menu_name' => "促销版块",
                "active" => 0
            ),
            array(
                'url' => 'config/waptemplate',
                'menu_name' => '手机模板',
                'active' => 0,
                'flag' => 8
            ),
            array(
                'url' => 'config/fixedtemplate',
                'menu_name' => '固定模板',
                'active' => 0
            ),
            array(
                'url' => 'config/customtemplatelist',
                'menu_name' => '自定义模板',
                'active' => 0,
                'flag' => 9
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        $web_config = new WebConfig();
        $shopid = $this->instance_id;
        if (request()->isAjax()) {
            $notice_message = request()->post('notice_message', '');
            $is_enable = request()->post('is_enable', '');
            $res = $web_config->setNotice($shopid, $notice_message, $is_enable);
            return AjaxReturn($res);
        }
        
        $info = $web_config->getNotice($shopid);
        $this->assign('info', $info);
        return view($this->style . 'Config/updateNotice');
    }

    public function areaManagement()
    {
        $child_menu_list = array(
            array(
                'url' => "express/expresscompany",
                'menu_name' => "物流公司",
                "active" => 0
            ),
            array(
                'url' => "config/areamanagement",
                'menu_name' => "地区管理",
                "active" => 1
            ),
            array(
                'url' => "order/returnsetting",
                'menu_name' => "商家地址",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointlist",
                'menu_name' => "自提点管理",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointfreight",
                'menu_name' => "自提点运费",
                "active" => 0
            ),
            array(
                'url' => "config/distributionareamanagement",
                'menu_name' => "货到付款地区管理",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        $dataAddress = new DataAddress();
        $area_list = $dataAddress->getAreaList(); // 区域地址
        $list = $dataAddress->getProvinceList();
        foreach ($list as $k => $v) {
            if ($dataAddress->getCityCountByProvinceId($v['province_id']) > 0) {
                $v['issetLowerLevel'] = 1;
            } else {
                $v['issetLowerLevel'] = 0;
            }
            if (! empty($area_list)) {
                foreach ($area_list as $area) {
                    if ($area['area_id'] == $v['area_id']) {
                        $list[$k]['area_name'] = $area['area_name'];
                        break;
                    } else {
                        $list[$k]['area_name'] = "-";
                    }
                }
            }
        }
        $this->assign("area_list", $area_list);
        $this->assign("list", $list);
        return view($this->style . 'Config/areaManagement');
    }

    public function selectCityListAjax()
    {
        if (request()->isAjax()) {
            $province_id = request()->post('province_id', '');
            $dataAddress = new DataAddress();
            $list = $dataAddress->getCityList($province_id);
            foreach ($list as $v) {
                if ($dataAddress->getDistrictCountByCityId($v['city_id']) > 0) {
                    $v['issetLowerLevel'] = 1;
                } else {
                    $v['issetLowerLevel'] = 0;
                }
            }
            return $list;
        }
    }

    public function selectDistrictListAjax()
    {
        if (request()->isAjax()) {
            $city_id = request()->post('city_id', '');
            $dataAddress = new DataAddress();
            $list = $dataAddress->getDistrictList($city_id);
            return $list;
        }
    }

    public function addCityAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $city_id = 0;
            $province_id = request()->post('superiorRegionId', '');
            $city_name = request()->post('regionName', '');
            $zipcode = request()->post('zipcode', '');
            $sort = request()->post('regionSort', '');
            $res = $dataAddress->addOrupdateCity($city_id, $province_id, $city_name, $zipcode, $sort);
            return AjaxReturn($res);
        }
    }

    public function updateCityAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $city_id = request()->post('eventId', '');
            $province_id = request()->post('superiorRegionId', '');
            $city_name = request()->post('regionName', '');
            $zipcode = request()->post('zipcode', '');
            $sort = request()->post('regionSort', '');
            $res = $dataAddress->addOrupdateCity($city_id, $province_id, $city_name, $zipcode, $sort);
            return AjaxReturn($res);
        }
    }

    public function addDistrictAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $district_id = 0;
            $city_id = request()->post('superiorRegionId', '');
            $district_name = request()->post('regionName', '');
            $sort = request()->post('regionSort', '');
            $res = $dataAddress->addOrupdateDistrict($district_id, $city_id, $district_name, $sort);
            return AjaxReturn($res);
        }
    }

    public function updateDistrictAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $district_id = request()->post('eventId', '');
            $city_id = request()->post('superiorRegionId', '');
            $district_name = request()->post('regionName', '');
            $sort = request()->post('regionSort', '');
            $res = $dataAddress->addOrupdateDistrict($district_id, $city_id, $district_name, $sort);
            return AjaxReturn($res);
        }
    }

    public function updateProvinceAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $province_id = request()->post('eventId', '');
            $province_name = request()->post('regionName', '');
            $sort = request()->post('regionSort', '');
            $area_id = request()->post('area_id', '');
            $res = $dataAddress->updateProvince($province_id, $province_name, $sort, $area_id);
            return AjaxReturn($res);
        }
    }

    public function addProvinceAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $province_name = request()->post('regionName', ''); // 区域名称
            $sort = request()->post('regionSort', ''); // 排序
            $area_id = request()->post('area_id', 0); // 区域id
            $res = $dataAddress->addProvince($province_name, $sort, $area_id);
            return AjaxReturn($res);
        }
    }

    public function deleteRegion()
    {
        if (request()->isAjax()) {
            $type = request()->post('type', '');
            $regionId = request()->post('regionId', '');
            $dataAddress = new DataAddress();
            if ($type == 1) {
                $res = $dataAddress->deleteProvince($regionId);
                return AjaxReturn($res);
            }
            if ($type == 2) {
                $res = $dataAddress->deleteCity($regionId);
                return AjaxReturn($res);
            }
            if ($type == 3) {
                $res = $dataAddress->deleteDistrict($regionId);
                return AjaxReturn($res);
            }
        }
    }

    public function updateRegionAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $upType = request()->post('upType', '');
            $regionType = request()->post('regionType', '');
            $regionName = request()->post('regionName', '');
            $regionSort = request()->post('regionSort', '');
            $regionId = request()->post('regionId', '');
            $res = $dataAddress->updateRegionNameAndRegionSort($upType, $regionType, $regionName, $regionSort, $regionId);
            return AjaxReturn($res);
        }
    }

    /**
     * 购物设置
     */
    public function shopSet()
    {
        $child_menu_list = array(
            array(
                'url' => "config/webconfig",
                'menu_name' => "网站设置",
                "active" => 0
            ),
            array(
                'url' => "config/seoConfig",
                'menu_name' => "SEO设置",
                "active" => 0
            ),
            array(
                'url' => "config/codeconfig",
                'menu_name' => "验证码设置",
                "active" => 0
            ),
            array(
                'url' => "config/shopset",
                'menu_name' => "购物设置",
                "active" => 1
            ),
            array(
                'url' => "config/expressmessage",
                'menu_name' => "物流跟踪设置",
                "active" => 0
            ),
            array(
                'url' => "config/copyrightinfo",
                'menu_name' => "版权设置",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $order_auto_delinery = request()->post("order_auto_delinery", '') ? request()->post("order_auto_delinery", '') : 0;
            
            $order_balance_pay = request()->post("order_balance_pay", '') ? request()->post("order_balance_pay", '') : 0;
            $order_delivery_complete_time = request()->post("order_delivery_complete_time", '') ? request()->post("order_delivery_complete_time", '') : 0;
            $order_show_buy_record = request()->post("order_show_buy_record", '') ? request()->post("order_show_buy_record", '') : 0;
            $order_invoice_tax = request()->post("order_invoice_tax", '') ? request()->post("order_invoice_tax", '') : 0;
            $order_invoice_content = request()->post("order_invoice_content", '') ? request()->post("order_invoice_content", '') : '';
            $order_delivery_pay = request()->post("order_delivery_pay", '') ? request()->post("order_delivery_pay", '') : 0;
            $order_buy_close_time = request()->post("order_buy_close_time", '') ? request()->post("order_buy_close_time", '') : 0;
            $buyer_self_lifting = request()->post("buyer_self_lifting", '') ? request()->post("buyer_self_lifting", '') : 0;
            $seller_dispatching = request()->post("seller_dispatching", '1');
            $is_logistics = request()->post("is_logistics", '1');
            $shopping_back_points = request()->post("shopping_back_points", '') ? request()->post("shopping_back_points", '') : 0;
            // var_dump($seller_dispatching);die;
            $Config = new WebConfig();
            $retval = $Config->SetShopConfig($shop_id, $order_auto_delinery, $order_balance_pay, $order_delivery_complete_time, $order_show_buy_record, $order_invoice_tax, $order_invoice_content, $order_delivery_pay, $order_buy_close_time, $buyer_self_lifting, $seller_dispatching, $is_logistics, $shopping_back_points);
            return AjaxReturn($retval);
        } else {
            $Config = new WebConfig();
            // 订单收货之后多长时间自动完成
            $shop_id = $this->instance_id;
            $shopSet = $Config->getShopConfig($shop_id);
            $this->assign("shopSet", $shopSet);
            return view($this->style . "Config/shopSet");
        }
    }

    /**
     * 通知系统
     */
    public function notifyIndex()
    {
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $notify_list = $config_service->getNoticeConfig($shop_id);
        $this->assign("notify_list", $notify_list);
        return view($this->style . 'Config/notifyConfig');
    }

    /**
     * 开启和关闭 邮件 和短信的开启和 关闭
     */
    public function updateNotifyEnable()
    {
        $id = $_POST["id"];
        $is_use = $_POST["is_use"];
        $config_service = new WebConfig();
        $retval = $config_service->updateConfigEnable($id, $is_use);
        return AjaxReturn($retval);
    }

    /**
     * 修改模板
     *
     * @return \think\response\View
     */
    public function notifyTemplate()
    {
        $type = isset($_GET["type"]) ? $_GET["type"] : "email";
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $template_detail = $config_service->getNoticeTemplateDetail($shop_id, $type);
        $template_type_list = $config_service->getNoticeTemplateType($type);
        for ($i = 0; $i < count($template_type_list); $i ++) {
            $template_code = $template_type_list[$i]["template_code"];
            $is_enable = 0;
            $template_title = "";
            $template_content = "";
            $sign_name = "";
            foreach ($template_detail as $template_obj) {
                if ($template_obj["template_code"] == $template_code) {
                    $is_enable = $template_obj["is_enable"];
                    $template_title = $template_obj["template_title"];
                    $template_content = str_replace(PHP_EOL, '', $template_obj["template_content"]);
                    $sign_name = $template_obj["sign_name"];
                    break;
                }
            }
            $template_type_list[$i]["is_enable"] = $is_enable;
            $template_type_list[$i]["template_title"] = $template_title;
            $template_type_list[$i]["template_content"] = $template_content;
            $template_type_list[$i]["sign_name"] = $sign_name;
        }
        $template_item_list = $config_service->getNoticeTemplateItem($template_type_list[0]["template_code"]);
        $this->assign("template_type_list", $template_type_list);
        $this->assign("template_json", json_encode($template_type_list));
        $this->assign("template_select", $template_type_list[0]);
        $this->assign("template_item_list", $template_item_list);
        $this->assign("template_send_item_json", json_encode($template_item_list));
        if ($type == "email") {
            return view($this->style . 'Config/notifyEmailTemplate');
        } else {
            return view($this->style . 'Config/notifySmsTemplate');
        }
    }

    /**
     * 得到可用的变量
     *
     * @return unknown
     */
    public function getTemplateItem()
    {
        $template_code = $_POST["template_code"];
        $config_service = new WebConfig();
        $template_item_list = $config_service->getNoticeTemplateItem($template_code);
        return $template_item_list;
    }

    /**
     * 更新通知模板
     *
     * @return multitype:unknown
     */
    public function updateNotifyTemplate()
    {
        $template_code = isset($_POST["type"]) ? $_POST["type"] : "email";
        $template_data = isset($_POST["template_data"]) ? $_POST["template_data"] : "";
        $shop_id = $this->instance_id;
        $config_service = new WebConfig();
        $retval = $config_service->updateNoticeTemplate($shop_id, $template_code, $template_data);
        return AjaxReturn($retval);
    }

    /**
     * 会员提现设置
     *
     * @return multitype:number unknown |Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function memberWithdrawSetting()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $key = 'WITHDRAW_BALANCE';
            $value = array(
                'withdraw_cash_min' => $_POST['cash_min'] ? $_POST['cash_min'] : 0,
                'withdraw_multiple' => $_POST['multiple'] ? $_POST['multiple'] : 1,
                'withdraw_poundage' => $_POST['poundage'] ? $_POST['poundage'] : 0,
                'withdraw_message' => $_POST['message'] ? $_POST['message'] : ''
            );
            $is_use = $_POST['is_use'];
            $config_service = new WebConfig();
            $retval = $config_service->setBalanceWithdrawConfig($shop_id, $key, $value, $is_use);
            return AjaxReturn($retval);
        } else {
            $shop_id = $this->instance_id;
            $config_service = new WebConfig();
            $list = $config_service->getBalanceWithdrawConfig($shop_id);
            if (empty($list)) {
                $list['id'] = '';
                $list['value']['withdraw_cash_min'] = '';
                $list['value']['withdraw_multiple'] = '';
                $list['value']['withdraw_poundage'] = '';
                $list['value']['withdraw_message'] = '';
            }
            $this->assign("list", $list);
            return view($this->style . "Config/memberWithdrawSetting");
        }
    }

    public function customservice()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $key = 'SERVICE_ADDR';
            $value = array(
                'service_addr' => $_POST['service_addr'] ? $_POST['service_addr'] : ''
            );
            $config_service = new WebConfig();
            $retval = $config_service->setcustomserviceConfig($shop_id, $key, $value);
            return AjaxReturn($retval);
        } else {
            $shop_id = $this->instance_id;
            $config_service = new WebConfig();
            $list = $config_service->getcustomserviceConfig($shop_id);
            if (empty($list)) {
                $list['id'] = '';
                $list['value']['service_addr'] = '';
            }
            $this->assign("list", $list);
            return view($this->style . "Config/customservice");
        }
    }

    /**
     * 首页商品促销板块是否开启设置
     */
    public function isrecommend()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $key = 'IS_RECOMMEND';
            $value = array(
                'is_recommend' => $_POST['is_recommend'] ? $_POST['is_recommend'] : '0'
            );
            $config_service = new WebConfig();
            $retval = $config_service->setisrecommendConfig($shop_id, $key, $value);
            return AjaxReturn($retval);
        }
    }

    /**
     * 首页商品分类是否显示设置
     */
    public function iscategory()
    {
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $key = 'IS_CATEGORY';
            $value = array(
                'is_category' => $_POST['is_category'] ? $_POST['is_category'] : '0'
            );
            $config_service = new WebConfig();
            $retval = $config_service->setiscategoryConfig($shop_id, $key, $value);
            return AjaxReturn($retval);
        }
    }

    /**
     * 用户提现审核
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function userCommissionWithdrawAudit()
    {
        $id = $_POST["id"];
        $status = $_POST["status"];
        $user = new User();
        $retval = $user->UserCommissionWithdrawAudit($this->instance_id, $id, $status);
        return AjaxReturn($retval);
    }

    /**
     * 支付
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function paymentConfig()
    {
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $pay_list = $config_service->getPayConfig($shop_id);
        $this->assign("pay_list", $pay_list);
        return view($this->style . 'Config/paymentConfig');
    }

    /**
     * 第三方登录页面
     */
    public function partyLogin()
    {
        $web_config = new WebConfig();
        // qq登录配置
        // 获取当前域名
        $domain_name = \think\Request::instance()->domain();
        // 获取回调域名qq回调域名
        $qq_call_back = $domain_name . \think\Request::instance()->root() . '/wap/login/callback';
        // 获取qq配置信息
        $qq_config = $web_config->getQQConfig($this->instance_id);
        // dump($qq_config);
        $qq_config['value']["AUTHORIZE"] = $domain_name;
        $qq_config['value']["CALLBACK"] = $qq_call_back;
        $qq_config['name'] = 'qq登录';
        $this->assign("qq_config", $qq_config);
        // 微信登录配置
        // 微信登录返回
        $wchat_call_back = $domain_name . \think\Request::instance()->root() . '/wap/Login/callback';
        $wchat_config = $web_config->getWchatConfig($this->instance_id);
        $wchat_config['value']["AUTHORIZE"] = $domain_name;
        $wchat_config['value']["CALLBACK"] = $wchat_call_back;
        $wchat_config['name'] = '微信登录';
        $this->assign("wchat_config", $wchat_config);
        return view($this->style . 'Config/partyLogin');
    }

    /**
     * 配送地区管理
     */
    public function distributionAreaManagement()
    {
        $child_menu_list = array(
            array(
                'url' => "express/expresscompany",
                'menu_name' => "物流公司",
                "active" => 0
            ),
            array(
                'url' => "config/areamanagement",
                'menu_name' => "地区管理",
                "active" => 0
            ),
            array(
                'url' => "order/returnsetting",
                'menu_name' => "商家地址",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointlist",
                'menu_name' => "自提点管理",
                "active" => 0
            ),
            array(
                'url' => "shop/pickuppointfreight",
                'menu_name' => "自提点运费",
                "active" => 0
            ),
            array(
                'url' => "config/distributionareamanagement",
                'menu_name' => "货到付款地区管理",
                "active" => 1
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        
        $dataAddress = new DataAddress();
        $provinceList = $dataAddress->getProvinceList();
        $cityList = $dataAddress->getCityList();
        foreach ($provinceList as $k => $v) {
            $arr = array();
            foreach ($cityList as $c => $co) {
                if ($co["province_id"] == $v['province_id']) {
                    $arr[] = $co;
                    unset($cityList[$c]);
                }
            }
            $provinceList[$k]['city_list'] = $arr;
        }
        $this->assign("list", $provinceList);
        $districtList = $dataAddress->getDistrictList();
        $this->assign("districtList", $districtList);
        $this->getDistributionArea();
        return view($this->style . "Config/distributionAreaManagement");
    }

    /**
     * 注册与访问
     */
    public function registerAndVisit()
    {
        $config_service = new WebConfig();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $is_register = request()->post('is_register', '');
            $register_info = request()->post('register_info', '');
            $register_info = empty($register_info) ? '' : rtrim($register_info, ',');
            $name_keyword = request()->post('name_keyword', '');
            $pwd_len = request()->post('pwd_len', '');
            $pwd_complexity = request()->post('pwd_complexity', '');
            $pwd_complexity = empty($pwd_complexity) ? '' : rtrim($pwd_complexity, ',');
            $terms_of_service = request()->post('terms_of_service', '');
            $is_requiretel = request()->post('is_requiretel', '');
            $is_use = request()->post('is_use', '1');
            $res = $config_service->setRegisterAndVisit($shop_id, $is_register, $register_info, $name_keyword, $pwd_len, $pwd_complexity, $terms_of_service, $is_requiretel, $is_use);
            return AjaxReturn($res);
        } else {
            $register_and_visit = $config_service->getRegisterAndVisit($this->instance_id);
            $this->assign('register_and_visit', json_decode($register_and_visit['value'], true));
            return view($this->style . "Config/registerAndVisit");
        }
    }

    /**
     * 获取配送地区设置
     */
    public function getDistributionArea()
    {
        $dataAddress = new DataAddress();
        $res = $dataAddress->getDistributionAreaInfo($this->instance_id);
        if ($res != '') {
            $this->assign("provinces", explode(',', $res['province_id']));
            $this->assign("citys", explode(',', $res['city_id']));
            $this->assign("districts", $res["district_id"]);
        }
    }

    /**
     * 通过ajax添加或编辑配送区域
     */
    public function addOrUpdateDistributionAreaAjax()
    {
        if (request()->isAjax()) {
            $dataAddress = new DataAddress();
            $shop_id = $this->instance_id;
            $province_id = request()->post("province_id", "");
            $city_id = request()->post("city_id", "");
            $district_id = request()->post("district_id", "");
            $res = $dataAddress->addOrUpdateDistributionArea($shop_id, $province_id, $city_id, $district_id);
            return AjaxReturn($res);
        }
    }

    /**
     * 物流跟踪
     */
    public function databaseList()
    {
        if (request()->isAjax()) {
            $web_config = new WebConfig();
            $database_list = $web_config->getDatabaseList();
            // 将所有建都转为小写
            $database_list = array_map('array_change_key_case', $database_list);
            foreach ($database_list as $k => $v) {
                $database_list[$k]["data_length_info"] = format_bytes($v['data_length']);
            }
            return $database_list;
        } else {
            $child_menu_list = array(
                array(
                    'url' => "Config/DatabaseList",
                    'menu_name' => "数据库备份",
                    "active" => 1
                ),
                array(
                    'url' => "Config/importDataList",
                    'menu_name' => "数据库恢复",
                    "active" => 0
                )
            );
            $this->assign('child_menu_list', $child_menu_list);
            return view($this->style . "Config/databaseList");
        }
    }

    public function expressMessage()
    {
        $child_menu_list = array(
            array(
                'url' => "config/webconfig",
                'menu_name' => "网站设置",
                "active" => 0
            ),
            array(
                'url' => "config/seoConfig",
                'menu_name' => "SEO设置",
                "active" => 0
            ),
            array(
                'url' => "config/codeconfig",
                'menu_name' => "验证码设置",
                "active" => 0
            ),
            array(
                'url' => "config/shopset",
                'menu_name' => "购物设置",
                "active" => 0
            ),
            array(
                'url' => "config/expressmessage",
                'menu_name' => "物流跟踪设置",
                "active" => 1
            ),
            array(
                'url' => "config/copyrightinfo",
                'menu_name' => "版权设置",
                "active" => 0
            )
        );
        
        $this->assign('child_menu_list', $child_menu_list);
        if (request()->isAjax()) {
            $config_service = new WebConfig();
            $shop_id = $this->instance_id;
            $appid = request()->post("appid", "");
            $appkey = request()->post("appkey", "");
            $back_url = request()->post('back_url', "");
            $is_use = request()->post("is_use", "");
            $res = $config_service->updateOrderExpressMessageConfig($shop_id, $appid, $appkey, $back_url, $is_use);
            return AjaxReturn($res);
        } else {
            $config_service = new WebConfig();
            $shop_id = $this->instance_id;
            $expressMessageConfig = $config_service->getOrderExpressMessageConfig($shop_id);
            $this->assign('emconfig', $expressMessageConfig);
            return view($this->style . "Config/expressMessage");
        }
    }

    /**
     * * 备份数据
     */
    public function exportDatabase()
    {
        $tables = isset($_POST['tables']) ? $_POST['tables'] : '';
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $start = isset($_POST['start']) ? $_POST['start'] : '';
        if (! empty($tables) && is_array($tables)) { // 初始化
                                                     // 读取备份配置
            $config = array(
                'path' => "runtime/dbsql/",
                'part' => 20971520,
                'compress' => 1,
                'level' => 9
            );
            // 检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if (is_file($lock)) {
                exit($lock . '检测到有一个备份任务正在执行，请稍后再试！');
            } else {
                $mode = intval('0777', 8);
                if (! file_exists($config['path']) || ! is_dir($config['path']))
                    mkdir($config['path'], $mode, true);
                    // 创建锁文件
                
                file_put_contents($lock, date('Ymd-His', time()));
            }
            // 自动创建备份文件夹
            // 检查备份目录是否可写
            is_writeable($config['path']) || exit('backup_not_exist_success');
            session('backup_config', $config);
            // 生成备份文件信息
            $file = array(
                'name' => date('Ymd-His', time()),
                'part' => 1
            );
            
            session('backup_file', $file);
            
            // 缓存要备份的表
            session('backup_tables', $tables);
            
            // 创建备份文件
            include 'data\extend\database.class.php';
            
            $database = new database($file, $config);
            if (false !== $database->create()) {
                $tab = array(
                    'id' => 0,
                    'start' => 0
                );
                $data = array();
                $data['status'] = 1;
                $data['message'] = '初始化成功';
                $data['tables'] = $tables;
                $data['tab'] = $tab;
                return $data;
            } else {
                exit('backup_set_error');
            }
        } elseif (is_numeric($id) && is_numeric($start)) { // 备份数据
            $tables = session('backup_tables');
            // 备份指定表
            include 'data\extend\database.class.php';
            $database = new database(session('backup_file'), session('backup_config'));
            $start = $database->backup($tables[$id], $start);
            if (false === $start) { // 出错
                exit('admin/backup_error');
            } elseif (0 === $start) { // 下一表
                if (isset($tables[++ $id])) {
                    $tab = array(
                        'id' => $id,
                        'table' => $tables[$id],
                        'start' => 0
                    );
                    $data = array();
                    $data['rate'] = 100;
                    $data['status'] = 1;
                    $data['info'] = '备份完成！';
                    $data['tab'] = $tab;
                    return $data;
                } else { // 备份完成，清空缓存
                    unlink("runtime/dbsql/" . 'backup.lock');
                    session('backup_tables', null);
                    session('backup_file', null);
                    session('backup_config', null);
                    exit('_operation_success_');
                }
            } else {
                $tab = array(
                    'id' => $id,
                    'table' => $tables[$id],
                    'start' => $start[0]
                );
                $rate = floor(100 * ($start[0] / $start[1]));
                $data = array();
                $data['status'] = 1;
                $data['rate'] = $rate;
                $data['info'] = "正在备份...({$rate}%)";
                $data['tab'] = $tab;
                return $data;
            }
        } else { // 出错
            exit('_param_error_');
        }
    }

    /**
     * 上传方式
     */
    public function uploadType()
    {
        $config_data = array();
        $web_config = new WebConfig();
        $upload_type = $web_config->getUploadType($this->instance_id);
        $config_data["type"] = $upload_type;
        // 获取七牛参数
        $config_qiniu_info = $web_config->getQiniuConfig($this->instance_id);
        $config_data["data"]["qiniu"] = $config_qiniu_info;
        $this->assign("config_data", $config_data);
        return view($this->style . "Config/uploadType");
    }

    /**
     * 修改上传类型
     */
    public function setUploadType()
    {
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $type = request()->post("type", "1");
        $result = $config_service->setUploadType($shop_id, $type);
        return AjaxReturn($result);
    }

    /**
     * 修改七牛配置
     */
    public function setQiniuConfig()
    {
        $config_service = new WebConfig();
        $shop_id = $this->instance_id;
        $Accesskey = request()->post("Accesskey", "");
        $Secretkey = request()->post("Secretkey", "");
        $Bucket = request()->post("Bucket", "");
        $QiniuUrl = request()->post("QiniuUrl", "");
        $value = array(
            "Accesskey" => $Accesskey,
            "Secretkey" => $Secretkey,
            "Bucket" => $Bucket,
            "QiniuUrl" => $QiniuUrl
        );
        $value = json_encode($value);
        $result = $config_service->setQiniuConfig($shop_id, $value);
        return AjaxReturn($result);
    }
}