<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_object.php");


if (isset($_GET['id_domain_user'])) {
	$iddu = $_GET['id_domain_user'];
}
if (isset($_POST['id_domain_user'])) {
	$iddu = $_POST['id_domain_user'];
}


if (isset($_POST['text_fio'])) {
	mysql_query("UPDATE `domain_user` SET `fio`='".$_POST['text_fio']."' WHERE `id`='".$iddu."'", $dbcnx);	
}

if (isset($_POST['phone'])) {
	mysql_query("UPDATE `domain_user` SET `phone`='".$_POST['phone']."' WHERE `id`='".$iddu."'", $dbcnx);	
}

if (isset($_POST['email'])) {
	$emailInDomainUser = emailInDomainUser ($dbcnx, $iddu);
	if (empty($emailInDomainUser)) {
		mysql_query("INSERT INTO `email`(`id_domain_user`, `email`) VALUES ('".$iddu."','".$_POST['email']."')", $dbcnx);
	} else {	
		mysql_query("UPDATE `email` SET `email`='".$_POST['email']."' WHERE `id_domain_user`='".$iddu."'", $dbcnx);	
	}
}

if (isset($_POST['jabber'])) {
	mysql_query("INSERT INTO `jabber`(`id_domain_user`, `jabber`) VALUES ('".$iddu."', '".$_POST['jabber']."') ON DUPLICATE KEY UPDATE `jabber`='".$_POST['jabber']."'", $dbcnx);	
}