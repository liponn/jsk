<?php
use think\Route;
use think\Cookie;
use think\Request;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//检测后台系统模块
     if(ADMIN_MODULE != 'admin')
    {
        Route::group(ADMIN_MODULE,function(){
            Route::rule(':controller/:action','admin/:controller/:action');
            Route::rule(':controller','admin/:controller/index');
            Route::rule('','admin/index/index');
          
        });
            Route::group('admin',function(){
                Route::rule(':controller/:action','shop/:controller/:action');
                Route::rule(':controller','shop/:controller/index');
                Route::rule('','shop/index/index');
            
            });
        
    }
    //检测入口文件
    $base_file = Request::instance()->baseFile();
    $alipay = strpos($base_file, 'alipay.php');
    $weixinpay = strpos($base_file, 'weixinpay.php');
    if($alipay)
    {
        Route::bind('wap/pay/aliUrlBack');
    }
    if($weixinpay)
    {
        Route::bind('wap/pay/wchatUrlBack');
    }
    //检测开启路由规则配置
    
    function getRouteConfig($type)
    {
        $route_config = array(
            //商品详情
            'GOODS'          =>  1,
            //商品列表
            'GOODSLIST'      =>  1,
            //品牌列表
            'BEAND'          =>  1,
            //会员中心
            'MEMBER'         =>  1
             
        );
        return $route_config[$type];
    }
    //检测浏览器类型以及显示方式(电脑端、手机端)
    function getShowModule(){
    
        $default_client = Cookie::get('default_client');
        if(!empty($default_client)){
            $default_client = Cookie::get('default_client');
        }else{
            if(Request::instance()->get('default_client') == 'shop'){
                $default_client = 'shop';
            }else{
                $default_client = 'wap';
            }
        }
        $is_mobile = Request::instance()->isMobile();
    
        if($is_mobile)
        {
            if($default_client == 'wap')
            {
                return 'wap';
            }else{
                return 'shop';
            }
        }else{
            if($default_client == 'wap')
            {
                return 'wap';
            }else{
                return 'shop';
            }
        }
    }
    $show_module = getShowModule();
    //pc端开启路由去除shop
    return [
        //pc端商品相关
        '[goods]'     => [
    
            //商品列表
            ':action'         => ['shop/goods/:action'],
            
        ],
        '[list]'     => [
        
            //商品列表
            '/'         => ['shop/goods/goodslist'],
        
        ],
        '[index]'     => [
    
            //商品列表
            ':action'         => ['shop/index/:action'],
            '/'               => ['shop/index/index'],
        ], 
        '[helpcenter]'     => [
        
            //商品列表
            ':action'         => ['shop/helpcenter/:action'],
            '/'               => ['shop/helpcenter/index'],
        ],
        '[login]'     => [
        
            //商品列表
            ':action'         => ['shop/login/:action'],
            '/'               => ['shop/login/index'],
        ],
        '[member]'     => [
        
            //商品列表
            ':action'         => ['shop/member/:action'],
            '/'               => ['shop/member/index'],
        ],
        '[components]'     => [
        
            //商品列表
            ':action'         => ['shop/components/:action'],
            '/'               => ['shop/components/index'],
        ],
        '[helpcenter]'     => [
        
            //商品列表
            ':action'         => ['shop/helpcenter/:action'],
            '/'               => ['shop/helpcenter/index'],
        ],
        '[order]'     => [
        
            //商品列表
            ':action'         => ['shop/order/:action'],
            '/'               => ['shop/order/index'],
        ],
        '[topic]'     => [
        
            //商品列表
            ':action'         => ['shop/topic/:action'],
            '/'               => ['shop/topic/index'],
        ],
        '[cms]'     => [
        
            //文章
            ':action'         => ['shop/cms/:action'],
            '/'               => ['shop/cms/index'],
        ],
        '[shop]'     => [
        
            ':controller/:action'         => ['shop/:controller/:action'],
            ':controller'         => ['shop/:controller/index'],
            '/'               => ['shop/index/index'],
        ]
         
    ];

//检测伪静态启用
  /*   if(getRouteConfig('GOODS'))
    {
        /* Route::group('goods_:goodsid',[
            '' => ['shop/goods/goodsinfo', ['method' => 'get'], ['goodsid' => '\d+']],
            ]); 
       // Route::get('goods/:goodsid','shop/goods/goodsinfo');
        Route::any('goods-:goodsid','shop/goods/goodsinfo/hoods',['method'=>'get']);
 
         
     
    } 

/* return [
     //pc端商品相关
     '[pcg]'     => [

        //商品列表
        'list'         => ['wap/goods/goodslist',['method' => 'get']],
        //商品详情
        '/'            => ['shop/goods/goodsinfo', ['method' => 'get']],
     ],
     //wap端商品相关
     '[wapg]'     => [
    
        //商品列表
        'list'         => ['wap/goods/goodslist'],
        //商品详情
        '/'            => ['wap/goods/goodsDetail', ['method' => 'get']],
        'point'        => ['wap/goods/integralcenter'],
        'classlist'    => ['wap/goods/goodsClassificationList'],
    ], 
    //pc端会员相关
    '[pcm]'     => [
        //会员中心
        'index'        => ['shop/member/index'],
        //会员余额
        'balance'      => ['shop/member/balancelist'],
        //会员积分
        'person'       => ['shop/member/person'],
        //会员地址
        'address'      => ['shop/member/addresslist'],
        //会员优惠券
        'vouchers'     => ['shop/member/vouchers'],
        //会员积分
        'point'        => ['shop/member/integrallist'],
        //订单列表
        'orders'       => ['shop/member/orderlist'],
        //退款与售后
        'refunds'      => ['shop/member/backlist'],
        ],
    
    
    
    '[login]'      => [
        //登录页面
        ''=> ['shop/login/index']
        ], 
  

   
]; */

