$(document).ready(function() {
	
	$(".card-wrapper").each(function(index, el) {
		var _this = $(this);
		var elementAfter = _this.next();

		// When done with a card
		$(this).find(".delete-card").submit(function(e){
			e.preventDefault();
			
			var fieldsSent = $(this).find(":input");
			var values = [];

			fieldsSent.each(function(index, el) {
				var name = $(this).attr('name');
				var value = $(this).val();
				values.push(name+"="+value);
			});

			values = values.join('&');

			var undoDiv = $("#undo");
			undoDiv.animate({left: '70px'}, 80);		
			
			var intervl = setInterval(function(){undoDiv.animate({left: '-70px'}, 80);}, 2000);
			
			undoDiv.off().click(function() {
				clearInterval(intervl);
				$.ajax({
				url: '../libs/inc/delete-card.php',
				type: 'POST',
				data: "undelete&"+values,
				})
				.done(function(msg) {
					elementAfter.before(_this);
					_this.slideDown('fast', function() {
						_this.find('.card').toggle('drop', {direction: 'right', easing:'easeOutExpo'});
					});

					undoDiv.animate({left: '-70px'}, 80);
				})
				.fail(function() {
					console.log("error");
				})
				.always(function() {
					console.log("complete");
				});
			});

			$.ajax({
				url: '../libs/inc/delete-card.php',
				type: 'POST',
				data: "delete&"+values,
			})
			.done(function(msg) {
				console.log(msg);
					_this.find('.card').toggle('drop', {direction: 'right', easing:'easeOutExpo'},function(){
					_this.slideUp('fast', function() {
						_this.detach();
					});
				});
			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
			});

		});
	});

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

	$.each($(".agenda"), function() {
		
		$(this).find(".head").click(function(event) {
			
			$(".more:visible").stop().slideUp('fast');
			
			$(this).siblings(".more").stop().slideToggle('fast');
		});
		
		_this = $(this);
		$(this).find('.dismiss').click(function(event) {
			_this.find('.head').click();
			event.stopPropagation();
		});
	});
	
	$("#addToggle").click(function() {
		
		$(this).toggleClass('on');		
		
		$("#addAgenda").slideToggle('fast');
	});

	if($("#dateField").length){$("#dateField").datepicker({minDate:0})};

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


function ajaxCall(values, serverHandler, done) {
	$.ajax({
		url: serverHandler,
		type: 'POST',
		data: values
	})
	.done(function() {
		done();
	})
	.fail(function() {
		msg = 'Oups! Please Try again later';
	})
	.always(function() {
		console.log('complete');
	});
}