<?php $libs = "../libs";

require "$libs/inc/classes.php";
page::init($dbc);
if($_SERVER['REQUEST_METHOD'] == "POST") {
	require "$libs/inc/db_connect.php";
	include "$libs/inc/add-user.php";
	include "$libs/inc/add-classes.php";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php 
	page::head('Administration Page'); 
	
	if (page::$USER_ROLE != "A") {
		header("Location: index.php");
	}
?>
</head>

<body id="admin">

	<nav>
		<p class="name"><a href="index.php"><?php echo page::$USER_INFO->first_name ?></a></p>
		<div><a href="?logout">Logout</a></li></div>
	</nav>
	
<main id="content">

	<div id="sub-nav">
		<a class="button-link" href='admin.php'>Main</a>
	</div>

	<script>
	$(function(){
		$('#panels').tabs({
			activate: function(event, ui){
				window.location.hash = ui.newTab.context.hash;
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
			<?php if(isset($teacherError)){
					echo "<p style='color: red'>".$teacherError."</p>" ;
				} ?>
					
				</div>
			<p class="intro">You can . . .</p>
			<div class="section">
				<p class="header">Add a teacher manually</p>
			
				<?php addUser('T'); ?>
			</div>

			<p class="intro">OR</p>

			<div class="section">
				<p class="header">import from your database as .CSV</p>
				<form action="<?php echo $_SERVER['SCRIPT_NAME']."#teachers";?>" method='post' enctype="multipart/form-data">
					<input type="hidden" name="role" value="T">
					<div class="field"><input type="file" name="csv"></div>
					<div class="field"><input type="submit" value="upload" name="add-user-db"></div>
				</form>
			</div>
		</div>

		<div class="single-panel" id="students">
		<div class="admin-error">
			<?php if(isset($studentError)){
				echo "<p style='color: red'>".$studentError."</p>" ;
			} ?>
				
			</div>
		<p class="intro">You can . . .</p>
	<div class="section">
	<p class="header">Add a student manually</p>
		<?php addUser('S'); ?>
	</div>

	<p class="intro">OR</p>
		<div class="section">
			<p class="header">import from your database as .CSV</p>
			<form action="<?php echo $_SERVER['SCRIPT_NAME']."#students";?>" method='POST' enctype="multipart/form-data">
				<input type="hidden" name="role" value="S">
				<div class="field">
					<input type="file" name="csv">
				</div>
				<div class="field">
					<input type="submit" value="upload" name="add-user-db">
				</div>
			</form>
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
				
				<?php if(isset($message)){echo "<p class=\"error\">".$message . "</p>" ;} ?>
				<form action="<?php echo $_SERVER['SCRIPT_NAME']."#classes";?>" method='POST' enctype="multipart/form-data">
					<div class="field">
						<label for="csv">File: </label><input id="csv" type="file" name="csv">
					</div>
					<div class="field">
						<input type="submit" value="upload" name="add-class-db">
					</div>
					
				</form>
			</div>
		</div>
		</div>
	</div>
</main>

<?php 
	function addUser($role) {
		if($role == 'T') {
			$tab = "teachers";
		} else {
			$tab = "students";
		}
		?> 
		<form action="<?php echo $_SERVER['SCRIPT_NAME']."#".$tab;?>" method='post'>
			<input type='hidden' name="role" value='<?php echo $role ?>'>

			<div class="row">
				<div class="field half">
					<label for="">ID: </label><input type='text' name='login_id'>
				</div>
			</div>
			
			<div class="row">
				<div class="field">
					<label for="first_name">First Name: </label><input type='text' name='first_name' id="first_name">
				</div>
				<div class="field">
					<label for="last_name">Last Name: </label><input type='text' name='last_name' id="last_name">
				</div>
			</div>

			<div class="row">
				<div class="field">
					<label for="email">Email: </label><input type='text' name='email' id="email">
				</div>
				</div>
				<div class='row'>
				<div class="field">
					<label for="username">Username: </label><input type='text' name='username' id="username">
				</div>
				<div class="field">
					<label for="pin">PIN (this will also be used as a default password) </label><input type='text' name='pin' id="pin">
				</div>
				<div class="field">
					<label for="pin">Classes: (class codes seperated by comma) </label><input type='text' name='class' id="class">
				</div>
			</div>
			<div class="row">
				<div class="field">
					<input type="submit" name="add-user-manually">
					</div>
			</div>
		</form>
		<?php
	}
?>