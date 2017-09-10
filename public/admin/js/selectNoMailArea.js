/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 王永杰
 * @date : 2017年6月26日 14:23:24
 * @version : v1.0.0.0
 * 运费模板编辑
 */
$(function(){
	
	//设置弹出地区的位置和宽度
	var top = ($(window).height()-$('#select-region').outerHeight())/2;
	$('#select-region').css({'top': top});
	
	/**
	 * 设置为默认模板
	 * 2017年6月27日 20:20:37 王永杰
	 */
	$("#is_default").change(function(){
		$("#hidden_is_default").val($(this).val());
		if(parseInt($(this).val())){
			$(".js-select-city").text("默认地区(全国)");
			$(".js-select-city").attr("data-flag",1);
			$(".js-select-city").addClass("btn-gradient-default").removeClass("btn-gradient-blue");
			$("#hidden_province_id_array").val("");
			$("#hidden_city_id_array").val("");
			$(".js-region-info").text("");
			$(".js-regions input[type='checkbox']").attr("checked",false);
		}else{
			$(".js-select-city").addClass("btn-gradient-blue").removeClass("btn-gradient-default");
			$(".js-select-city").text("指定地区城市");
			$(".js-select-city").attr("data-flag",0);
		}
	});
	
	
	//修改运费模板时，把弹出框的地区选择选中
	if(parseInt($("#hidden_shipping_fee_id").val())){
		
		if($("#hidden_province_id_array").val()){
			
			var province_id_array = $("#hidden_province_id_array").val().split(",");
			
			for(var i=0;i<province_id_array.length;i++){
				
				if(province_id_array[i]){
					$("input[data-second-parent-index][value='"+province_id_array[i]+"']").attr("checked",true);
				}
				
			}
		}
		
		if($("#hidden_city_id_array").val()){
			
			var city_id_array = $("#hidden_city_id_array").val().split(",");
			for(var j=0;j<city_id_array.length;j++){
				if(city_id_array[j]){
					$("input[data-third-parent-index][value='"+city_id_array[j]+"']").attr("checked",true);
				}
			}
		}

		var flag = false;
		$("input[data-second-parent-index]").each(function(){
			if(!$(this).is(":checked")){
				flag = true;
			}
		});
		
		
		
		/**
		 * 1.如果二级地区全部选中，父级则设为选中状态
		 * 2.如果二级地区全部禁用，父级则设为禁用状态
		 * 2017年6月29日 17:09:34 王永杰
		 */
		$("input[data-first-index]").each(function(q){
			
			var parent = $(this);
			var array = parent.val().substr(0,parent.val().length-1).split(",");//二级地区总长度
			var checked_count = 0;//选中的二级地区长度
			var disabled_count = 0;//禁用的二级地区长度
			$("input[data-second-parent-index='"+parent.attr("data-first-index")+"']").each(function(j){
				if($(this).is(":checked")){
					for(var i=0;i<array.length;i++){
						if(array[i]){
							if($(this).val() == array[i]){
								checked_count++;
								break;
							}
						}
					}
				}
				if($(this).is(":disabled") && $(this).attr("data-is-disabled")){
					disabled_count++;
				}
			});
			
			if(checked_count == array.length){
				parent.attr("checked",true);
			}
			
			if(disabled_count == array.length){
				parent.attr("disabled",true).attr("data-is-disabled",1);
				parent.next().addClass("disabled");
				
			}
			
		});
		
	}
	
	
	/**
	 * 指定地区城市[打开城市弹出框，进行选中]
	 * 2017年6月26日 15:59:32
	 */
	$(".js-select-city").click(function(){
		if(!parseInt($(this).attr("data-flag"))){
			$(".mask-layer").fadeIn(300);
			$('#select-region').fadeIn(300);
		}
	});
	
	
	/**
	 * 一级地区（大类）例如：华北、华东、东北、西北、港澳台等
	 * 根据当前地区的选中状态对应的改变它的子地区
	 * 2017年6月26日 15:29:55 王永杰
	 */
	$("input[data-first-index]").change(function(){
		
		if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
			
			var curr = $(this);//当前对象
			var index = curr.attr("data-first-index");//索引
			var checked = curr.is(":checked");//选中状态

			//省
			if($("input[data-second-parent-index='"+index+"']").length){
				
				$("input[data-second-parent-index='"+index+"']").each(function(){
					if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
						$(this).attr("checked",checked);
					}
				});
				
				//市
				if($("input[data-third-parent-index='"+index+"']").length){
					
					$("input[data-third-parent-index='"+index+"']").each(function(){
						if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
							$(this).attr("checked",checked);
						}
					});
					
				}
			}
		}
	});
	
	
	/**
	 * 二级地区（省）例如：山西省、山东省、河北省等
	 * 根据当前地区的选中状态对应的改变它的子地区
	 * 2017年6月26日 15:46:29 王永杰
	 */
	$("input[data-second-parent-index]").change(function(){
		
		var curr = $(this);//当前对象
		var checked = curr.is(":checked");//选中状态
		
		if(curr.parent().next().find("input[type='checkbox']").length){
			
			curr.parent().next().find("input[type='checkbox']").each(function(){
				if(!$(this).is(":disabled") && !$(this).attr("data-is-disabled")){
					$(this).attr("checked",checked);
				}
			});
			
		}
		
	});
	
	
	/**
	 * 三级地区（市区）例如：太原市、运城市等
	 * 只要改变了三级地区那它的上一级为不选中状态
	 * 2017年6月26日 16:23:15 王永杰
	 */
	$("input[data-third-parent-index").change(function(){
		
		var curr = $(this);//当前对象
		var index = curr.attr("data-third-parent-index");//索引
		var province_id = curr.attr("data-province-id");//省id
		$("input[data-second-parent-index='"+index+"'][data-province-id='"+province_id+"']").attr("checked",true);
	});
	
	
	/**
	 * 确定选择地区
	 * 2017年6月26日 17:14:59 王永杰
	 */
	$("#select-region .js-confirm").click(function(){
		setProvinceIdArray();
		setCityIdArray();
		$(".js-region-info").html(getRegions());
		$(".mask-layer").fadeOut(300);
		$('#select-region').fadeOut(300);
	});
	
	
	/**
	 * 取消选择地区
	 * 关闭选择地区弹出框
	 * 2017年6月26日 17:09:50 王永杰
	 */
	$("#select-region .js-cancle").click(function(){
		$(".mask-layer").fadeOut(300);
		$('#select-region').fadeOut(300);
	})
	
	
	//判断是否显示
	$(".drop-down").click(function () {
		
		var curr = $(this).parent();
		var is_visible = curr.next().is(":visible");
		if(!is_visible){
			curr.parent().addClass("showCityPop");
			curr.next().show();
		}else{
			curr.next().hide();
			curr.parent().removeClass("showCityPop");
		}
		
	});
	
	//关闭按钮
	$(".close_button").click(function () {
		$(this).parent().parent().css("display", "none");
		$(this).parent().parent().parent().removeClass("showCityPop");
	});
	
	
	var flag = false;
	/**
	 * 保存运费模板
	 * edit_button为旧界面
	 * btn-common为新界面
	 * 2017年6月26日 19:03:02 王永杰
	 */
	$(".edit_button,.btn-common").click(function(){
		if(validation()){
			if(flag){
				return;
			}
			flag = true;
			$.ajax({
				url : ADMINMAIN + "/express/freighttemplateedit",
				type : "post",
				data : { "data" : getData() },
				success : function(res){
					if (parseInt(res.code)) {
						showMessage('success', res.message,ADMINMAIN+'/Express/freightTemplateList?co_id='+$("#hidden_co_id").val());
					}else{
						showMessage('error',  res.message);
						flag = false;
					}
				}
			});
		}
	});
	
});


/**
 * 获取选中的地区（只显示省），逗号隔开
 * 2017年6月26日 18:09:55 王永杰
 */
function getRegions(){
	var regions_arr = new Array();
	if($(".js-regions input[data-second-parent-index]:checked").length){
		$(".js-regions input[data-second-parent-index]:checked").each(function(){
			regions_arr.push($(this).attr("data-province-name"));
		});
	}
	
	return regions_arr.toString().replace(",","&nbsp;,&nbsp;");
}

/**
 * 保存选中的省id组
 * @param id_arr 省id组
 */
function setProvinceIdArray(){
	
	var id_arr = new Array();

	if($(".js-regions input[data-second-parent-index]:checked").length){
		$(".js-regions input[data-second-parent-index]:checked").each(function(){
			id_arr.push($(this).val());
		});
	}
	$("#hidden_province_id_array").val(id_arr.toString());
}

/**
 * 保存选中的市id组
 * @param id_arr 
 */
function setCityIdArray(){
	
	var id_arr = new Array();
	if($(".js-regions input[data-third-parent-index]:checked").length){
		$(".js-regions input[data-third-parent-index]:checked").each(function(){
			id_arr.push($(this).val());
		});
	}
	$("#hidden_city_id_array").val(id_arr);// 市id
}