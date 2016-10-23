/*
TABLE REQUIREMENT:
				   - table rows within <tbody>
				   _

SEARCH REQUIREMENT: an input field.

EDIT REQUIREMENT: editable fields should be present
		for each row as hidden inputs.
*/

function SmartTable(tableId) {

	/* Properties */
	this.table = $(tableId);
	this.rows = this.table.find('tbody tr');
	this.tableNameInDB = this.table.find("input[name='table']").val();


	/* Methods */
	this.activateSearch = function(searchBoxId) {
		/* Activstes search on the table */
		
		// This will make it easy to reference the 
		// root 'this' and prevent conflicts.
		var _this = this;

		// Calling the search() methos whenever the is a keyup
		// in the provided search field.
		$(searchBoxId).keyup(function(event) {
			var query = $(this).val();
			_this.search(query);
		});
	}

	this.activateEdit = function(classOfHiddenInputs) {
		/* Activstes edit on the table

		REQUIREMENT	: editable fields should be present
		for each row as hidden inputs

		*/

		var _this = this;

		// Binding a click event for each row, which
		// calls the edit() method.
		_this.rows.click(function(event) {
			_this.edit($(this), classOfHiddenInputs);
		});
	}

	this.search = function(query) {
		var _this = this;

		// if triming the white space doesn't cause an empty
		// string then there is something in the query.
		if(query.trim()) {
			// For each row ...
			$.each(_this.rows, function() {
				// Getting all of its cells
				var cells = $(this).find('td');
				var matches = 0;

				// For each cell ...
				$.each(cells, function() {
					var cell = $(this);
					// 👇 The data in the cell
					var cellData =  cell.text();

					// 👇 creating RgularExpression object
					// with the pattern being the query
					// and the flag 'i' for care-insesitivity.
					var queryAsRegEx = new RegExp(query, 'i');
					
					// text() returns true if the pattern
					// has any matches with a string or false.
					if(queryAsRegEx.test(cellData)) {
						// if yes then the html in the cell is 
						// replaced with itself plus <mark> around
						// the matching chars.
						cell.html(cellData.replace(queryAsRegEx, function(str){return "<mark>" + str + "</mark>";}));
						// cuz we got a match.✌️
						matches++;
					} else {
						// Otherwise we do this because if the
						// query no longet matched a cell then
						// the <mark> will be gone.
						cell.html(cellData);
					}
				}); 

				if(matches) {
					// act on the matching rows
					$(this).fadeIn(150);
				} else {
					// act on the non-matching rows
					$(this).fadeOut(150);
				}
			});
		} else {
			// This is needed to reset the table when the 
			// query is empty. Especially when emptying it 
			// after some seacrch.
			$.each(_this.rows, function() {
				$(this).fadeIn(300);
				$(this).find('td').each(function (){$(this).html($(this).text())});
			});
		}
	}

	this.edit = function(row, classOfHiddenInputs) {
		var _this = this;

		// Initializing a div that will act as a popup to edit.
		var editBox = $("<div id='editBox'><div class='head'><span class='title'>Edit</span><span id='close'>x</span></div></div>");
		
		// this will be the selector of the hidden inputs.
		// if a class argument was passed then we select 
		// them with that class, otherwise we just select 
		// all the hidden inputs.
		var fieldsSelector = (classOfHiddenInputs) ? 'input.'+classOfHiddenInputs : "input[type='hidden']";
		
		// Using the selector to retrieve the fields.
		var FieldsToEdit = row.find(fieldsSelector);
		
		// init form
		var editForm = $("<form>");
		
		// init fieldset for better styling
		var editableFieldset = $('<fieldset>');

		// going thru each field in the set of hidden inputs.
		$.each(FieldsToEdit, function() {
			var label = $(this).prop('name');
			var prettyLabel = capitalize(label.replace('_',' '));
			var value = $(this).prop('value');

			// this serves better styling.
			fieldInForm = $("<div class='field'>");
			
			if (label === "id") {
				editForm.append($(this));
			} else {
				// if it was empty then the place holder will be none.
				var placeholder = (value) ? value : 'None';
				// Building the <label> and <input text>
				var input = $('<label>' + prettyLabel + '<input type="text" name="'+label+'" placeholder="'+placeholder+'" value="'+value+'"">'+'</label>');

				// setting a change and keyup event to unlock
				// the update button whenever something new happens
				// to the fields
				input.find('input').off().on('change keyup',function(event) {
					$("#updateRecord").removeAttr('disabled');
				});
				// Appdending to the field element, then to the fieldset.
				fieldInForm.append(input);
				fieldInForm.appendTo(editableFieldset);
			}
			
		});
		
		// init three buttons, which will be bound to events
		updateButton = $("<input class='button inFieldset' type='submit' id='updateRecord' value='Update' disabled />");
		resetButton = $("<input class='button inFieldset' type='reset' id='resetForm' value='Reset' />")
		deleteButton = $("<input class='button' type='submit' id='deleteRecord' value='Delete' />");
		
		
		updateButton.click(function(){
			// These vars will be used to communicate with the
			// server
			formTarget = "../libs/inc/update_record.php";
			formAction = "update";
		});
		deleteButton.click(function(){
			formTarget = "../libs/inc/delete_record.php";
			formAction = "delete";
			
			// returning false will stop the event and true
			// will let it happen.
			return confirm("You sure tho?");
		});
		
		// Appending them.
		updateButton.appendTo(editableFieldset);
		resetButton.appendTo(editableFieldset);
		deleteButton.appendTo(editForm);	

		// Putting the fieldset up.
		editableFieldset.prependTo(editForm);
		// Putting the whole form up.
		editForm.appendTo(editBox);

		// 
		var overlay = new Popup(editBox, 'overlay');
		overlay.beforeAppearance = function() {
			// Moving the clicked row to the edit field (at least 
			// that's what it looks like).
			row.stop().effect('transfer',{to: overlay.popup}, 150);
		}
		overlay.beforeDisappearance = function() {
			// Moving the row back to the edit field (at least 
			// that's what it looks like).
			editBox.stop().effect('transfer',{to: row}, 150);
		}
		overlay.show();

		editBox.find("#close").click(function() {
			// which means clicking close will do whatever
			// dissmissing the the overlay does.
			overlay.dismiss();
		});

		// On submittion (no matter what submitted it, the delete or update).
		editForm.submit(function(e) {
			e.preventDefault();
			
			values = [];
			// For each input
			$.each($(this).find(':input'), function(index, el) {
				var name = $(this).prop('name');
				var value = $(this).prop('value');

				// Adding the "name=value" to the array.
				values.push(name +"="+ value);
			});

			// Because the name of the table needs to be passed
			values.push("table="+_this.tableNameInDB); 
			
			switch (formAction)  {
				// When the action is update we direct the 
				// script to a different formTarget.
				case 'update':
					_this.updateRecord(row, values, formTarget);
					break;
				// and a different one when 'delete'
				case 'delete':
					_this.deleteRecord(row, values,formTarget, function(){
						row.fadeOut(400);
						overlay.dismiss();
					});
					break;
			}
		});

	}

	this.updateRecord = function(row, newValues, serverHandler, done) {
		// init a default function if done is not passed.
		// Because what we basically wanna do is update the
		// updated row with new values.
		var done = done || function() {
			// Stripping out the vlues from the newValues's object
			values = Object.keys(newValues).map(function(key){
				return newValues[key];
			});

			// replacing the content/text() of the cells 
			// with the updated version.
			var tableCells = row.find('td');
			$.each(tableCells, function(index){
				value = values[index].slice(values[index].indexOf("=") + 1);
				$(this).text(value);
			});

			// Also, updating the hidden inputs with the new 
			// values. (That's if they click back again on
			// the same row to edit it)
			$.each(row.find('input[type=hidden]'), function(index){
				value = values[index].slice(values[index].indexOf("=") + 1);
				$(this).val(value);
			});
		}
		// defined below
		ajaxCall(newValues.join('&'), serverHandler, done);
	}

	this.deleteRecord = function(row, dataToDelete, serverHandler, done) {
		// using a fallback function done() is param done was 
		// not defined.
		var done = done || function() {
			alert("DELETED");
		}
		// defined below
		ajaxCall(values.join('&'), serverHandler, done);
	}
}

