<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";
include $_SERVER["DOCUMENT_ROOT"]."/function/function_object.php";

$date_time = date("y-m-d H:i:s");

if (isset($_POST['inputMail'])) {
	$selectUser = selectUser ($dbcnx_pf, $_POST['inputMail']);
	if (!isset($selectUser)) {
		echo 'false';
	} else {
		echo 'true';
	}
}

if (isset($_POST['addMailbox'])) {
	$id_object = $_POST['id_object'];
	$selectObject = selectObject ($dbcnx, $id_object);
	
	if ($_POST['name'] != '') {
		$name = $_POST['name'];	
	} else {
		$name = $selectObject['name'];
	}
	
	if ($_POST['type'] == '0') {
		$_POST['addMailbox'] = mb_strtolower($_POST['addMailbox']);
		
		preg_match("/(.*?)@neo/sui", $_POST['addMailbox'], $user);
		$maildir = "neo63.ru/".$user[1]."/";
		$pass = md5($user[1]."3000");
		mysql_query("INSERT INTO `mailbox`(`username`, `password`, `maildir`, `name`, `quota`, `local_part`, `domain`, `created`, `modified`, `active`) VALUES ('".mysql_real_escape_string($_POST['addMailbox'])."','".$pass."', '".$maildir."', '".$name."','0', '".$user[1]."', 'neo63.ru', '".$date_time."', '".$date_time."', '1')", $dbcnx_pf);
	}ct_rules
	$update_log = "+ Эл.адрес: ".$_POST['addMailbox']." ";
	mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('0','".$date_time."','email','".$id_object."','".$update_log."')", $dbcnx);
	mysql_query("INSERT INTO `email`(`id_object`, `email`) VALUES ('".$id_object."', '".mysql_real_escape_string($_POST['addMailbox'])."')", $dbcnx);
	echo "INSERT INTO `email`(`id_object`, `email`) VALUES ('".$id_object."', '".mysql_real_escape_string($_POST['addMailbox'])."')";
}