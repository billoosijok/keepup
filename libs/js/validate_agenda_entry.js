$(function() {

	$("#addAgenda").submit(function(event){
		event.preventDefault();

		// Selecting all form controls
		fields = $(this).find(":input");
		
		// Creating and populating an object that
		// contains all names and values of fields
		// as -> {name: value}
		fieldValues = {};
		$.each(fields, function() {
			fieldValues[$(this).attr('name')] = $(this).val();
		});
		
		// validateFields() validates the fields that need to
	 	// be checked. It takes an object of {fieldId: fieldValue}
	 	// and returns true if they all passed the check or false if
	 	// if any of them didn't.
		var valid = validateFields({
						"#titleField": fieldValues.title,
						"#dateField": fieldValues.date,
						"#timeField": fieldValues.time
					});
		
		// IF valid is true it means everything is good :D.
		// so we build sting of "key=value&..." to send off
		// to the php script thru Ajax using upload().
		if (valid) {
			var PHPvalues = "";
			for(item in fieldValues) {
				PHPvalues += item+"="+fieldValues[item]+"&";
			}

			upload(PHPvalues);
		}

	});
});

function upload(values) {	
		$.ajax({
			method: "POST",
			url: '../libs/inc/process_agenda_entry.php',
			data: values
		})
		.always(function(data) {
			console.log(data);
			$("#addAgenda").animate({
				'height' : '120px'
			});
		})
		.done(function() {
			$("#addAgenda").html("<p class='done'><img src='../libs/resources/done.gif?dum="+Math.random()+"'></p>");
		})
		.fail(function(msg, text, error) {
			$("#addAgenda").text("ERROR");
		});
}

function validateFields(obj) {
	var noErrors = true;

	for (item in obj) {
		var fieldId = item;
		var field = $(fieldId);
		var errorDiv = field.siblings(".error");

		if(Array.isArray(obj[item])) {
			var value = obj[item][0];
			var condition = obj[item][1];
		} else {
			var value = obj[item];
			var condition = function() {return true};
		}

		// trim() eleminates white space, and if the string is 
		// false/empty then this executes.
		if(! value.trim() || ! condition()) {
			noErrors = false;
			
			field.addClass('fieldError');
			
			errorDiv.text('Required');
			errorDiv.animate({"opacity": 1.0},130);

			field.focus(function(event) {
				$(this).removeClass('fieldError');
			});
		}

		field.blur(function(event) {
			errorDiv = $(this).siblings(".error");
			if(! $(this).val().trim()) {
				$(this).addClass('fieldError');
				errorDiv.animate({"opacity": 1.0},130);
			} else {
				errorDiv.animate({"opacity": 0},130);
			}
		});
	}

	return noErrors;
}