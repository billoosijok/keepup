
function SmartTable(tableId) {
	this.table = $(tableId);
	this.rows = this.table.find('tbody tr');
	this.tableNameInDB = this.table.find("input[name='table']").val();

	this.activateSearch = function(searchBoxId) {
		var _this = this;
		$(searchBoxId).keyup(function(event) {
			var query = $(this).val();
			_this.search(query);
		});
	}

	this.activateEdit = function(classOfHiddenInputs) {
		var _this = this;

		_this.rows.click(function(event) {
			_this.edit($(this), classOfHiddenInputs);
		});
	}

	this.search = function(query) {
		var _this = this;

		if(query.trim() !== '') {
			$.each(_this.rows, function() {
				var cells = $(this).find('td');
				var matches = 0;

				$.each(cells, function() {
					
					var cell = $(this);
					var cellData =  cell.text();
					var queryAsRegEx = new RegExp("\^"+query, 'i');
					
					if(queryAsRegEx.test(cellData)) {
						cell.html(cellData.replace(queryAsRegEx, function(str){return "<mark>" + str + "</mark>";}));
						matches++;
					} else {
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
			$.each(_this.rows, function() {
				$(this).fadeIn(300);
				$(this).find('td').each(function (){$(this).html($(this).text())});
			});
		}
	}

	this.edit = function(row, classOfHiddenInputs) {
		var that = this;
		var editBox = $("<div id='editBox'><div class='head'><span class='title'>Edit</span><span id='close'>x</span></div></div>");
		
		var fieldsSelector = (classOfHiddenInputs) ? 'input.'+classOfHiddenInputs : 'input';
		var FieldsToEdit = row.find(fieldsSelector);
		
		// Setting up the edit box and populating it with a bunch of 
		// editable fields.
		editForm = $("<form>");

		editableFieldset = $('<fieldset>');

		$.each(FieldsToEdit, function() {
			label = $(this).prop('name');
			prettyLabel = capitalize(label.replace('_',' '));
			value = $(this).prop('value');

			fieldInForm = $("<div class='field'>");
			
			if (label === "id") {
				fieldInForm.append($(this));
				fieldInForm.appendTo(editForm);
			} else {
				placeholder = (value) ? value : 'None';
				input = $('<label>' + prettyLabel + '<input type="text" name="'+label+'" placeholder="'+placeholder+'" value="'+value+'"">'+'</label>');
				input.find('input').off().on('change keyup',function(event) {
					$("#updateRecord").removeAttr('disabled');
				});
				fieldInForm.append(input);
				fieldInForm.appendTo(editableFieldset);
			}
			
		});
		
		editableFieldset.prependTo(editForm);

		updateButton = $("<input class='button inFieldset' type='submit' id='updateRecord' value='Update' />");
		resetButton = $("<input class='button inFieldset' type='reset' id='resetForm' value='Reset' />")
		deleteButton = $("<input class='button' type='submit' id='deleteRecord' value='Delete' />");
		
		updateButton.attr('disabled', 'disabled');
		
		resetButton.click(function(){
			editForm[0].reset();
		});
		updateButton.click(function(){
			formTarget = "../libs/inc/update_record.php";
			formAction = "update";
		});
		deleteButton.click(function(){
			formTarget = "../libs/inc/delete_record.php";
			formAction = "delete";
			return confirm("You sure tho?");
		});
		
		updateButton.appendTo(editableFieldset);
		resetButton.appendTo(editableFieldset);
		deleteButton.appendTo(editForm);	

		editForm.appendTo(editBox);

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
			overlay.overlayDiv.click();
		});

		editForm.submit(function(e) {
			e.preventDefault();
			values = [];
			
			$.each($(this).find(':input'), function(index, el) {
				var name = $(this).prop('name');
				var value = $(this).prop('value');
				values.push(name +"="+ value);
			});

			// Because the name of the table needs to be passed
			values.push("table="+that.tableNameInDB); 
			
			switch (formAction)  {
				case 'update':
					that.updateRecord(row, values, formTarget);
					break;
				case 'delete':
					that.deleteRecord(row, values,formTarget, function(){
						row.fadeOut(100);
						overlay.dismiss();
					});
					break;
			}
		});

	}

	this.updateRecord = function(row, newValues, serverHandler, done) {
		var done = done || function() {
			values = Object.keys(newValues).map(function(key){
				return newValues[key];
			});

			var tableCells = row.find('td');

			$.each(tableCells, function(index){
				value = values[index].slice(values[index].indexOf("=") + 1);
				$(this).text(value);
			});

			$.each(row.find('input[type=hidden]'), function(index){
				value = values[index].slice(values[index].indexOf("=") + 1);
				$(this).val(values[index]);
			});
		}

		ajaxCall(newValues.join('&'), serverHandler, done);
	}

	this.deleteRecord = function(row, dataToDelete, serverHandler, done) {
		var done = done || function() {
			alert("DELETED");
		}

		ajaxCall(values.join('&'), serverHandler, done);
	}
}

function Popup(popup, className) {
	this.className = className || "overlayObj";
	this.overlayDiv = $("<div class='"+this.className+"'>");
	this.popup = popup;
	
	// Setting the display to block but not showing it in case 
	// there is anythings that needs to be done before displaying
	this.overlayDiv.css({opacity: 0});
	
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
		var that = this;

		popup.click(function(e){e.stopPropagation()});

		// Adding the popup div to the overlaying div.
		popup.appendTo(that.overlayDiv);

		// Adding the overlay to DOM, but note that it's still
		// hidden. aka. has opacity of 0.
		that.overlayDiv.appendTo('body');

		// This is an empty function that can be overwritten 
		// if any code needs to be excuted before the popup appears.
		that.beforeAppearance();

		// Making it appear
		that.overlayDiv.animate({opacity: '1'}, 200);

		// Binding a closing event by clicking the overlay div. aka
		// outside the popup.
		that.overlayDiv.off('click').click(function(event) {
			that.beforeDisappearance();
			that.dismiss();
		});
	}

	this.dismiss = function() {
		this.overlayDiv.stop().fadeOut(200).remove();
	}

	// Optional functions
	this.beforeAppearance = function() {}
	this.beforeDisappearance = function() {}
}

function capitalize(str) {
	var words = str.split(" ");
	for (var i = 0; i < words.length; i++) {
		words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
	}
	return words.join(' ');
}

function ajaxCall(values, serverHandler, done) {
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