function Popup(popup, className) {
	/* This class creates the overlaying div with any
	div on top of it */

	this.className = className || "overlayObj";
	this.overlayDiv = $("<div class='"+this.className+"'>");
	this.popup = popup;
	
	// Setting the display to block but not showing it in case 
	// there is anythings that needs to be done before displaying
	this.overlayDiv.css({opacity: 0});
	
	// If className is not defined, we give it 
	// default inline css.
	if (!className) {
		this.overlayDiv.css({
			display: 'flex',
			width: '100vw',    height: '100vh',
			position: 'fixed', top: 0,
			background: 'rgba(0,0,0,0.3)',
			'z-index': 100
		});
	}

	this.show = function() {
		var _this = this;

		popup.click(function(e){e.stopPropagation()});

		// Adding the popup div to the overlaying div.
		popup.appendTo(_this.overlayDiv);

		// Adding the overlay to DOM, but note that it's still
		// hidden. aka. has opacity of 0.
		_this.overlayDiv.appendTo('body');

		// This is an empty function that can be overwritten 
		// if any code needs to be excuted before the popup appears.
		_this.beforeAppearance();

		// Making it appear
		_this.overlayDiv.animate({opacity: '1'}, 200);

		// Binding a closing event by clicking the overlay div. aka
		// outside the popup.
		_this.overlayDiv.off('click').click(function(event) {
			_this.dismiss();
		});
	}

	this.dismiss = function() {
		this.beforeDisappearance();
		this.overlayDiv.stop().fadeOut(200).remove();
	}

	// Optional functions
	this.beforeAppearance = function() {}
	this.beforeDisappearance = function() {}
}

function capitalize(str) {
	/* Function to capitalize the first letters
	 of each word in the string*/
	var words = str.split(" ");
	for (var i = 0; i < words.length; i++) {
		words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
	}
	return words.join(' ');
}

function ajaxCall(values, serverHandler, done) {
	// It takes "key=value", "phpScript", and function done()
	// to tell it waht to do when done/successful.
	$.ajax({
		url: serverHandler,
		type: 'POST',
		data: values
	})
	.done(function(mesg) {
		console.log(mesg);
		msg = ' 👍';
		done();
	})
	.fail(function() {
		msg = 'Oups! Please Try again later';
	})
	.always(function() {
		msg = $("<div style='display: inline-block; padding: 0 5px;'>" + msg + "</div>");
		$('#updateRecord').after(msg);
		msg.effect('slide');
	});
}