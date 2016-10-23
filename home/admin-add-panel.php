<?php $libs = "../libs";
require_once "$libs/inc/classes.php";

// This initializes the dbc to the page along with 
// SESSION and other essential stuff. 
// class Page{} is defined in ~/libs/classes.php.
page::init($dbc);

// Including files only if the request method was post
// which means a form was submitted.
if($_SERVER['REQUEST_METHOD'] == "POST") {
	include_once "$libs/inc/add-user.php";
	include_once "$libs/inc/add-classes.php";
}

// Like any page, kicking out any non-admin.
if (page::$USER_ROLE != "A") {
	header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php 
	// <head> of the page.
	page::head('Add Records'); 
	?>
</head>

<body id="admin">

	<nav>
		<p class="name"><a href="index.php"><?php echo page::$USER_INFO->first_name ?></a></p>
		<div><a href="?logout">Logout</a></div>
	</nav>
	
<main id="content">

	<div id="sub-nav">
		<a class="button-link" href='admin.php'>Main</a>
	</div>

	<script>
	$(function(){
		// Making #panles into tabs
		$('#panels').tabs({
			activate: function(event, ui){
				// This function fires whenever a tab is clicked
				// the problem is that the page jumps down to the
				// div of that hash.

				// updating the url with the new hash.
				window.location.hash = ui.newTab.context.hash;

				// Then I scroll back up top, which looks like nothing
				// happened.
				window.scrollTo(0,0);
				
			}
		});
	});
	</script>

	<div class="message"><?php if(isset($success)){echo $success;} ?></div>
	
	<div id="panels">
		<ul>
			<li class="tabs">
				<a class="button-link" href="#teachers">Add Teachers</a>
			</li>
			<li class="tabs">
				<a class="button-link" href="#students">Add Students</a>
			</li>
			<li class="tabs">
				<a class="button-link" href="#classes">Add Classes</a>
			</li>
		</ul>
		
		<div class="single-panel" id="teachers">

			<div class="admin-error">
				<?php 
				if(isset($teacherError)){
					echo "<p style='color: red'>".$teacherError."</p>";
				}
				?>
				
			</div>

			<p class="intro"> You can . . . </p>
			
			<div class="section">
				<p class="header">Add a teacher manually</p>
			
				<?php addUser('T'); ?>
			</div>

			<p class="intro">OR</p>

			<div class="section">
				<p class="header">import from your database as .CSV</p>
				<form action="<?php echo $_SERVER['SCRIPT_NAME']."#teachers";?>" method='POST' enctype="multipart/form-data">
					<input type="hidden" name="role" value="T">
					<div class="field">
						<input type="file" name="csv">
					</div>
					<div class="field">
						<input type="submit" value="upload" name="add-user-db">
					</div>
				</form>
				<h2> Expected Columns: </h2>
				<div><img src="../libs/resources/teacher-table.png"></div>
			</div>
		</div>

		<div class="single-panel" id="students">
			<div class="admin-error">
				<?php if(isset($studentError)){
					echo "<p style='color:red'>".$studentError."</p>" ;
				} ?>
			</div>
			<p class="intro">You can . . .</p>
			<div class="section">
				<p class="header">Add a student manually</p>
					<?php addUser('S'); ?>
			</div>

			<p class="intro"> OR </p>
			<div class="section">
				<p class="header">Import from your database as .CSV</p>
				<form action="<?php echo $_SERVER['SCRIPT_NAME']."#students";?>" method='POST' enctype="multipart/form-data">
					<input type="hidden" name="role" value="S">
					<div class="field">
						<input type="file" name="csv">
					</div>
					<div class="field">
						<input type="submit" value="upload" name="add-user-db">
					</div>
				</form>
				<h2> Expected Columns: </h2>
				<div><img src="../libs/resources/students-table.png"></div>
			</div>
		</div>

		<div class="single-panel" id="classes">
			<div class="admin-error">
				<?php if(isset($classError)) {
					echo "<p style='color: red'>".$classError."</p>" ;
				} ?>	
			</div>
			<p class="intro">You can . . .</p>
			<div class="section">
				<p class="header">Add a class manually</p>
				<form action="<?php echo $_SERVER['SCRIPT_NAME']."#classes"; ?>" method="POST">
		
					<div class="row">
						<div class="field half">
							<label>Class Name <input type='text' name='class_name'></label>
						</div>
					</div>
					
					<div class="row">
						<div class="field half">
							<label>Class Code <input type='text' name='class_code'></label>
						</div>
					</div>
					
					<div class="field">
						<input type="submit" name="add-class-manually">
					</div>
				</form>
			</div>
			<p class="intro">OR</p>
			<div class="section">
				<p class="header">import from your database as .CSV</p>
				
				<form action="<?php echo $_SERVER['SCRIPT_NAME']."#classes";?>" method='POST' enctype="multipart/form-data">
					<div class="field">
						<label for="csv">File: </label>
						<input id="csv" type="file" name="csv">
					</div>
					<div class="field">
						<input type="submit" value="upload" name="add-class-db">
					</div>
				</form>

				<h2> Expected Columns: </h2>

				<div><img src="../libs/resources/classes-table.png"></div>
			</div>
		</div>
	</div>
</main>
</body></html>
<?php 
function addUser($role) {
	if($role == 'T') {$tab = "teachers";} 
	else {$tab = "students";} 
?> 
	<form action="<?php echo $_SERVER['SCRIPT_NAME']."#".$tab;?>" method='post'>
		<input type='hidden' name="role" value='<?php echo $role?>'>

		<div class="row">
			<div class="field half">
				<label for="<?php echo "$role-login_id"?>">ID: </label>
				<input type='text' name='login_id' id="<?php echo "$role-login_id";?>">
			</div>
		</div>
		
		<div class="row">
			<div class="field">
				<label for="<?php echo "$role-first_name"?>">First Name: </label>
				<input type='text' name='first_name' id="<?php echo "$role-first_name";?>">
			</div>
			<div class="field">
				<label for="<?php echo "$role-last_name"?>">Last Name: </label>
				<input type='text' name='last_name' id="<?php echo "$role-last_name";?>">
			</div>
		</div>

		<div class="row">
			<div class="field">
				<label for="<?php echo "$role-email"?>">Email: </label>
				<input type='text' name='email' id="<?php echo "$role-email";?>">
			</div>
		</div>

		<div class='row'>
			<div class="field">
				<label for="<?php echo "$role-username"?>">Username: </label>
				<input type='text' name='username' id="<?php echo "$role-username";?>">
			</div>
			<div class="field">
				<label for="<?php echo "$role-pin"?>">PIN (this will also be used as a default password) </label>
				<input type='text' name='pin' id="<?php echo "$role-pin";?>">
			</div>
			<div class="field">
				<label for="<?php echo "$role-classesField"?>">Classes: (classes codes seperated by comma) </label>
				<input type='text' name='classes' id="<?php echo "$role-classesField";?>">
			</div>
		</div>

		<div class="row">
			<div class="field">
				<input type="submit" name="add-user-manually">
			</div>
		</div>
	</form>
<?php

} ?>
