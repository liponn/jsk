<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:55:"template/wap/default/Goods/goodsClassificationList.html";i:1504947419;s:30:"template/wap/default/base.html";i:1505026012;s:34:"template/wap/default/urlModel.html";i:1504942797;s:37:"template/wap/default/controGroup.html";i:1504942796;s:32:"template/wap/default/footer.html";i:1505026518;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta name="renderer" content="webkit" />
<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1"/>
<meta content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<title><?php echo $platform_shopname; if($seoconfig['seo_title'] != ''): ?>-<?php echo $seoconfig['seo_title']; endif; ?></title>
<meta name="keywords" content="<?php echo $seoconfig['seo_meta']; ?>" />
<meta name="description" content="<?php echo $seoconfig['seo_desc']; ?>"/>
<link rel="shortcut  icon" type="image/x-icon" href="__TEMP__/<?php echo $style; ?>/public/images/favicon.ico" media="screen"/>
<link rel="stylesheet" type="text/css" href="__TEMP__/<?php echo $style; ?>/public/css/pre_foot.css">
<link rel="stylesheet" type="text/css" href="__TEMP__/<?php echo $style; ?>/public/css/pro-detail.css">
<link rel="stylesheet" type="text/css" href="__TEMP__/<?php echo $style; ?>/public/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="__TEMP__/<?php echo $style; ?>/public/css/showbox.css">
<link rel="stylesheet" href="__TEMP__/<?php echo $style; ?>/public/css/layer.css" id="layuicss-skinlayercss">
<script src="__TEMP__/<?php echo $style; ?>/public/js/showBox.js"></script>
<script src="__TEMP__/<?php echo $style; ?>/public/js/jquery.js"></script>
<script type="text/javascript" src="__TEMP__/<?php echo $style; ?>/public/js/layer.js"></script>
<script src="__STATIC__/js/load_task.js" type="text/javascript"></script>
<!-- <script src="__STATIC__/js/load_bottom.js" type="text/javascript"></script> //banquan-->
<script src="__STATIC__/js/time_common.js" type="text/javascript"></script>
<script type="text/javascript">
var CSS = "__TEMP__/<?php echo $style; ?>/public/css";
var APPMAIN='APP_MAIN';
var ADMINMAIN='ADMIN_MAIN';
var UPLOADAVATOR = 'UPLOAD_AVATOR';//存放用户头像
var UPLOADCOMMON = 'UPLOAD_COMMON';
var temp = "__TEMP__/";//外置JS调用
$(function(){
	showLoadMaskLayer();
})

$(document).ready(function(){
	hiddenLoadMaskLayer();
	//编写代码
});

//页面底部选中
function buttomActive(event){
	clearButton();
	if(event == "#buttom_home"){
		$("#buttom_home").find("img").attr("src","__TEMP__/<?php echo $style; ?>/public/images/home_check.png");
	}else if(event == "#buttom_classify"){
		$("#buttom_classify").find("img").attr("src","__TEMP__/<?php echo $style; ?>/public/images/classify_check.png");
	}else if(event == "#buttom_stroe"){
		$("#buttom_stroe").find("img").attr("src","__TEMP__/<?php echo $style; ?>/public/images/store_check.png");
	}else if(event == "#bottom_cart"){
		$("#bottom_cart").find("img").attr("src","__TEMP__/<?php echo $style; ?>/public/images/cart_check.png");
	}else if(event == "#bottom_member"){
		$("#bottom_member").find("img").attr("src","__TEMP__/<?php echo $style; ?>/public/images/user_check.png");
	}
}

function clearButton(){
	$("#buttom_home").find("img").attr("src","__TEMP__/<?php echo $style; ?>/public/images/home_uncheck.png");
	$("#buttom_classify").find("img").attr("src","__TEMP__/<?php echo $style; ?>/public/images/classify_uncheck.png");
	$("#buttom_stroe").find("img").attr("src","__TEMP__/<?php echo $style; ?>/public/images/store_uncheck.png");
	$("#bottom_cart").find("img").attr("src","__TEMP__/<?php echo $style; ?>/public/images/cart_uncheck.png");
	$("#bottom_member").find("img").attr("src","__TEMP__/<?php echo $style; ?>/public/images/user_uncheck.png");
}

//显示加载遮罩层
function showLoadMaskLayer(){
	$(".mask-layer-loading").fadeIn(300);
}

