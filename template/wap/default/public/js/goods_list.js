$(function() {
	// 头部切换
	$(".filtrate-term  ul").find("li").click(function() {
		$(this).addClass("cur").siblings().removeClass("cur");
		if ($(this).index() != 1) {
			$(".filtrate-more").addClass("hide");
			$('.mask-div').hide();
		}
		// 综合排序
		if ($(this).index() == 0) {
			$(".filtrate-more").find("input[name='sort']").val(0);
			$(".filtrate-more").find("input[name='order']").val('');
			$('.filtrate-more').find('a').find('b').remove();
			$('.filtrate-more').find('a').removeClass('current');
			$('.filtrate-sort').find('em').html('排序');
		}
		if ($(this).index() == 1) {
			if ($(".filtrate-more").hasClass("hide")) {
				$(".filtrate-more").removeClass("hide");
				$('.mask-div').show();
				$('.arrow').removeClass('arrow_down').addClass('arrow_up');
			} else {
				$(".filtrate-more").addClass("hide");
				$('.arrow').removeClass('arrow_up').addClass('arrow_down');
				$('.mask-div').hide();
			}
		}
		if ($(this).index() == 2) {
			$('.filtrate-pop-section').show();
			var timer = setTimeout(function() {
				$('.filtrate-pop-section').addClass("show");
			}, 300);
			$('body').height("100%").css("overflow", "hidden");
		}
	});

	// 点击排序筛选后选取样式
	$('.filtrate-more').find('a').click(function() {
		$(".filtrate-more").find("input[name='sort']").val($(this).data('sort'));
		if ($(this).hasClass('current')) {
			if ($(".filtrate-more").find("input[name='order']").val() == 'DESC') {
				$(".filtrate-more").find("input[name='order']").val('ASC');
			} else {
				$(".filtrate-more").find("input[name='order']").val('DESC');
			}
		} else {
			$(".filtrate-more").find("input[name='order']").val('DESC');
		}
		$(this).addClass('current').parents('span').siblings().find('a').removeClass('current');
		$(this).parents('.filtrate-more').addClass('hide');
		$('.mask-div').hide();
		$(this).parents('.filtrate-more').find('a').find('b').remove();

		if ($(".filtrate-more").find("input[name='order']").val() == 'DESC') {
			$(this).append('<b class="icon-descending"></b>');
			$('.filtrate-sort').find('em').html($(this).data('name') + "由高到低");
		} else {
			$(this).append('<b class="icon-ascending"></b>');
			$('.filtrate-sort').find('em').html($(this).data('name') + "由低到高");
		}
		
		getgoodlist();
	});

});