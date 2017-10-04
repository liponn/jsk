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

	public function getUserInfoByAuth($code){
		$url="https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token={$this->access_token}&code=$code";
		$content=curl_get($url);
		$ret= json_decode($content,true);
		return $ret;
	}

	public function getUserInfo($userId){
		$url="https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token={$this->access_token}&userid=$userId";
		$content=curl_get($url);
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