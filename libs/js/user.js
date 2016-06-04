$(document).ready(function() {
	$("#menu-toggle").hover(function() {
		$("#menu").stop().fadeToggle('fast');
	});

	$(".agenda .title").click(function(event) {
		$(".more").each(function() {
			if($(this).css('display').toLowerCase() == 'block') {
				$(this).slideUp('fast');
			}
		});
		$(this).siblings(".date").stop().fadeToggle('fast');
		$(this).siblings(".more").stop().slideToggle('fast');

		var _this = $(this);
		$(this).find("#dismiss").click(function(event) {
			_this.find(".date").stop().fadeIn('fast');
			_this.find(".more").stop().slideUp('fast');
			_this.find(".title").animate({
				'left': "0px"},
			400);
			event.stopPropagation();
		});
	});

	$("#addToggle").click(function() {
		
		$(this).toggleClass('on');		
		
		$("#addAgenda").slideToggle('fast');
	});

	$("#dateField").datepicker();

	$("#categoryField").change(function() {
		if($("#categoryField").val() == "1") {
			$(".label-due").animate({
				'width': '30px',
				'opacity': 1
			}, 100);
		} else {
			$(".label-due").animate({
				'width': '0px',
				'opacity': 0
			}, 100);
		}
	});
});