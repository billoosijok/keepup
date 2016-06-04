<?php 

	$result = $dbc->query("SELECT * FROM `users` WHERE user_id='$USER_ID' LIMIT 1");

	$user_info = $result->fetch();

	?>
	<nav>
		<div id="menu-toggle">
			<p><?php echo $user_info->first_name ?></p>
			<ul id="menu">
				<li><a href="?logout">Logout</a></li>
			</ul>
		</div>
	</nav>