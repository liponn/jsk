<?php
namespace data\service;

/**
 * 系统配置业务层
 */
use data\api\IConfig as IConfig;
use data\model\ConfigModel as ConfigModel;
use data\model\CustomTemplateModel;
use data\model\NoticeModel;
use data\model\NoticeTemplateItemModel;
use data\model\NoticeTemplateModel;
use data\model\NoticeTemplateTypeModel;
use data\service\BaseService as BaseService;
use think\Db;
use Qiniu\json_decode;

class Config extends BaseService implements IConfig
{

    private $config_module;

    function __construct()
    {
        parent::__construct();
        $this->config_module = new ConfigModel();
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IConfig::getWchatConfig()
     */
    public function getWchatConfig($instance_id)
    {   
        return Array
            (
                'value' => '{"APP_KEY":"","APP_SECRET":"","AUTHORIZE":"http:\/\/b2c1.01.niushop.com.cn","CALLBACK":"http:\/\/b2c1.01.niushop.com.cn\/wap\/Login\/callback"}',
                'is_use' => 0,
            );
        $info = $this->config_module->getInfo([
            'key' => 'WCHAT',
            'instance_id' => $instance_id
        ], 'value,is_use');
        if (empty($info['value'])) {
            return array(
                'value' => array(
                    'APP_KEY' => '',
                    'APP_SECRET' => '',
                    'AUTHORIZE' => '',
                    'CALLBACK' => ''
                ),
                'is_use' => 0
            );
        } else {
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IConfig::setWchatConfig()
     */
    public function setWchatConfig($instance_id, $appid, $appsecret, $url, $call_back_url, $is_use)
    {
        $info = array(
            'APP_KEY' => $appid,
            'APP_SECRET' => $appsecret,
            'AUTHORIZE' => $url,
            'CALLBACK' => $call_back_url
        );
        $value = json_encode($info);
        $count = $this->config_module->where([
            'key' => 'WCHAT',
            'instance_id' => $instance_id
        ])->count();
        if ($count > 0) {
            $data = array(
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => time()
            );
            $res = $this->config_module->where([
                'key' => 'WCHAT',
                'instance_id' => $instance_id
            ])->update($data);
            if ($res == 1) {
                return SUCCESS;
            } else {
                return UPDATA_FAIL;
            }
        } else {
            $data = array(
                'instance_id' => $instance_id,
                'key' => 'WCHAT',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => time()
            );
            $res = $this->config_module->save($data);
        }
        
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IConfig::getQQConfig()
     */
    public function getQQConfig($instance_id)
    {
        return Array
            (
                'value' => '{"APP_KEY":"","APP_SECRET":"","AUTHORIZE":"http:\/\/b2c1.01.niushop.com.cn","CALLBACK":"http:\/\/b2c1.01.niushop.com.cn\/wap\/login\/callback"}',
                'is_use' => 0,
            );
        $info = $this->config_module->getInfo([
            'key' => 'QQLOGIN',
            'instance_id' => $instance_id
        ], 'value,is_use');
        if (empty($info['value'])) {
            return array(
                'value' => array(
                    'APP_KEY' => '',
                    'APP_SECRET' => '',
                    'AUTHORIZE' => '',
                    'CALLBACK' => ''
                ),
                'is_use' => 0
            );
        } else {
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
        
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IConfig::setQQConfig()
     */
    public function setQQConfig($instance_id, $appkey, $appsecret, $url, $call_back_url, $is_use)
    {
        $info = array(
            'APP_KEY' => $appkey,
            'APP_SECRET' => $appsecret,
            'AUTHORIZE' => $url,
            'CALLBACK' => $call_back_url
        );
        $value = json_encode($info);
        $count = $this->config_module->where([
            'key' => 'QQLOGIN',
            'instance_id' => $instance_id
        ])->count();
        if ($count > 0) {
            $data = array(
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => time()
            );
            $res = $this->config_module->where([
                'key' => 'QQLOGIN',
                'instance_id' => $instance_id
            ])->update($data);
            if ($res == 1) {
                return SUCCESS;
            } else {
                return UPDATA_FAIL;
            }
        } else {
            $data = array(
                'instance_id' => $instance_id,
                'key' => 'QQLOGIN',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => time()
            );
            $res = $this->config_module->save($data);
            return $res;
        }
        // TODO Auto-generated method stub
    }

    public function getLoginConfig()
    {
        $instance_id = 0;
        $wchat_config = $this->getWchatConfig($instance_id);
        $qq_config = $this->getQQConfig($instance_id);
        
        $mobile_config = $this->getMobileMessage($instance_id);// lbid
        $email_config = $this->getEmailMessage($instance_id);//lbid
        $data = array(
            'wchat_login_config' => $wchat_config,
            'qq_login_config' => $qq_config,
            'mobile_config' => $mobile_config,
            'email_config' => $email_config
        );
        return $data;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IConfig::getWpayConfig()
     */
    public function getWpayConfig($instance_id)
    {
        $info = $this->config_module->getInfo([
            'instance_id' => $instance_id,
            'key' => 'WPAY'
        ], 'value,is_use');
        if (empty($info['value'])) {
            return array(
                'value' => array(
                    'appid' => '',
                    'appkey' => '',
                    'mch_id' => '',
                    'mch_key' => ''
                ),
                'is_use' => 0
            );
        } else {
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IConfig::setWpayConfig()
     */
    public function setWpayConfig($instanceid, $appid, $appkey, $mch_id, $mch_key, $is_use)
    {
        $data = array(
            'appid' => $appid,
            'appkey' => $appkey,
            'mch_id' => $mch_id,
            'mch_key' => $mch_key
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'WPAY',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'WPAY',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'WPAY',
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => $instanceid,
                'key' => 'WPAY'
            ]);
        }
        return $res;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IConfig::getAlipayConfig()
     */
    public function getAlipayConfig($instance_id)
    {
        $info = $this->config_module->getInfo([
            'instance_id' => $instance_id,
            'key' => 'ALIPAY'
        ], 'value,is_use');
        if (empty($info['value'])) {
            return array(
                'value' => array(
                    'ali_partnerid' => '',
                    'ali_seller' => '',
                    'ali_key' => ''
                ),
                'is_use' => 0
            );
        } else {
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IConfig::setAlipayConfig()
     */
    public function setAlipayConfig($instanceid, $partnerid, $seller, $ali_key, $is_use)
    {
        $data = array(
            'ali_partnerid' => $partnerid,
            'ali_seller' => $seller,
            'ali_key' => $ali_key
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'ALIPAY',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'ALIPAY',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'ALIPAY',
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => $instanceid,
                'key' => 'ALIPAY'
            ]);
        }
        return $res;
        // TODO Auto-generated method stub
    }

    /**
     * 设置微信和支付宝开关状态
     */
    public function setWpayStatusConfig($instanceid, $is_use, $type)
    {
        $config_module = new ConfigModel();
        $result = $config_module->getInfo([
            'instance_id' => $instanceid,
            'key' => $type
        ], 'value');
        if (empty($result['value'])) {
            $info = array();
            $value = json_encode($info);
            $data = array(
                'is_use' => $is_use,
                'modify_time' => time(),
                'value' => $value
            );
        } else {
            $data = array(
                'is_use' => $is_use,
                'modify_time' => time()
            );
        }
        $res = $config_module->save($data, [
            'instance_id' => $instanceid,
            'key' => $type
        ]);
        return $res;
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getHotsearchConfig()
     */
    public function getHotsearchConfig($instanceid)
    {
        $info = $this->config_module->getInfo([
            'key' => 'HOTKEY',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info['value'])) {
            return null;
        } else {
            return json_decode($info['value'], true);
        }
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \ata\api\IConfig::setHotsearchConfig()
     */
    public function setHotsearchConfig($instanceid, $keywords, $is_use)
    {
        $data = $keywords;
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'HOTKEY',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'HOTKEY',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => $instanceid,
                'key' => 'HOTKEY'
            ]);
        }
        return $res;
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::getDefaultSearchConfig()
     */
    public function getDefaultSearchConfig($instanceid)
    {
        $info = $this->config_module->getInfo([
            'key' => 'DEFAULTKEY',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info['value'])) {
            return null;
        } else {
            return json_decode($info['value'], true);
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::setDefaultSearchConfig()
     */
    public function setDefaultSearchConfig($instanceid, $keywords, $is_use)
    {
        $data = $keywords;
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'DEFAULTKEY',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'DEFAULTKEY',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => $instanceid,
                'key' => 'DEFAULTKEY'
            ]);
        }
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::getUserNotice()
     */
    public function getUserNotice($instanceid)
    {
        $info = $this->config_module->getInfo([
            'key' => 'USERNOTICE',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info['value'])) {
            return null;
        } else {
            return json_decode($info['value'], true);
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::setUserNotice()
     */
    public function setUserNotice($instanceid, $keywords, $is_use)
    {
        $data = $keywords;
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'USERNOTICE',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'USERNOTICE',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => $instanceid,
                'key' => 'USERNOTICE'
            ]);
        }
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::getEmailMessage()
     */
    public function getEmailMessage($instanceid)
    {
        return array(
                'value' => array(
                    'email_host' => 'smtp.163.com',
                    'email_port' => '465',
                    'email_addr' => '',
                    'email_pass' => '',
                    'email_id' => '',
                    'email_is_security' => false,
                ),
                'is_use' => 0
            );
        $info = $this->config_module->getInfo([
            'key' => 'EMAILMESSAGE',
            'instance_id' => $instanceid
        ], 'value, is_use');
        if (empty($info['value'])) {
            return array(
                'value' => array(
                    'email_host' => '',
                    'email_port' => '',
                    'email_addr' => '',
                    'email_pass' => '',
                    'email_id' => '',
                    'email_is_security' => false,
                ),
                'is_use' => 0
            );
        } else {
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::setEmailMessage()
     */
    public function setEmailMessage($instanceid, $email_host, $email_port, $email_addr, $email_id, $email_pass, $is_use, $email_is_security)
    {
        $data = array(
            'email_host' => $email_host,
            'email_port' => $email_port,
            'email_addr' => $email_addr,
            'email_id' => $email_id,
            'email_pass' => $email_pass,
            'email_is_security' => $email_is_security,
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'EMAILMESSAGE',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'EMAILMESSAGE',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'EMAILMESSAGE',
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => $instanceid,
                'key' => 'EMAILMESSAGE'
            ]);
        }
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::getMobileMessage()
     */
    public function getMobileMessage($instanceid)
    {
        return array(
                'value' => array(
                    'appKey' => '',
                    'secretKey' => '',
                    'freeSignName' => ''
                ),
                'is_use' => 0
            );
        $info = $this->config_module->getInfo([
            'key' => 'MOBILEMESSAGE',
            'instance_id' => $instanceid
        ], 'value, is_use');
        if (empty($info['value'])) {
            return array(
                'value' => array(
                    'appKey' => '',
                    'secretKey' => '',
                    'freeSignName' => ''
                ),
                'is_use' => 0
            );
        } else {
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::setMobileMessage()
     */
    public function setMobileMessage($instanceid, $app_key, $secret_key, $free_sign_name, $is_use, $user_type)
    {
        $data = array(
            'appKey' => $app_key,
            'secretKey' => $secret_key,
            'freeSignName' => $free_sign_name,
            'user_type' => $user_type
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'MOBILEMESSAGE',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'MOBILEMESSAGE',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'MOBILEMESSAGE',
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => $instanceid,
                'key' => 'MOBILEMESSAGE'
            ]);
        }
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::getWinxinOpenPlatformConfig()
     */
    public function getWinxinOpenPlatformConfig($instanceid)
    {
        $info = $this->config_module->getInfo([
            'key' => 'WXOPENPLATFORM',
            'instance_id' => $instanceid
        ], 'value, is_use');
        if (empty($info['value'])) {
            return array(
                'value' => array(
                    'appId' => '',
                    'appsecret' => '',
                    'encodingAesKey' => '',
                    'tk' => ''
                ),
                'is_use' => 1
            );
        } else {
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \ata\api\IConfig::setWinxinOpenPlatformConfig()
     */
    public function setWinxinOpenPlatformConfig($instanceid, $appid, $appsecret, $encodingAesKey, $tk)
    {
        $data = array(
            'appId' => $appid,
            'appsecret' => $appsecret,
            'encodingAesKey' => $encodingAesKey,
            'tk' => $tk
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'WXOPENPLATFORM',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'WXOPENPLATFORM',
                'value' => $value,
                'is_use' => 1,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'WXOPENPLATFORM',
                'value' => $value,
                'is_use' => 1,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => $instanceid,
                'key' => 'WXOPENPLATFORM'
            ]);
        }
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::getLoginVerifyCodeConfig()
     */
    public function getLoginVerifyCodeConfig($instanceid)
    {
        // $info = $this->config_module->getInfo([
        //     'key' => 'LOGINVERIFYCODE',
        //     'instance_id' => $instanceid
        // ], 'value, is_use');
        if (empty($info['value'])) {
            return array(
                'value' => array(
                    'platform' => 0,
                    'admin' => 0,
                    'pc' => 0
                ),
                'is_use' => 1
            );
        } else {
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::setLoginVerifyCodeConfig()
     */
    public function setLoginVerifyCodeConfig($instanceid, $platform, $admin, $pc)
    {
        $data = array(
            'platform' => $platform,
            'admin' => $admin,
            'pc' => $pc
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'LOGINVERIFYCODE',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'LOGINVERIFYCODE',
                'value' => $value,
                'is_use' => 1,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'LOGINVERIFYCODE',
                'value' => $value,
                'is_use' => 1,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => $instanceid,
                'key' => 'LOGINVERIFYCODE'
            ]);
        }
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::setInstanceWchatConfig()
     */
    public function setInstanceWchatConfig($instance_id, $appid, $appsecret)
    {
        $data = array(
            'appid' => $appid,
            'appsecret' => $appsecret
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'SHOPWCHAT',
            'instance_id' => $instance_id
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instance_id,
                'key' => 'SHOPWCHAT',
                'value' => $value,
                'is_use' => 1,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'SHOPWCHAT',
                'value' => $value,
                'is_use' => 1,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => $instance_id,
                'key' => 'SHOPWCHAT'
            ]);
        }
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::getInstanceWchatConfig()
     */
    public function getInstanceWchatConfig($instance_id)
    {
        $info = $this->config_module->getInfo([
            'key' => 'SHOPWCHAT',
            'instance_id' => $instance_id
        ], 'value');
        if (empty($info['value'])) {
            return array(
                'value' => array(
                    'appid' => '',
                    'appsecret' => ''
                ),
                'is_use' => 1
            );
        } else {
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::getOtherPayTypeConfig()
     */
    public function getOtherPayTypeConfig()
    {
        $info = $this->config_module->getInfo([
            'key' => 'OTHER_PAY',
            'instance_id' => 0
        ], 'value');
        if (empty($info['value'])) {
            return array(
                'value' => array(
                    'is_coin_pay' => 0,
                    'is_balance_pay' => 0
                ),
                'is_use' => 1
            );
        } else {
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::setOtherPayTypeConfig()
     */
    public function setOtherPayTypeConfig($is_coin_pay, $is_balance_pay)
    {
        $data = array(
            'is_coin_pay' => $is_coin_pay,
            'is_balance_pay' => $is_balance_pay
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo([
            'key' => 'OTHER_PAY',
            'instance_id' => 0
        ], 'value');
        if (empty($info)) {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => 0,
                'key' => 'OTHER_PAY',
                'value' => $value,
                'is_use' => 1,
                'create_time' => time()
            );
            $res = $config_module->save($data);
        } else {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'OTHER_PAY',
                'value' => $value,
                'is_use' => 1,
                'modify_time' => time()
            );
            $res = $config_module->save($data, [
                'instance_id' => 0,
                'key' => 'OTHER_PAY'
            ]);
        }
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::setNotice()
     */
    public function setNotice($shopid, $notice_message, $is_enable)
    {
        $notice = new NoticeModel();
        $data = array(
            'notice_message' => $notice_message,
            'is_enable' => $is_enable
        );
        $res = $notice->save($data, [
            'shopid' => $shopid
        ]);
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::getNotice()
     */
    public function getNotice($shopid)
    {
        $notice = new NoticeModel();
        $notice_info = $notice->getInfo([
            'shopid' => $shopid
        ]);
        if (empty($notice_info)) {
            $data = array(
                'shopid' => $shopid,
                'notice_message' => '',
                'is_enable' => 0
            );
            $notice->save($data);
            $notice_info = $notice->getInfo([
                'shopid' => $shopid
            ]);
        }
        return $notice_info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::getConfig()
     */
    public function getConfig($instance_id, $key)
    {
        $config = new ConfigModel();
        $array = array();
        $info = $config->getInfo([
            'instance_id' => $instance_id,
            'key' => $key
        ]);
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::setConfig()
     */
    public function setConfig($params)
    {
        $config = new ConfigModel();
        foreach ($params as $key => $value) {
            if ($this->checkConfigKeyIsset($value['instance_id'], $value['key'])) {
                $res = $this->updateConfig($value['instance_id'], $value['key'], $value['value'], $value['desc'], $value['is_use']);
            } else {
                $res = $this->addConfig($value['instance_id'], $value['key'], $value['value'], $value['desc'], $value['is_use']);
            }
        }
        return $res;
    }

    /**
     * 添加设置
     *
     * @param unknown $instance_id            
     * @param unknown $key            
     * @param unknown $value            
     * @param unknown $desc            
     * @param unknown $is_use            
     */
    protected function addConfig($instance_id, $key, $value, $desc, $is_use)
    {
        $config = new ConfigModel();
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $data = array(
            'instance_id' => $instance_id,
            'key' => $key,
            'value' => $value,
            'desc' => $desc,
            'is_use' => $is_use,
            'create_time' => time()
        );
        $res = $config->save($data);
        return $res;
    }

    /**
     * 修改配置
     *
     * @param unknown $instance_id            
     * @param unknown $key            
     * @param unknown $value            
     * @param unknown $desc            
     * @param unknown $is_use            
     */
    protected function updateConfig($instance_id, $key, $value, $desc, $is_use)
    {
        $config = new ConfigModel();
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $data = array(
            'value' => $value,
            'desc' => $desc,
            'is_use' => $is_use,
            'modify_time' => time()
        );
        $res = $config->save($data, [
            'instance_id' => $instance_id,
            'key' => $key
        ]);
        return $res;
    }

    /**
     * 判断当前设置是否存在
     * 存在返回 true 不存在返回 false
     *
     * @param unknown $instance_id            
     * @param unknown $key            
     */
    public function checkConfigKeyIsset($instance_id, $key)
    {
        $config = new ConfigModel();
        $num = $config->where([
            'instance_id' => $instance_id,
            'key' => $key
        ])->count();
        return $num > 0 ? true : false;
    }

    /**
     *
     * 得到店铺的系统通知的详情
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::getNoticeTemplateDetail()
     */
    public function getNoticeTemplateDetail($shop_id, $template_type)
    {
        $notice_template_model = new NoticeTemplateModel();
        $condition = array(
            "template_type" => $template_type,
            "instance_id" => $shop_id
        );
        $template_list = $notice_template_model->getQuery($condition, "*", "");
        return $template_list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::getNoticeTemplateOneDetail()
     */
    public function getNoticeTemplateOneDetail($shop_id, $template_type, $template_code)
    {
        $notice_template_model = new NoticeTemplateModel();
        $info = $notice_template_model->getInfo([
            'instance_id' => $shop_id,
            'template_type' => $template_type,
            'template_code' => $template_code
        ]);
        return $info;
    }

    /**
     * 更新通知模板的信息
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::updateNoticeTemplate()
     */
    public function updateNoticeTemplate($shop_id, $template_type, $template_array)
    {
        $template_data = json_decode($template_array, true);
        foreach ($template_data as $template_obj) {
            $template_code = $template_obj["template_code"];
            $template_title = $template_obj["template_title"];
            $template_content = $template_obj["template_content"];
            $sign_name = $template_obj["sign_name"];
            $is_enable = $template_obj["is_enable"];
            $notice_template_model = new NoticeTemplateModel();
            $t_count = $notice_template_model->getCount([
                "instance_id" => $shop_id,
                "template_type" => $template_type,
                "template_code" => $template_code
            ]);
            
            if ($t_count > 0) {
                // 更新
                $data = array(
                    "template_title" => $template_title,
                    "template_content" => $template_content,
                    "sign_name" => $sign_name,
                    "is_enable" => $is_enable,
                    "modify_time" => time()
                );
                $res = $notice_template_model->save($data, [
                    "instance_id" => $shop_id,
                    "template_type" => $template_type,
                    "template_code" => $template_code
                ]);
            } else {
                // 添加
                $data = array(
                    "instance_id" => $shop_id,
                    "template_type" => $template_type,
                    "template_code" => $template_code,
                    "template_title" => $template_title,
                    "template_content" => $template_content,
                    "sign_name" => $sign_name,
                    "is_enable" => $is_enable,
                    "modify_time" => time()
                );
                $res = $notice_template_model->save($data);
            }
        }
        return $res;
    }

    /**
     * 得到店铺的系统通知项
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::getNoticeConfig()
     */
    public function getNoticeConfig($shop_id)
    {
        $config_model = new ConfigModel();
        $condition = array(
            'instance_id' => $shop_id,
            'key' => array(
                'in',
                'EMAILMESSAGE,MOBILEMESSAGE'
            )
        );
        $notify_list = $config_model->getQuery($condition, "*", "");
        if (! empty($notify_list)) {
            for ($i = 0; $i < count($notify_list); $i ++) {
                if ($notify_list[$i]["key"] == "EMAILMESSAGE") {
                    $notify_list[$i]["notify_name"] = "邮件通知";
                } else 
                    if ($notify_list[$i]["key"] == "MOBILEMESSAGE") {
                        $notify_list[$i]["notify_name"] = "短信通知";
                    }
            }
            return $notify_list;
        } else {
            return null;
        }
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IConfig::getNoticeConfig()
     */
    public function getMobileConfig($shop_id)
    {
        $config_model = new ConfigModel();
        $condition = array(
            'instance_id' => $shop_id,
            'key' => 'MOBILEMESSAGE'
            
        );
        $notify_list = $config_model->getQuery($condition, "*", "");
        if (! empty($notify_list)) {
                if ($notify_list["key"] == "MOBILEMESSAGE") {
                    $notify_list["notify_name"] = "短信通知";
                }
           
            return $notify_list;
        } else {
            return null;
        }
    }
    
    /**
     * 得到店铺的email的配置信息
     *
     * @param unknown $shop_id            
     */
    public function getNoticeEmailConfig($shop_id)
    {
        $config_model = new ConfigModel();
        $condition = array(
            'instance_id' => $shop_id,
            'key' => 'EMAILMESSAGE'
        );
        $email_detail = $config_model->getQuery($condition, "*", "");
        return $email_detail;
    }

    /**
     * 得到店铺的短信配置信息
     *
     * @param unknown $shop_id            
     */
    public function getNoticeMobileConfig($shop_id)
    {
        $config_model = new ConfigModel();
        $condition = array(
            'instance_id' => $shop_id,
            'key' => 'MOBILEMESSAGE'
        );
        $mobile_detail = $config_model->getQuery($condition, "*", "");
        return $mobile_detail;
    }

    /**
     * 得到店铺的邮件发送项
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::getNoticeSendItem()
     */
    public function getNoticeTemplateItem($template_code)
    {
        $notice_model = new NoticeTemplateItemModel();
        $item_list = $notice_model->where("FIND_IN_SET('" . $template_code . "', type_ids)")->select();
        return $item_list;
    }

    /**
     * 得到店铺模板的集合
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::getNoticeTemplateType()
     */
    public function getNoticeTemplateType($template_type)
    {
        $notice_type_model = new NoticeTemplateTypeModel();
        $type_list = $notice_type_model->where("template_type='" . $template_type . "' or template_type='all'")->select();
        return $type_list;
    }

    /**
     * 支付的通知项
     *
     * @param unknown $shop_id            
     * @return string|NULL
     */
    public function getPayConfig($shop_id)
    {
        $config_model = new ConfigModel();
        $condition = array(
            'instance_id' => $shop_id,
            'key' => array(
                'in',
                'WPAY,ALIPAY'
            )
        );
        $notify_list = $config_model->getQuery($condition, "*", "");
        if (! empty($notify_list)) {
            for ($i = 0; $i < count($notify_list); $i ++) {
                if ($notify_list[$i]["key"] == "WPAY") {
                    $notify_list[$i]["logo"] = "public/admin/images/wchat.png";
                    $notify_list[$i]["pay_name"] = "微信支付";
                    $notify_list[$i]["desc"] = "该系统支持微信网页支付和扫码支付";
                } else 
                    if ($notify_list[$i]["key"] == "ALIPAY") {
                        $notify_list[$i]["pay_name"] = "支付宝支付";
                        $notify_list[$i]["logo"] = "public/admin/images/pay.png";
                        $notify_list[$i]["desc"] = "该系统支持即时到账接口";
                    }
            }
            return $notify_list;
        } else {
            return null;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::getBalanceWithdrawConfig()
     */
    public function getBalanceWithdrawConfig($shop_id)
    {
        $key = 'WITHDRAW_BALANCE';
        $info = $this->getConfig($shop_id, $key);
        if (empty($info)) {
            $params[0] = array(
                'instance_id' => $shop_id,
                'key' => $key,
                'value' => array(
                    'withdraw_cash_min' => 0.00,
                    'withdraw_multiple' => 0,
                    'withdraw_poundage' => 0,
                    'withdraw_message' => ''
                ),
                'desc' => '会员余额提现设置',
                'is_use' => 0
            );
            $this->setConfig($params);
            $info = $this->getConfig($shop_id, $key);
        }
        $info['value'] = json_decode($info['value'], true);
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ata\api\IConfig::setBalanceWithdrawConfig()
     */
    public function setBalanceWithdrawConfig($shop_id, $key, $value, $is_use)
    {
        $params[0] = array(
            'instance_id' => $shop_id,
            'key' => $key,
            'value' => array(
                'withdraw_cash_min' => $value['withdraw_cash_min'],
                'withdraw_multiple' => $value['withdraw_multiple'],
                'withdraw_poundage' => $value['withdraw_poundage'],
                'withdraw_message' => $value['withdraw_message']
            ),
            'desc' => '会员余额提现设置',
            'is_use' => $is_use
        );
        $res = $this->setConfig($params);
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::getcustomserviceConfig()
     */
    public function getcustomserviceConfig($shop_id)
    {
        $key = 'SERVICE_ADDR';
        $info = $this->getConfig($shop_id, $key);
        if (empty($info)) {
            $params[0] = array(
                'instance_id' => $shop_id,
                'key' => $key,
                'value' => array(
                    'service_addr' => ''
                ),
                'desc' => '美洽客服链接地址设置'
            );
            $this->setConfig($params);
            $info = $this->getConfig($shop_id, $key);
        }
        $info['value'] = json_decode($info['value'], true);
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::getrecommendConfig()
     */
    public function getrecommendConfig($shop_id)
    {
        $key = 'IS_RECOMMEND';
        $info = $this->getConfig($shop_id, $key);
        if (empty($info)) {
            $params[0] = array(
                'instance_id' => $shop_id,
                'key' => $key,
                'value' => array(
                    'is_recommend' => ''
                ),
                'desc' => '首页商城促销版块显示设置'
            );
            $this->setConfig($params);
            $info = $this->getConfig($shop_id, $key);
        }
        $info['value'] = json_decode($info['value'], true);
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::setisrecommendConfig()
     */
    public function setisrecommendConfig($shop_id, $key, $value)
    {
        $params[0] = array(
            'instance_id' => $shop_id,
            'key' => $key,
            'value' => array(
                'is_recommend' => $value['is_recommend']
            ),
            'desc' => '首页商品促销版块是否显示设置',
            'is_use' => 1
        );
        $res = $this->setConfig($params);
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::setiscategoryConfig()
     */
    public function setiscategoryConfig($shop_id, $key, $value)
    {
        $params[0] = array(
            'instance_id' => $shop_id,
            'key' => $key,
            'value' => array(
                'is_category' => $value['is_category']
            ),
            'desc' => '首页商品分类是否显示设置',
            'is_use' => 1
        );
        $res = $this->setConfig($params);
        return $res;
    }

    public function getcategoryConfig($shop_id)
    {
        $key = 'IS_CATEGORY';
        $info = $this->getConfig($shop_id, $key);
        if (empty($info)) {
            $params[0] = array(
                'instance_id' => $shop_id,
                'key' => $key,
                'value' => array(
                    'is_category' => ''
                ),
                'desc' => '首页商品分类是否显示设置'
            );
            $this->setConfig($params);
            $info = $this->getConfig($shop_id, $key);
        }
        $info['value'] = json_decode($info['value'], true);
        return $info;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::setcustomserviceConfig()
     */
    public function setcustomserviceConfig($shop_id, $key, $value)
    {
        $params[0] = array(
            'instance_id' => $shop_id,
            'key' => $key,
            'value' => array(
                'service_addr' => $value['service_addr']
            ),
            'desc' => '美洽客服链接地址'
        );
        $res = $this->setConfig($params);
        return $res;
    }

    public function getSeoConfig($shop_id)
    {
        return array(
                'seo_title' => '',
                'seo_meta' => '',
                'seo_desc' => '',
                'seo_other' => ''
            );
        $seo_title = $this->getConfig($shop_id, 'SEO_TITLE');
        $seo_meta = $this->getConfig($shop_id, 'SEO_META');
        $seo_desc = $this->getConfig($shop_id, 'SEO_DESC');
        $seo_other = $this->getConfig($shop_id, 'SEO_OTHER');
        if (empty($seo_title) || empty($seo_meta) || empty($seo_desc) || empty($seo_other)) {
            $this->SetSeoConfig($shop_id, '', '', '', '');
            $array = array(
                'seo_title' => '',
                'seo_meta' => '',
                'seo_desc' => '',
                'seo_other' => ''
            );
        } else {
            $array = array(
                'seo_title' => $seo_title['value'],
                'seo_meta' => $seo_meta['value'],
                'seo_desc' => $seo_desc['value'],
                'seo_other' => $seo_other['value']
            );
        }
        return $array;
    }

    public function getCopyrightConfig($shop_id)
    {
        $copyright_logo = $this->getConfig($shop_id, 'COPYRIGHT_LOGO');
        $copyright_meta = $this->getConfig($shop_id, 'COPYRIGHT_META');
        $copyright_link = $this->getConfig($shop_id, 'COPYRIGHT_LINK');
        $copyright_desc = $this->getConfig($shop_id, 'COPYRIGHT_DESC');
        $copyright_companyname = $this->getConfig($shop_id, 'COPYRIGHT_COMPANYNAME');
        if (empty($copyright_logo) || empty($copyright_meta) || empty($copyright_link) || empty($copyright_desc) || empty($copyright_companyname)) {
            $this->SetCopyrightConfig($shop_id, '', '', '', '', '');
            $array = array(
                'copyright_logo' => '',
                'copyright_meta' => '',
                'copyright_link' => '',
                'copyright_desc' => '',
                'copyright_companyname' => ''
            );
        } else {
            $array = array(
                'copyright_logo' => $copyright_logo['value'],
                'copyright_meta' => $copyright_meta['value'],
                'copyright_link' => $copyright_link['value'],
                'copyright_desc' => $copyright_desc['value'],
                'copyright_companyname' => $copyright_companyname['value']
            );
        }
        return $array;
    }

    public function getShopConfig($shop_id)
    {
        $order_auto_delinery = $this->getConfig($shop_id, 'ORDER_AUTO_DELIVERY');
        $order_balance_pay = $this->getConfig($shop_id, 'ORDER_BALANCE_PAY');
        $order_delivery_complete_time = $this->getConfig($shop_id, 'ORDER_DELIVERY_COMPLETE_TIME');
        $order_show_buy_record = $this->getConfig($shop_id, 'ORDER_SHOW_BUY_RECORD');
        $order_invoice_tax = $this->getConfig($shop_id, 'ORDER_INVOICE_TAX');
        $order_invoice_content = $this->getConfig($shop_id, 'ORDER_INVOICE_CONTENT');
        $order_delivery_pay = $this->getConfig($shop_id, 'ORDER_DELIVERY_PAY');
        $order_buy_close_time = $this->getConfig($shop_id, 'ORDER_BUY_CLOSE_TIME');
        $buyer_self_lifting = $this->getConfig($shop_id, 'BUYER_SELF_LIFTING');
        $seller_dispatching = $this->getConfig($shop_id, 'ORDER_SELLER_DISPATCHING');
        $is_logistics = $this->getConfig($shop_id, 'ORDER_IS_LOGISTICS');
        $shopping_back_points = $this->getConfig($shop_id, 'SHOPPING_BACK_POINTS');
        if (empty($order_auto_delinery) || empty($order_balance_pay) || empty($order_delivery_complete_time) || empty($order_show_buy_record) || empty($order_invoice_tax) || empty($order_invoice_content) || empty($order_delivery_pay) || empty($order_buy_close_time)) {
            $this->SetShopConfig($shop_id, '', '', '', '', '', '', '', '');
            $array = array(
                'order_auto_delinery' => '',
                'order_balance_pay' => '',
                'order_delivery_complete_time' => '',
                'order_show_buy_record' => '',
                'order_invoice_tax' => '',
                'order_invoice_content' => '',
                'order_delivery_pay' => '',
                'order_buy_close_time' => '',
                'buyer_self_lifting' => '',
                'seller_dispatching' => '',
                'is_logistics' => '1',
                'shopping_back_points' => ''
            );
        } else {
            $array = array(
                'order_auto_delinery' => $order_auto_delinery['value'],
                'order_balance_pay' => $order_balance_pay['value'],
                'order_delivery_complete_time' => $order_delivery_complete_time['value'],
                'order_show_buy_record' => $order_show_buy_record['value'],
                'order_invoice_tax' => $order_invoice_tax['value'],
                'order_invoice_content' => $order_invoice_content['value'],
                'order_delivery_pay' => $order_delivery_pay['value'],
                'order_buy_close_time' => $order_buy_close_time['value'],
                'buyer_self_lifting' => $buyer_self_lifting['value'],
                'seller_dispatching' => $seller_dispatching['value'],
                'is_logistics' => $is_logistics['value'],
                'shopping_back_points' => $shopping_back_points['value']
            );
        }
        if ($array['order_buy_close_time'] == 0) {
            $array['order_buy_close_time'] = 60;
        }
        
        return $array;
    }

    public function SetSeoConfig($shop_id, $seo_title, $seo_meta, $seo_desc, $seo_other)
    {
        $array[0] = array(
            'instance_id' => $shop_id,
            'key' => 'SEO_TITLE',
            'value' => $seo_title,
            'desc' => '标题附加字',
            'is_use' => 1
        );
        $array[1] = array(
            'instance_id' => $shop_id,
            'key' => 'SEO_META',
            'value' => $seo_meta,
            'desc' => '商城关键词',
            'is_use' => 1
        );
        $array[2] = array(
            'instance_id' => $shop_id,
            'key' => 'SEO_DESC',
            'value' => $seo_desc,
            'desc' => '关键词描述',
            'is_use' => 1
        );
        $array[3] = array(
            'instance_id' => $shop_id,
            'key' => 'SEO_OTHER',
            'value' => $seo_other,
            'desc' => '其他页头信息',
            'is_use' => 1
        );
        $res = $this->setConfig($array);
        return $res;
    }

    public function SetCopyrightConfig($shop_id, $copyright_logo, $copyright_meta, $copyright_link, $copyright_desc, $copyright_companyname)
    {
        $array[0] = array(
            'instance_id' => $shop_id,
            'key' => 'COPYRIGHT_LOGO',
            'value' => $copyright_logo,
            'desc' => '版权logo',
            'is_use' => 1
        );
        $array[1] = array(
            'instance_id' => $shop_id,
            'key' => 'COPYRIGHT_META',
            'value' => $copyright_meta,
            'desc' => '备案号',
            'is_use' => 1
        );
        $array[2] = array(
            'instance_id' => $shop_id,
            'key' => 'COPYRIGHT_LINK',
            'value' => $copyright_link,
            'desc' => '版权链接',
            'is_use' => 1
        );
        $array[3] = array(
            'instance_id' => $shop_id,
            'key' => 'COPYRIGHT_DESC',
            'value' => $copyright_desc,
            'desc' => '版权信息',
            'is_use' => 1
        );
        $array[4] = array(
            'instance_id' => $shop_id,
            'key' => 'COPYRIGHT_COMPANYNAME',
            'value' => $copyright_companyname,
            'desc' => '公司名称',
            'is_use' => 1
        );
        $res = $this->setConfig($array);
        return $res;
    }

    public function SetShopConfig($shop_id, $order_auto_delinery, $order_balance_pay, $order_delivery_complete_time, $order_show_buy_record, $order_invoice_tax, $order_invoice_content, $order_delivery_pay, $order_buy_close_time, $buyer_self_lifting, $seller_dispatching, $is_logistics, $shopping_back_points)
    {
        $array[0] = array(
            'instance_id' => $this->instance_id,
            'key' => 'ORDER_AUTO_DELIVERY',
            'value' => $order_auto_delinery,
            'desc' => '订单多长时间自动完成',
            'is_use' => 1
        );
        $array[1] = array(
            'instance_id' => $this->instance_id,
            'key' => 'ORDER_BALANCE_PAY',
            'value' => $order_balance_pay,
            'desc' => '是否开启余额支付',
            'is_use' => 1
        );
        $array[2] = array(
            'instance_id' => $this->instance_id,
            'key' => 'ORDER_DELIVERY_COMPLETE_TIME',
            'value' => $order_delivery_complete_time,
            'desc' => '收货后多长时间自动完成',
            'is_use' => 1
        );
        $array[3] = array(
            'instance_id' => $this->instance_id,
            'key' => 'ORDER_SHOW_BUY_RECORD',
            'value' => $order_show_buy_record,
            'desc' => '是否显示购买记录',
            'is_use' => 1
        );
        $array[4] = array(
            'instance_id' => $this->instance_id,
            'key' => 'ORDER_INVOICE_TAX',
            'value' => $order_invoice_tax,
            'desc' => '发票税率',
            'is_use' => 1
        );
        $array[5] = array(
            'instance_id' => $this->instance_id,
            'key' => 'ORDER_INVOICE_CONTENT',
            'value' => $order_invoice_content,
            'desc' => '发票内容',
            'is_use' => 1
        );
        $array[6] = array(
            'instance_id' => $this->instance_id,
            'key' => 'ORDER_DELIVERY_PAY',
            'value' => $order_delivery_pay,
            'desc' => '是否开启货到付款',
            'is_use' => 1
        );
        $array[7] = array(
            'instance_id' => $this->instance_id,
            'key' => 'ORDER_BUY_CLOSE_TIME',
            'value' => $order_buy_close_time,
            'desc' => '订单自动关闭时间',
            'is_use' => 1
        );
        $array[8] = array(
            'instance_id' => $this->instance_id,
            'key' => 'BUYER_SELF_LIFTING',
            'value' => $buyer_self_lifting,
            'desc' => '是否开启买家自提',
            'is_use' => 1
        );
        $array[9] = array(
            'instance_id' => $this->instance_id,
            'key' => 'ORDER_SELLER_DISPATCHING',
            'value' => $seller_dispatching,
            'desc' => '是否开启商家配送',
            'is_use' => 1
        );
        $array[10] = array(
            'instance_id' => $this->instance_id,
            'key' => 'ORDER_IS_LOGISTICS',
            'value' => $is_logistics,
            'desc' => '是否允许选择物流',
            'is_use' => 1
        );
        $array[11] = array(
            'instance_id' => $this->instance_id,
            'key' => 'SHOPPING_BACK_POINTS',
            'value' => $shopping_back_points,
            'desc' => '购物返积分设置',
            'is_use' => 1
        );
        
        $res = $this->setConfig($array);
        return $res;
    }

    public function SetIntegralConfig($shop_id, $register, $sign, $share)
    {
        $array[0] = array(
            'instance_id' => $shop_id,
            'key' => 'REGISTER_INTEGRAL',
            'value' => $register,
            'desc' => '注册送积分',
            'is_use' => 1
        );
        $array[1] = array(
            'instance_id' => $shop_id,
            'key' => 'SIGN_INTEGRAL',
            'value' => $sign,
            'desc' => '签到送积分',
            'is_use' => 1
        );
        $array[2] = array(
            'instance_id' => $shop_id,
            'key' => 'SHARE_INTEGRAL',
            'value' => $share,
            'desc' => '分享送积分',
            'is_use' => 1
        );
        $res = $this->setConfig($array);
        return $res;
    }

    public function getIntegralConfig($shop_id)
    {
        $register_integral = $this->getConfig($shop_id, 'REGISTER_INTEGRAL');
        $sign_integral = $this->getConfig($shop_id, 'SIGN_INTEGRAL');
        $share_integral = $this->getConfig($shop_id, 'SHARE_INTEGRAL');
        if (empty($register_integral) || empty($sign_integral) || empty($share_integral)) {
            $this->SetIntegralConfig($shop_id, '', '', '');
            $array = array(
                'register_integral' => '',
                'sign_integral' => '',
                'share_integral' => ''
            );
        } else {
            $array = array(
                'register_integral' => $register_integral['value'],
                'sign_integral' => $sign_integral['value'],
                'share_integral' => $share_integral['value']
            );
        }
        return $array;
    }

    /**
     * 修改状态
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::updateConfigEnable()
     */
    public function updateConfigEnable($id, $is_use)
    {
        $config_model = new ConfigModel();
        $data = array(
            "is_use" => $is_use,
            "modify_time" => time()
        );
        $retval = $config_model->save($data, [
            "id" => $id
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::getRegisterAndVisit()
     */
    public function getRegisterAndVisit($shop_id)
    {
        $register_and_visit = $this->getConfig($shop_id, 'REGISTERANDVISIT');
        if (empty($register_and_visit) || $register_and_visit == null) {
            // 按照默认值显示生成
            $value_array = array(
                'is_register' => "1",
                'register_info' => "plain",
                'name_keyword' => "",
                'pwd_len' => "5",
                'pwd_complexity' => "",
                'terms_of_service' => "",
                'is_requiretel' => 0
            );
            
            $data = array(
                'instance_id' => $shop_id,
                'key' => 'REGISTERANDVISIT',
                'value' => json_encode($value_array),
                'create_time' => time(),
                'is_use' => "1"
            );
            
            $config_model = new ConfigModel();
            $res = $config_model->save($data);
            if ($res > 0) {
                $register_and_visit = $this->getConfig($shop_id, 'REGISTERANDVISIT');
            }
        }
        return $register_and_visit;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::setRegisterAndVisit()
     */
    public function setRegisterAndVisit($shop_id, $is_register, $register_info, $name_keyword, $pwd_len, $pwd_complexity, $terms_of_service, $is_requiretel, $is_use)
    {
        $value_array = array(
            'is_register' => $is_register,
            'register_info' => $register_info,
            'name_keyword' => $name_keyword,
            'pwd_len' => $pwd_len,
            'pwd_complexity' => $pwd_complexity,
            'is_requiretel' => $is_requiretel,
            'terms_of_service' => $terms_of_service
        );
        
        $data = array(
            'value' => json_encode($value_array),
            'modify_time' => time(),
            'is_use' => $is_use
        );
        
        $config_model = new ConfigModel();
        $res = $config_model->save($data, [
            'key' => 'REGISTERANDVISIT',
            'instance_id' => $shop_id
        ]);
        return $res;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IConfig::databaseList()
     */
    public function getDatabaseList()
    {
        // TODO Auto-generated method stub
        $databaseList = Db::query("SHOW TABLE STATUS");
        return $databaseList;
    }

    /**
     * 查询物流跟踪的配置信息
     *
     * @param unknown $shop_id            
     */
    public function getOrderExpressMessageConfig($shop_id)
    {
        $express_detail = $this->config_module->getInfo([
            'instance_id' => $shop_id,
            'key' => 'ORDER_EXPRESS_MESSAGE'
        ], 'value,is_use');
        if (empty($express_detail['value'])) {
            return array(
                'value' => array(
                    'appid' => '',
                    'appkey' => '',
                    'back_url' => ''
                ),
                'is_use' => 0
            );
        } else {
            $express_detail['value'] = json_decode($express_detail['value'], true);
            return $express_detail;
        }
    }

    /**
     * 更新物流跟踪的配置信息
     *
     * @param unknown $shop_id            
     * @param unknown $appid            
     * @param unknown $appkey            
     * @param unknown $is_use            
     */
    public function updateOrderExpressMessageConfig($shop_id, $appid, $appkey, $back_url, $is_use)
    {
        $express_detail = $this->config_module->getInfo([
            'instance_id' => $shop_id,
            'key' => 'ORDER_EXPRESS_MESSAGE'
        ], 'value,is_use');
        $value = array(
            "appid" => $appid,
            "appkey" => $appkey,
            "back_url" => $back_url
        );
        $value = json_encode($value);
        $config_model = new ConfigModel();
        if (empty($express_detail)) {
            $data = array(
                "instance_id" => $shop_id,
                "key" => 'ORDER_EXPRESS_MESSAGE',
                "value" => $value,
                "create_time" => time(),
                "modify_time" => time(),
                "desc" => "物流跟踪配置信息",
                "is_use" => $is_use
            );
            $config_model->save($data);
            return $config_model->id;
        } else {
            $data = array(
                "key" => 'ORDER_EXPRESS_MESSAGE',
                "value" => $value,
                "modify_time" => time(),
                "is_use" => $is_use
            );
            $result = $config_model->save($data, [
                "instance_id" => $shop_id,
                "key" => "ORDER_EXPRESS_MESSAGE"
            ]);
            return $result;
        }
    }

    public function getUseWapTemplate($instanceid)
    {
        $config_model = new ConfigModel();
        $res = $config_model->getInfo([
            'key' => 'USE_WAP_TEMPLATE'
        ], 'value', '');
        return $res;
    }

    function setUseWapTemplate($instanceid, $folder)
    {
        $res = 0;
        $config_model = new ConfigModel();
        $info = $this->config_module->getInfo([
            'key' => 'USE_WAP_TEMPLATE',
            'instance_id' => $instanceid
        ], 'value');
        if (empty($info)) {
            $data['instance_id'] = $instanceid;
            $data['key'] = 'USE_WAP_TEMPLATE';
            $data['value'] = $folder;
            $data['create_time'] = time();
            $data['modify_time'] = time();
            $data['desc'] = '当前使用的手机模板文件夹';
            $data['is_use'] = 1;
            $res = $config_model->save($data);
        } else {
            $data['instance_id'] = $instanceid;
            $data['value'] = $folder;
            $data['modify_time'] = time();
            $res = $config_model->save($data, [
                'key' => 'USE_WAP_TEMPLATE'
            ]);
        }
        return $res;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IConfig::setPickupPointFreight()
     */
    function setPickupPointFreight($is_enable, $pickup_freight, $manjian_freight)
    {
        $config_value = array(
            'is_enable' => $is_enable,
            'pickup_freight' => $pickup_freight,
            'manjian_freight' => $manjian_freight
        );
        $config_key = 'PICKUPPOINT_FREIGHT';
        $config_info = $this->getConfig($this->instance_id, $config_key);
        if (empty($config_info)) {
            $res = $this->addConfig($this->instance_id, $config_key, json_encode($config_value), '自提点运费菜单配置', 1);
        } else {
            $res = $this->updateConfig($this->instance_id, $config_key, json_encode($config_value), '自提点运费菜单配置', 1);
        }
        return $res;
    }

    /**
     * 获取手机端自定义模板列表
     * 2017年8月14日 11:23:28
     *
     * {@inheritdoc}
     *
     * @see \data\api\IConfig::getCustomTeplateList()
     */
    function getCustomTemplateList($page_index, $page_size, $condition, $order, $field)
    {
        $custom_template_model = new CustomTemplateModel();
        $res = $custom_template_model->pageQuery($page_index, $page_size, $condition, $order, $field);
        return $res;
    }

    /**
     * 根据id获取手机端自定义模板
     * 2017年8月14日 11:23:24
     *
     * {@inheritdoc}
     *
     * @see \data\api\IConfig::getCustomTemplateById()
     */
    function getCustomTemplateById($id)
    {
        $custom_template_model = new CustomTemplateModel();
        $res = $custom_template_model->getInfo([
            "id" => $id
        ]);
        return $res;
    }

    /**
     * 获取默认手机端自定义模板
     * 创建时间：2017年8月14日 15:35:19
     *
     * {@inheritdoc}
     *
     * @see \data\api\IConfig::getDefaultCustomTemplate()
     */
    function getDefaultCustomTemplate($shop_id)
    {
        $custom_template_model = new CustomTemplateModel();
        $res = $custom_template_model->getInfo([
            "shop_id" => $shop_id,
            "is_default" => 1,
            "is_enable" => 1
        ]);
        return $res;
    }

    /**
     * 添加手机端自定义模板
     * 2017年7月31日 11:16:34
     *
     * {@inheritdoc}
     *
     * @see \data\api\IConfig::addCustomTemplate()
     */
    public function addCustomTemplate($shop_id, $template_name, $template_data)
    {
        $custom_template_model = new CustomTemplateModel();
        $data['shop_id'] = $shop_id;
        $data['template_name'] = $template_name;
        $data['template_data'] = $template_data;
        $data['modify_time'] = time();
        $data['create_time'] = time();
        $res = $custom_template_model->save($data);
        return $res;
    }

    /**
     * 修改手机端自定义模板
     * 2017年8月14日 12:09:11
     *
     * @param unknown $id            
     * @param unknown $shop_id            
     * @param unknown $template_name            
     * @param unknown $template_data            
     * @return boolean
     */
    public function updateCustomTemplate($id, $shop_id, $template_name, $template_data)
    {
        $custom_template_model = new CustomTemplateModel();
        $data['shop_id'] = $shop_id;
        $data['template_name'] = $template_name;
        $data['template_data'] = $template_data;
        $data['modify_time'] = time();
        $data['create_time'] = time();
        $res = $custom_template_model->save($data, [
            'id' => $id
        ]);
        return $res;
    }

    /**
     * 根据id删除手机端自定义模板
     * 2017年8月14日 12:00:07
     *
     * {@inheritdoc}
     *
     * @see \data\api\IConfig::deleteCustomTemplateById()
     */
    public function deleteCustomTemplateById($id)
    {
        $custom_template_model = new CustomTemplateModel();
        $res = $custom_template_model->destroy($id);
        return $res;
    }

    /**
     * 开启关闭自定义模板
     * 2017年8月9日 14:42:21
     *
     * @param 店铺id $shop_id            
     * @param 1：开启，0：禁用 $is_enable            
     */
    public function setIsEnableCustomTemplate($shop_id,$is_enable)
    {
        $custom_template_model = new CustomTemplateModel();
        $res = $custom_template_model->save([
            "is_enable" => $is_enable,
            "modify_time" => time()
        ], [
            "shop_id" => $shop_id
        ]);
        return $res;
    }

    /**
     * 设置默认模板
     * 创建时间：2017年8月14日 15:13:06
     *
     * {@inheritdoc}
     *
     * @see \data\api\IConfig::setDefaultCustomTemplate()
     */
    public function setDefaultCustomTemplate($id)
    {
        $custom_template_model = new CustomTemplateModel();
        $custom_template_model->save([
            "is_default" => 0,
            "modify_time" => time()
        ], [
            "id" => array(
                'NEQ',
                $id
            )
        ]);
        $res = $custom_template_model->save([
            "is_default" => 1,
            "modify_time" => time()
        ], [
            "id" => $id
        ]);
        return $res;
    }
	/* (non-PHPdoc)
     * @see \data\api\IConfig::getUploadType()
     */
    public function getUploadType($shop_id)
    {
        // TODO Auto-generated method stub
        $upload_type = $this->config_module->getInfo(["key"=>"UPLOAD_TYPE","instance_id"=>$shop_id],"*");
        if(empty($upload_type)){
            $res = $this->addConfig($shop_id, "UPLOAD_TYPE", 1, "上传方式 1 本地  2 七牛", 1);
            return 1;
        }else{
            return $upload_type['value'];
        }
    }

	/* (non-PHPdoc)
     * @see \data\api\IConfig::getQiniuConfig()
     */
    public function getQiniuConfig($shop_id)
    {
        // TODO Auto-generated method stub
        $qiniu_info = $this->config_module->getInfo(["key"=>"QINIU_CONFIG","instance_id"=>$shop_id],"*");
        if(empty($qiniu_info)){
            $data = array(
                "Accesskey"=>"",
                "Secretkey"=>"",
                "Bucket"=>"",
                "QiniuUrl"=>""
            );
            $res = $this->addConfig($shop_id, "QINIU_CONFIG", json_encode($data), "七牛云存储参数配置", 1);
            if(!$res > 0){
                return null;
            }else{
                $qiniu_info = $this->config_module->getInfo(["key"=>"QINIU_CONFIG","instance_id"=>$shop_id],"*");
            }
        }
        $value_info = $qiniu_info["value"];
        $value = json_decode($qiniu_info["value"], true);
        return $value;
        
    }
	/* (non-PHPdoc)
     * @see \data\api\IConfig::setUploadType()
     */
    public function setUploadType($shop_id, $value)
    {
        $upload_info = $this->config_module->getInfo(["key"=>"UPLOAD_TYPE","instance_id"=>$shop_id],"*");
        if(!empty($upload_info)){
            $data = array(
                "value"=>$value
            );
            $res = $this->config_module->save($data, ["instance_id"=>$shop_id, "key"=>"UPLOAD_TYPE"]);
        }else{
            $res = $this->addConfig($shop_id, "UPLOAD_TYPE", $value, "上传方式 1 本地  2 七牛", 1);
        }
        // TODO Auto-generated method stub
        
        return $res;
    }
    public function setQiniuConfig($shop_id, $value){
        $qiniu_info = $this->config_module->getInfo(["key"=>"QINIU_CONFIG","instance_id"=>$shop_id],"*");
        if(empty($qiniu_info)){
            $data = array(
                "Accesskey"=>"",
                "Secretkey"=>"",
                "Bucket"=>"",
                "QiniuUrl"=>""
            );
            $res = $this->addConfig($shop_id, "QINIU_CONFIG", json_encode($data), "七牛云存储参数配置", 1);            
        }else{
            $data = array(
                "value"=>$value
            );
            $res = $this->config_module->save($data, ["key"=>"QINIU_CONFIG","instance_id"=>$shop_id]);
        }
        return $res;
    }


}