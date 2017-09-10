$(function(){
	$.ajax({
		url : __URL(ADMINMAIN+"/task/load_task"),
		type : "post",
		data : {},
		dataType : "json",
		success : function(data) {
			
		}
	});
});