//隐藏加载遮罩层
function hiddenLoadMaskLayer(){
	$(".mask-layer-loading").fadeOut(300);
}
</script>
<style>
body .sub-nav.nav-b5 dd i {margin: 3px auto 5px auto;}
body .fixed.bottom {bottom: 0;}
.mask-layer-loading{position: fixed;width: 100%;height: 100%;z-index: 999999;top: 0;left: 0;text-align: center;display: none;}
.mask-layer-loading i,.mask-layer-loading img{text-align: center;color:#000000;font-size:50px;position: relative;top:50%;}
.sub-nav.nav-b5 dd{width:25%;font-size:14px;}

.modal {
    position: fixed;
    top: 25%;
    left: 8%;
    z-index: 1050;
    width: 74%;
    background-color: #ffffff;
    border: 1px solid #999;
    border: 1px solid rgba(0, 0, 0, 0.3);
    outline: none;
    -webkit-box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
    -moz-box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
    box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding-box;
    background-clip: padding-box;
}
.fade {
    opacity: 1;
    -webkit-transition: opacity 0.15s linear;
    -moz-transition: opacity 0.15s linear;
    -o-transition: opacity 0.15s linear;
    transition: opacity 0.15s linear;
	padding:0 16px;
	border-radius: 6px;
}
.modal-title{
	    height: 45px;
    line-height: 45px;
    text-align: center;
    border-bottom: 1px solid #eee;
    color: red;
    font-size: 17px;
    font-weight: normal;
}
.log-cont{
	margin-top: 15px;
    height: 40px;
    line-height: 40px;
    border: 1px solid #eee;
    background: #fff;
    border-radius: 3px;
	padding: 1px 5px;
	padding-left: 10px;
}
.loginbotton{
	margin-top: 25px;
    height: 38px;
    line-height: 38px;
    text-align: center;
    background: red;
    margin-bottom: 33px;
    border-radius: 3px;
}

.lang-btn{
    border: 0;
    background: red;
    color: #fff;
}
input:-webkit-autofill, textarea:-webkit-autofill, select:-webkit-autofill {
    background-color: rgba(217, 217, 217, 0.29);
}
input{
	border:0;
	background:#fff;
}
.getvilidate{
    border: 1px solid red;
    border-radius: 3px;
    color: red;
	padding: 0 5px;
    height: 25px;
    line-height: 25px;
    margin-top: 4px;
}



</style>

<link rel="stylesheet" type="text/css" href="__TEMP__/<?php echo $style; ?>/public/css/components.css">
<link rel="stylesheet" type="text/css" href="__TEMP__/<?php echo $style; ?>/public/css/group_goods_list.css">
<style>
.custom-search-button{top:6px;}
.category{width:95%;margin:10px auto;display: none;}
.category img{display: inline-block;width: 100%;}
.nothing-data{margin-top:50%;}
.custom-tag-list-goods dl{width:100%;}
.custom-tag-list-goods dt a {padding:0 10px;}
.custom-tag-list{height:92%;}
/* .category img{
	display: inline-block;
    vertical-align: middle;
    width: 100%;
    height: auto;
} */
/* .custom-tag-list,.custom-tag-list .custom-tag-list-menu-block,.custom-tag-list .custom-tag-list-goods{height:initial;}
 body{ 
	height:initial;
} */ 
</style>

</head>
<input type="hidden" id="niushop_rewrite_model" value="<?php echo rewrite_model(); ?>">
<input type="hidden" id="niushop_url_model" value="<?php echo url_model(); ?>">
<input type="hidden" id="niushop_admin_model" value="<?php echo admin_model(); ?>">
<script>
function __URL(url)
{
    url = url.replace('SHOP_MAIN', '');
    url = url.replace('APP_MAIN', 'wap');
    url = url.replace('ADMIN_MAIN', $("#niushop_admin_model"));
    if(url == ''|| url == null){
        return 'SHOP_MAIN';
    }else{
        var str=url.substring(0, 1);
        if(str=='/' || str=="\\"){
            url=url.substring(1, url.length);
        }
        if($("#niushop_rewrite_model").val()==1 || $("#niushop_rewrite_model").val()==true){
            return 'SHOP_MAIN/'+url;
        }
        var action_array = url.split('?');
        //检测是否是pathinfo模式
        url_model = $("#niushop_url_model").val();
        if(url_model==1 || url_model==true)
        {
            var base_url = 'SHOP_MAIN/'+action_array[0];
            var tag = '?';
        }else{
            var base_url = 'SHOP_MAIN?s=/'+ action_array[0];
            var tag = '&';
        }
        if(action_array[1] != '' && action_array[1] != null){
            return base_url + tag + action_array[1];
        }else{
        	 return base_url;
        }
    }
}
 /**
  * 处理图片路径
  */
 function __IMG(img_path){
  	var path = "";
 	if(img_path.indexOf("http://") == -1 && img_path.indexOf("https://") == -1){
 		path = "__UPLOAD__\/"+img_path;
 	}else{
 		path = img_path;
 	}
 	return path;
 }
</script>
<body class="body-gray">
	
<section class="head">
	<a class="head_back" id="head_back" href="<?php echo __URL('APP_MAIN'); ?>"><i class="icon-back"></i></a>
	<div class="head-title"><span style="margin-left: 40px;"><?php echo lang('classification_goods'); ?></span><style>
*{
	padding:0;
	margin:0;
}
.group{
	display: inline-block;
	width: 44px;
	height: 44px;
	background: #F7F7F7;
	float: right;
	text-align: center;
	border-left: 1px solid #F7F7F7;
}
.group img{
	width: 20px;
	margin-top: 15px;
}
.group-child{
	position: absolute;
	z-index: 10;
	width: 100%;
	height: 60px;
	background: #fff;
	margin-top: 1px;
	border-bottom: 1px solid #E2E2E2;
	display: none;
}
.group-child ul.gorup-nav{
	width: 100%;
}
.group-child ul.gorup-nav li{
	display: inline-block;
	width: 25%;
	height: 59px;
	float: left;
	text-align: center;
}
.group-child ul.gorup-nav li a{
	position: static;
	display: block;
}
.group-child ul.gorup-nav li a div{
	text-align: center;
	height: 54px;
	padding-top: 5px;
}
.group-child ul.gorup-nav li a div img{
	width: 28px;
	height: 28px;
	display: block;
	margin:0 auto 0 auto;
}
.group-child ul.gorup-nav li a div span.nav_name{
	font-size: 13px!important;
	height: 15px;
	display: block;
	color: #979899;
	margin-top: -10px;
}
</style>
<div class="group" data-show="false">
	<img src="__TEMP__/<?php echo $style; ?>/public/images/group.png" alt="">
</div>
<div class="group-child">
	<ul class="gorup-nav">
		<li>
			<a href="<?php echo __URL('APP_MAIN'); ?>">
				<div>
					<img src="__TEMP__/<?php echo $style; ?>/public/images/home_uncheck.png"/>
					<span class="nav_name"><?php echo lang('home_page'); ?></span>
				</div>
			</a>
		</li>
		<li>
			<a href="<?php echo __URL('APP_MAIN/goods/goodsclassificationlist'); ?>">
				<div>
					<img src="__TEMP__/<?php echo $style; ?>/public/images/classify_uncheck.png"/>
					<span class="nav_name"><?php echo lang('category'); ?></span>
				</div>
			</a>
		</li>
		<li>
			<a href="<?php echo __URL('APP_MAIN/goods/cart'); ?>">
				<div>
					<img src="__TEMP__/<?php echo $style; ?>/public/images/cart_uncheck.png"/>
					<span class="nav_name"><?php echo lang('goods_cart'); ?></span>
				</div>
			</a>
		</li>
		<li>
			<a href="<?php echo __URL('APP_MAIN/member/index'); ?>">
				<div>
					<img src="__TEMP__/<?php echo $style; ?>/public/images/user_uncheck.png"/>
					<span class="nav_name"><?php echo lang('member_member_center'); ?></span>
				</div>
			</a>
		</li>
	</ul>
</div>
<script>
	$(".group").click(function(){
			if($(this).attr("data-show") == "false"){
				$(this).css({"background":"#fff","border-bottom":"1px solid #fff","border-left":"1px solid #E2E2E2"});
				$(".group-child").slideDown();
				$(this).attr("data-show","true");
			}else{
				$(this).css({"background":"#F7F7F7","border-bottom":"none","border-left":"1px solid #F7F7F7"});
				$(".group-child").slideUp();
				$(this).attr("data-show","false");
			}
			
		}
	)
</script></div>
</section>

	<div class="motify" style="display: none;"><div class="motify-inner"><?php echo lang('pop_up_prompt'); ?></div></div>
	
	<!-- 平台商品分类 -->
	<!-- 搜索框样式 -->
<div class="custom-tag-list clearfix">
<!-- 	<div class="mask" id="grouGoodsListmask"></div> -->
	<?php if(count($goods_category_list_1)): ?>
	<div class="custom-tag-list-menu-block">
		<ul class="custom-tag-list-side-menu" style="position: relative;width:100%;background:#fff;" id='goods_group'>
			<?php if(is_array($goods_category_list_1) || $goods_category_list_1 instanceof \think\Collection || $goods_category_list_1 instanceof \think\Paginator): if( count($goods_category_list_1)==0 ) : echo "" ;else: foreach($goods_category_list_1 as $k=>$category): ?>
			<li val="<?php echo $category['category_id']; ?>">
				<a <?php if($k==0): ?> class="selected" <?php endif; ?> onclick="showCategorySecond(this,<?php echo $category['category_id']; ?>)" data-category-id="<?php echo $category['category_id']; ?>"><span><?php echo $category['short_name']; ?></span></a>
			</li>
			<?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
	<?php endif; if(count($goods_category_list_1)): ?>
	<div class="custom-tag-list-goods" id='good_list'>
		
		<!-- 一级分类频道 -->
		<?php if(is_array($goods_category_list_1) || $goods_category_list_1 instanceof \think\Collection || $goods_category_list_1 instanceof \think\Paginator): if( count($goods_category_list_1)==0 ) : echo "" ;else: foreach($goods_category_list_1 as $k=>$category): ?>
		<div class="category js-category-<?php echo $category['category_id']; ?>" <?php if($k==0): ?>style="display:block;"<?php endif; ?>>
		
			<div style="width:100%;background: #ffffff;text-align: center;">
			<?php if($category['category_pic']!=''): ?>
			<img src="<?php echo __IMG($category['category_pic']); ?>" alt="<?php echo $category['short_name']; ?>">
			<?php else: ?>
			<img src="__TEMP__/<?php echo $style; ?>/public/images/catagory.png" alt="<?php echo $category['short_name']; ?>">
			<?php endif; ?>
			</div>
			<a href="<?php echo __URL('APP_MAIN/goods/goodslist?category_id='.$category['category_id']); ?>" class="all" style="color: #FFF"><?php echo lang('get_into'); ?><?php echo $category['short_name']; ?><?php echo lang('frequency_channel'); ?>&nbsp;&gt;&nbsp;&gt;</a>
		</div>
		<?php endforeach; endif; else: echo "" ;endif; if(is_array($goods_category_list_2) || $goods_category_list_2 instanceof \think\Collection || $goods_category_list_2 instanceof \think\Paginator): if( count($goods_category_list_2)==0 ) : echo "" ;else: foreach($goods_category_list_2 as $k=>$category_second): ?>
			<dl class="js-category-<?php echo $category_second['pid']; ?>" <?php if($category_second['pid']!=$goods_category_list_1[0]['category_id']): ?>style="display: none;"<?php endif; ?>>
			<dt><a href="<?php echo __URL('APP_MAIN/goods/goodslist?category_id='.$category_second['category_id']); ?>"> <?php echo $category_second['short_name']; ?> </a></dt>
			<dd>
				<div class="catalog-box">
				<?php if(is_array($goods_category_list_3) || $goods_category_list_3 instanceof \think\Collection || $goods_category_list_3 instanceof \think\Paginator): if( count($goods_category_list_3)==0 ) : echo "" ;else: foreach($goods_category_list_3 as $key=>$category_third): if($category_second['category_id']==$category_third['pid']): ?>
					<div class="catalog-info">
						
						<a href="<?php echo __URL('APP_MAIN/goods/goodslist?category_id='.$category_third['category_id']); ?>">
							<?php if($category_third['category_pic']!=''): ?>
							<div style=" height:40px; width: 100%;overflow: hidden;">
								<img src="<?php echo __IMG($category_third['category_pic']); ?>" alt="<?php echo $category_third['short_name']; ?>" />
							</div>
							<?php else: ?>
							<div style=" height:40px; width: 100%;overflow: hidden;">
								<img src="__TEMP__/<?php echo $style; ?>/public/images/catagory-02.png" alt="<?php echo $category_third['short_name']; ?>" />
							</div>
							<?php endif; ?>
							<em style="margin:0;"><?php echo $category_third['short_name']; ?></em>
						</a>
					</div>
					<?php endif; endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</dd>
		</dl>
		<?php endforeach; endif; else: echo "" ;endif; ?>
		<div class="nothing-data js-children" align="center" style="display:none;">
			<img src="__TEMP__/<?php echo $style; ?>/public/images/wap_nodata.png"/>
			<div><?php echo lang('no_subcategories_for_current_commodity_classification'); ?>...</div>
		</div>
	</div>
	<?php else: ?>
		<div class="nothing-data" align="center">
			<img src="__TEMP__/<?php echo $style; ?>/public/images/wap_nodata.png"/>
			<div><?php echo lang('no_classification_of_goods_at_present'); ?>...</div>
		</div>
	<?php endif; ?>
</div>

<script>
$(function(){
	
	//默认显示选择的第一个商品分类数据
	if($("#goods_group li .selected").attr("data-category-id") != undefined){
		showCategorySecond($("#goods_group li .selected"),$("#goods_group li .selected").attr("data-category-id"));
	}
	
	//将没有第三级的商品分类过滤
	$("[class*='js-category']").each(function(){
		if($(this).find(".catalog-info").length == 0){
			$(this).find("dd").remove();
		}
	});
});
//显示二级分类
function showCategorySecond(obj,category_id){
	//设置选中效果
	$(".custom-tag-list-side-menu li a").removeClass("selected");
	$(obj).addClass("selected");
	$("[class*='js-category']").hide();
	$("[class$='js-category-"+category_id+"']").show();
	if($("dl[class='js-category-"+category_id+"']").length == 0){
		$(".js-children").show();
	}else{
		$(".js-children").hide();
	}
}
// $("#head_back").click(function (){
// 	var json ={
// 		"center" : "2"
// 	};
// 	window.webkit.messageHandlers.center.postMessage(json);
// })
</script>

	
		<!-- 底部菜单 -->
<div class="fixed bottom">
	<div class="distribution-tip" id="distribution-tip" style="display: none;"></div>
	<dl class="sub-nav nav-b5">
		<dd id="buttom_home">
			<a href="<?php echo __URL('APP_MAIN'); ?>">
				<div class="nav-b5-relative">
					<img src="__TEMP__/<?php echo $style; ?>/public/images/<?php if($footer_check == 'home_check'): ?>home_check.png<?php else: ?>home_uncheck.png<?php endif; ?>"/>
					<span><?php echo lang('home_page'); ?></span>
				</div>
			</a>
		</dd>
		<dd id="buttom_classify">
			<a href="<?php echo __URL('APP_MAIN/goods/goodsclassificationlist'); ?>">
				<div class="nav-b5-relative">
					<img src="__TEMP__/<?php echo $style; ?>/public/images/<?php if($footer_check == 'classify_check'): ?>classify_check.png<?php else: ?>classify_uncheck.png<?php endif; ?>"/>
					<span><?php echo lang('category'); ?></span>
				</div>
			</a>
		</dd>
		<dd id="bottom_cart">
			<a href="<?php echo __URL('APP_MAIN/goods/cart'); ?>">
				<div class="nav-b5-relative">
					<img src="__TEMP__/<?php echo $style; ?>/public/images/<?php if($footer_check == 'cart_check'): ?>cart_check.png<?php else: ?>cart_uncheck.png<?php endif; ?>"/>
					<span><?php echo lang('goods_cart'); ?></span>
				</div>
			</a>
		</dd>
		<dd id="bottom_member">
			<a href="<?php echo __URL('APP_MAIN/member/index'); ?>">
				<div class="nav-b5-relative">
					<img src="__TEMP__/<?php echo $style; ?>/public/images/<?php if($footer_check == 'user_check'): ?>user_check.png<?php else: ?>user_uncheck.png<?php endif; ?>"/>
					<span><?php echo lang('member_member_center'); ?></span>
				</div>
			</a>
		</dd>
	</dl>
</div>
	
	
	<input type="hidden" value="<?php echo $uid; ?>" id="uid"/>
	<!-- 加载弹出层 -->
	<div class="mask-layer-loading">
		<img src="__TEMP__/<?php echo $style; ?>/public/images/mask_load.gif"/>
	</div>
	

</body>
</html>