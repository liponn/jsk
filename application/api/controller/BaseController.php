<?php
namespace app\api\controller;

\think\Loader::addNamespace('data', 'data/');
use think\Controller;

class BaseController extends Controller
{

    public $api_result;
    
    public $user;
    
    public $style;
    
    protected $shop_name;
    
    protected $uid;
    
    protected $instance_id;
    /**
     * 当前版本的路径
     *
     * @var string
     */

    public function __construct()
    {
        parent::__construct();
        $this->api_result = new ApiResult();
        $this->init();
        
    }
    /**
     * 创建时间：2016-10-27
     * 功能说明：action基类 调用 加载头部数据的方法
     */
    public function init()
    {  
      $this->style = 'api/default/';
      $this->assign("platform_shopname", '手机端');
      $this->assign("style", 'wap/default' );
      $this->assign("platform_shopname", $this->shop_name); // 平台店铺名称
    }

    /**
     * 返回信息
     * @param unknown $res
     * @return \think\response\Json
     */
    public function outMessage($data_name, $data, $code = 0, $message = "success"){
//         dump(55);

        $this->api_result->code = $code;
        $this->api_result->data = array($data_name=>$data);
        $this->api_result->message = $message;
        
        if ($this->api_result) {
            return json($this->api_result);
        } else {
            abort(404);
        }
    }
    
    /**
     * 加密解密
     * @param unknown $string
     * @param unknown $operation
     * @param string $key
     * @return string
     */
    function encrypt($string,$operation,$key=''){
        $key=md5($key);
        $key_length=strlen($key);
        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for($i=0;$i<=255;$i++){
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++){
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++){
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if($operation=='D'){
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
                return substr($result,8);
            }else{
                return'';
            }
        }else{
            return str_replace('=','',base64_encode($result));
        }
    }
}

class ApiResult
{

    public $code;

    public $message;

    public $data;

    public function __construct()
    {
        $this->code = 0;
        $this->message = "success";
        $this->data = null;
    }

}