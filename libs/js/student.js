$(document).ready(function() {
	
	$(".card-wrapper").each(function(index, el) {
		var _this = $(this);

		// Storing the next element to be able to 
		// return it if the user clicks 'undo'
		var elementAfter = _this.next();

		// When done with a card. Because the delete
		// button is a form
		$(this).find(".delete-card").submit(function(e){
			e.preventDefault();
			
			// these will be the id of the agenda and the id 
			// of the user who deleted it.
			var fieldsSent = $(this).find(":input");
			
			// building and array of "key=value"
			var values = [];
			fieldsSent.each(function() {
				var name = $(this).attr('name');
				var value = $(this).val();
				values.push(name+"="+value);
			});
			// to make it php-friendly 🙃
			values = values.join('&');

			// Getting a handle of the 'undo' div and flying it in.
			// also, flying it out after 5 secs.
			var undoDiv = $("#undo");
			undoDiv.animate({left: '70px'}, 80);		
			var intervl = setInterval(function(){undoDiv.animate({left: '-70px'}, 80);}, 5000);
			
			// When undo div is clicked ...
			undoDiv.off().click(function() {
				// Clearing to avoid overlapping
				clearInterval(intervl); 

				// Defining a done function to call when the 
				// ajax request (of undeleting a card) is done
				var done = function() {
					// using the elementAfter (defined earlier)
					// to put the card back where it was (before it)
					elementAfter.before(_this);
					_this.slideDown('fast', function() {
						_this.find('.card').toggle('drop', {direction: 'right', easing:'easeOutExpo'});
					});
					// Dismissing the undo div
					undoDiv.animate({left: '-70px'}, 80);
				}
				// Adding 'undelete' to the vars so that the
				// php script knows what the request is.
				ajaxCall("undelete&"+values, done);

			});

			// Defining a done function to call when the 
			// ajax request (of deleting a card) is done
			var done = function() {
				_this.find('.card').toggle('drop', {direction: 'right', easing:'easeOutExpo'},function(){
					_this.slideUp('fast', function() {
						_this.detach();
					});
				});
			}
			// Adding 'delete' to the vars so that the
			// php script knows what the request is.
			ajaxCall("delete&"+values, done);
		
		});
	});

	// This would be the button up top to slide up the ones
	// that don't belong to this class/ class name
	$("nav .class_name").click(function(e){
		e.preventDefault();

		// This is to apply 'active' state the active tab.
		$(".active").removeClass('active');
		$(this).addClass('active');

		// Using the class code to slideUp the elements
		// with any class code other than this
		var classCode = $(this).text().toLowerCase();
		
		// Going thru all of them to slideUp/slideDown the elements
		// as needed.
		$('.card-wrapper').each(function() {
			if($(this).hasClass(classCode) || classCode === "all") {
				$(this).slideDown('fast');
			} else {
				$(this).slideUp('fast');
			}
		});
	});
});


function ajaxCall(values, done) {
	$.ajax({
		url: '../libs/inc/delete-card.php',
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
		console.log(msg);
	});
}