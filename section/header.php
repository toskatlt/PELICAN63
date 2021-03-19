<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> 
		<?php
			include $_SERVER["DOCUMENT_ROOT"]."/config.php";
			require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_object.php");
			require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_terminals.php");
			require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_jabber.php");
			require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_admin.php");

			if (isset($_GET['id'])) {	
				$selectObject = selectObject ($dbcnx, $_GET['id']);
				echo $selectObject['name'];
			} elseif (isset($_GET['email'])) {
				$objectFromEmail = objectFromEmail($dbcnx, $_GET['email']);
				echo $objectFromEmail['name'];
			} else {
				echo "Пеликан";
			}
			
			$date_time = date("y-m-d H:i:s");
			if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) { 
				$userdata = authorization_lite ($dbcnx, $_COOKIE['id']);
			} else {
				$userdata['access'] = 0;
			}
			$randval = rand();
		?>	
	</title>
	<link rel='stylesheet' href='/css/table.css?ver=<?=$randval?>'/>
	<link rel='stylesheet' href='/css/style.css?ver=<?=$randval?>'/>
	<link rel='stylesheet' href='/highslide/highslide.css'/>
	<link rel='stylesheet' href='/css/input.css?ver=<?=$randval?>'/>
	
	<script src='/highslide/highslide.js'></script>
	<script src='/jquery/jquery-3.1.0.min.js'></script>
	<script src='/jquery/jquery.metadata.js'></script>
	<script src='/jquery/jquery.tablesorter.js'></script>
	<script src='/js/js.js?ver=<?=$randval?>'></script>
	<script src='/js/spin/spin.js'></script>
	<script src='/js/spin/hideshow.j'></script>
</head>
<body class='gradient'>
	<header class='header_block container header-flex'>
		<div class='logo'><a href='/'><img src='/img/logo.png' class='logo'></a></div>
		<div class='login_online'>
			<?php if ($userdata['access'] > 0) { ?>
				<form method='post' name='exit_cookies'>
					<p>
					<?php 
						if ($userdata['access'] > 1) {
							echo "<a href='personal'>".$userdata['fio']."</a>";
						} else {
							echo $userdata['fio'];
						}
					?>
					<a class="exit" onclick="clearCookie()">X</a></p>
				</form>
			<?php } ?>
		</div>	
	</header>
	
<?php

//var_dump($userdata);

?>
	