$(function() {

	// This is to slide down info about the agenda when the title
	// is clicked
	$.each($(".agenda"), function() {
		$(this).find(".head").click(function(event) {
			
			$(".more:visible").stop().slideUp('fast');
			
			$(this).siblings(".more").stop().slideToggle('fast');
		});
	});
	
	// This is to slide down the addForm when the add button s clicked
	$("#addToggle").click(function() {
		$(this).toggleClass('on');		
		$("#addAgenda").slideToggle('fast');
	});

	// This is to show a jquery datepicker calendar when 
	// the date input is clicked
	if($("#dateField").length){$("#dateField").datepicker({minDate:0})};

	// This is to change the 'due date' label to 'date'
	// when 'Quiz' category is clicked. And vise versa.
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