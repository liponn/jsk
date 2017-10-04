<?php
namespace data\extend;
use think\Cache as cache;
use think\Config;
//use data\service\Config;
use think;

class QyWchatOauth{
	public $_corpid;
	public $_secret;
	public $access_token;


	public function __construct(){
        $this->_corpid = Config::get('corpid');
        $this->_secret = Config::get('secret');
        if(cache::get('token-'.$this->_corpid) == false){
        	$this->s_get_access_token();
        }
        $this->access_token = cache::get('token-'.$this->_corpid);

	}

	public function get_member_access_token(){
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            //通过code获得openid
            if (empty($_GET['code'])){
                //触发微信返回code码
                $baseUrl = request()->url(true);
                $url = $this->get_single_authorize_url($baseUrl, "123");
            	// exit($url);
                Header("Location: $url");
                exit();
            }else{
                //获取code码，以获取userid
                $code = $_GET['code'];
        
                    $data = $this->getUserInfoByAuth($code);
                    return $data;
               
            }
    
        }
	}

	/**
     * 获取微信OAuth2授权链接snsapi_base
     * @param string $redirect_uri 跳转地址
     * @param mixed $state 参数
     * 不弹出授权页面，直接跳转，只能获取用户openid
     */
    public function get_single_authorize_url($redirect_url = '', $state = ''){
        $redirect_url = urlencode($redirect_url);
        // return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$wchat_config['value']['appid']."&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->_corpid}&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    }

	public function getUserInfoByAuth($code){
		$url="https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token={$this->access_token}&code=$code";
		$content=$this->curl_get($url);
		$ret= json_decode($content,true);
		return $ret;
	}

	public function getUserInfo($userId){
		$url="https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token={$this->access_token}&userid=$userId";
		$content=$this->curl_get($url);
		$ret= json_decode($content,true);
		return $ret;
	}


	private function curl_get($url){
		$ch = curl_init();
	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
	    
	    if(!curl_exec($ch))
	    {
	        error_log(curl_error($ch));
	        $data="";
	    }
	    else
	    {
	        $data=curl_multi_getcontent($ch);
	    }
	    
	    curl_close($ch);
	    return $data;
	}

	private function s_get_access_token(){
		$url="https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$this->_corpid}&corpsecret={$this->_secret}";
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

		$output=curl_exec($ch);

		curl_close($ch);
		$jsoninfo = json_decode($output,true);
        cache::set('token-'.$this->_corpid,$jsoninfo["access_token"],3600);

        return  $jsoninfo["access_token"];
	}
}

?>