$(document).ready(function(){
	$('form').submit(function(evt) {
		var statusElement = $('#status');
		statusElement.html('&nbsp;').slideDown(50).css('background','url(resources/loading.gif) no-repeat center top');
		
		evt.preventDefault();

		
		var loginId = $("#loginIdField").val();
		var password = $("#passwordField").val();
		
		if (loginId && password) {
			var values = "login_id="+loginId+"&password="+password;

			$.ajax({
				type: "POST",
				url: "inc/process_login.php",
				data: values,
				success: function(msg){
			  		statusElement.slideUp(50);
			        window.location = "home.php";
			  	},
			  	error: function() {
			  		error('Incorrect ID or Password!', statusElement);
			  	}
			});
		} else {
			error('Please fill out both fields', statusElement);
		}
		
	});
});	

function error(msg, target) {
	target.css('background','none');
	target.text(msg);
}