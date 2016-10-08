$(function() {
	$("nav .class_name").click(function(e){
		e.preventDefault();
		$(".active").removeClass('active');
		$(this).addClass('active');
		var classCode = $(this).text().toLowerCase();
		$('.card-wrapper').each(function() {

			if($(this).hasClass(classCode) || classCode === "all") {
				$(this).slideDown('fast');
			} else {
				$(this).slideUp('fast');
			}
		});
	});
});