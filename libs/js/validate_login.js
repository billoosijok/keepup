
$(document).ready(function(){

	$('form').submit(function(evt) {
		var statusElement = $('#status');
		
		evt.preventDefault();

		var  loginId = $("#loginIdField").val();
		var password = $("#passwordField").val();
		
		if (loginId && password) {
			var values = "ajax&login_id="+loginId+"&password="+password;

			$.ajax({
				type: "POST",
				url: "../libs/inc/process_login.php",
				data: values,
				success: function(msg){
			  		statusElement.slideUp(50);
			  		// dismissPage('top', msg, 1000);
			  		// viewPage(msg);
			  		Transition.toPage(msg,50000);
			  	},
			  	error: function() {
			  		error('Incorrect ID or Password', statusElement);
			  	}
			});
		} else {
			error('Please fill out both fields', statusElement);
		}
		
	});
});	