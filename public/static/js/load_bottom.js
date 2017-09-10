/**
 *  功能描述：加载底部的版权信息
 */
$(function(){
	$.ajax({
		url : __URL(ADMINMAIN+"/task/copyrightisload"),
		type : "post",
		data : {},
		dataType : "json",
		success : function(data) {
			$is_load=data["is_load"];
			$bottom_info=data["bottom_info"];
			if($is_load>0){
				$("#copyright_logo").attr("src", data["default_logo"]);
				$("#copyright_meta").html("Copyright © 2015-2025 NIUSHOP开源商城&nbsp;版权所有 保留一切权利");
				$("#copyright_companyname").attr("href", "http://www.niushop.com.cn");
				$("#copyright_companyname").html("山西牛酷信息科技有限公司&nbsp;提供技术支持");
				$("#copyright_desc").html("400-886-7993");
			}else{
				$("#copyright_logo").attr("src", $bottom_info["copyright_logo"]);
				$("#copyright_logo_wap").attr("src", $bottom_info["copyright_logo"]);
				$("#copyright_meta").html($bottom_info["copyright_meta"]);
				$("#copyright_companyname").attr("href", $bottom_info["copyright_link"]);
				$("#copyright_companyname").html($bottom_info["copyright_companyname"]);
				$("#copyright_desc").html($bottom_info["copyright_desc"]);
			}
		}
	});
});