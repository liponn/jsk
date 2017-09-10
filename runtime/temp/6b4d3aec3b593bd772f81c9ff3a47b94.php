<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:41:"template/wap/default/Goods/goodsList.html";i:1504942796;s:30:"template/wap/default/base.html";i:1505026012;s:34:"template/wap/default/urlModel.html";i:1504942797;s:45:"template/wap/default/Index/controlSearch.html";i:1504942796;}*/ ?>
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

<link rel="stylesheet" type="text/css" href="__TEMP__/<?php echo $style; ?>/public/css/common.css">
<link rel="stylesheet" type="text/css" href="__TEMP__/<?php echo $style; ?>/public/css/components.css">
<link rel="stylesheet" type="text/css" href="__TEMP__/<?php echo $style; ?>/public/css/category.css">
<link rel="stylesheet" type="text/css" href="__TEMP__/<?php echo $style; ?>/public/css/group_goods_list.css">
<script src="__TEMP__/<?php echo $style; ?>/public/js/goods_list.js" type="text/javascript"></script>
<style>
.openList li dd {width: 90%;}
.openList li dl dt {font-size: 14px;width: 90%;height: 20px;overflow: hidden;}
.openList .goods-sales {font-size: 12px;}
.openList li dd i {font-size: 14px;}
.custom-search{width:90%;margin-left:20px;}
.custom-search .custom-search-input{width:97%;}
.custom-search-button{top:6px;}
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
	<a class="head_back" id="head_back"href="javascript:window.history.go(-1)"><i class="icon-back"></i></a>
	<div class="head-title"><script src="__TEMP__/<?php echo $style; ?>/public/js/public_assembly.js"></script>
<style>
/* .custom-search {width: 90%;margin-left: 20px;} */
/* .custom-search .custom-search-input{width:97%;} */
</style>
<div class="editing">
	<div class="control-group">
		<div class="custom-search" >
			<input type="text" class="custom-search-input" placeholder="<?php echo lang('search_goods'); ?>" style="background:#f4f4f4;border:none;border-radius:0;padding-right:10%;">
			<button type="button" class="custom-search-button"><?php echo lang('search'); ?></button>
			<input type="hidden" value="<?php echo $shop_id; ?>" id="hidden_shop_id"/>
		</div>
		<div class="component-border"></div>
	</div>
	<div class="sort">
		<i class="sort-handler"></i>
	</div>
</div></div>
</section>

	<div class="motify" style="display: none;"><div class="motify-inner"><?php echo lang('pop_up_prompt'); ?></div></div>
	
<div id="index_content">
<!--列表页内容start-->
<section class="category-content-section">
	<section class="filtrate-term ">
		<ul>
			<li class="cur"><a href="javascript:void(0)"><?php echo lang('comprehensive_ranking'); ?></a></li>
			<!--排序点击li标签增加样式cur；span标签增加样式active_down-->
			<li>
				<a href="javascript:void(0);" class="filtrate-sort">
					<em><?php echo lang('sort'); ?></em>
					<span class="arrow_down arrow"></span>
				</a>
			</li>
		</ul>
	</section>
	<div class="mask-div"></div>
	<div class="filtrate-more hide sale-num">
		<span><a href="javascript:void(0)" data-sort="1" data-name="<?php echo lang('goods_sales_volume'); ?>" class=""><?php echo lang('goods_sales_volume'); ?> </a></span>
		<span><a href="javascript:void(0)" data-sort="2" data-name="<?php echo lang('goods_new'); ?>" class=""><?php echo lang('goods_new'); ?> </a></span>
		<span><a href="javascript:void(0)" data-sort="3" data-name="<?php echo lang('goods_price'); ?>" class=""><?php echo lang('goods_price'); ?> </a></span>
		<input type="hidden" id='order' name="order"/>
		<input type="hidden" id='category_id' name='category_id' value="<?php echo $category_id; ?>" />
	</div>
	<div class="goods-list-grid openList">
		<div class="blank-div"></div>
		<div id="goods_list">
			<div class="tablelist-append clearfix"></div>
		</div>
	</div>
</section>
</div>

	
	
	<input type="hidden" value="<?php echo $uid; ?>" id="uid"/>
	<!-- 加载弹出层 -->
	<div class="mask-layer-loading">
		<img src="__TEMP__/<?php echo $style; ?>/public/images/mask_load.gif"/>
	</div>
	
<script type="text/javascript">
$(function(){
	getgoodlist()
})
function getgoodlist(){
	$('#grouGoodsListmask').hide();
	$('.two-list-menu').hide();
	$('.two-list-menu li[pid]').hide();
	var order=$('.filtrate-more span a.current').attr('data-sort');
	var sort=$(".filtrate-more").find("input[name='order']").val();
	$.ajax({
		type:"post",
		url : "<?php echo __URL('APP_MAIN/Goods/goodsList'); ?>",
		data : {'category_id':'<?php echo $category_id; ?>','brand_id':'<?php echo $brandz_id; ?>','order':order,'sort':sort },
		beforeSend:function(){
			showLoadMaskLayer();
		},
		success : function(data){
			var list_html="";
			for(var i=0;i<data['data'].length;i++){
				var item=data['data'][i];
				list_html+='<div class="product single_item info">'
							+'<li>'
								+'<div class="item">'
									+'<div class="item-tag-box">'
										+'<!--<?php echo lang("hot_sale"); ?>icon<?php echo lang("position_is"); ?>：0px 0px，<?php echo lang("goods_new"); ?>icon<?php echo lang("position_is"); ?>：0px -35px，<?php echo lang("competitive_products"); ?>icon<?php echo lang("position_is"); ?>：0px -70px-->'
									+'</div>'

									+'<div class="item-pic">'
										+'<a href="'+__URL('APP_MAIN/goods/goodsdetail?id='+item.goods_id)+'">'
											+'<img class="" src="'+__IMG(item.pic_cover_small)+'" alt="'+item.goods_name+'" style="display: block;max-width:100%;max-height:100%;">'
										+'</a>'
									+'</div>'

									+'<dl>'
										+'<dt>'
											+'<a href="'+__URL('APP_MAIN/goods/goodsdetail?id='+item.goods_id)+'">'+item.goods_name+'</a>'
										+'</dt>'
										+'<dd>'
											+'<i>￥'+item.promotion_price+'</i>'
										+'</dd>'
									+'</dl>'
								+'</div>'

								+'<div class="item-con-info">'
									+'<span class="goods-sales"> <?php echo lang("goods_sales_volume"); ?>：'+item.sales+'</span>'
									+'<div class="cart-box">'
										+'<a class="add-cart increase" data-goods_id="575" href="javascript:CartGoodsInfo('+item.goods_id+','+item.state+')"></a>'
										+'<a class="decrease hide" data-goods_id="575" style="right: 60px;"></a>'
									+'</div>'
								+'</div>'
							+'</li>'
						+'</div>';
		}
		$('.tablelist-append').html(list_html);
		hiddenLoadMaskLayer();
	}
	});
}
</script>
<script type="text/javascript">
$("#head_back").click(function (){
    var json ={
            "center" : "2"
        }
    window.webkit.messageHandlers.center.postMessage(json);
})
</script>


</body>
</html>