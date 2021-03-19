<?php
header('Content-type: text/html; charset=utf-8');
include "/var/www/html/www/config.php";
require_once("../function/function_object.php");

// ПЕРЕСТРОЕНИЕ ИНДЕКСОВ ПО ПОРЯДКУ В ТАБЛИЦЕ work_domain_user | 28.11.2018

	function newIndex ($dbcnx) {
		$query = mysql_query("SELECT * FROM `work_domain_user` ORDER BY `id` ASC", $dbcnx);
		$n = mysql_num_rows($query);
		for ($i = 0; $i < $n; $i++) {
			$result[] = mysql_fetch_assoc($query);
		}	
		return $result;
	}
	
$newIndex = newIndex ($dbcnx);	

mysql_query("TRUNCATE TABLE `work_domain_user`", $dbcnx);
mysql_query("ALTER TABLE `work_domain_user` auto_increment = 1", $dbcnx);


$i=1;
foreach ($newIndex as $nI) {
	mysql_query("INSERT INTO `work_domain_user`(`id`, `username`, `fio`, `phone`, `password`, `hash`, `access`, `id_position`, `id_object`, `run`) VALUES ('".$i++."','".$nI['username']."','".$nI['fio']."','".$nI['phone']."','".$nI['password']."','".$nI['hash']."','".$nI['access']."','".$nI['id_position']."','".$nI['id_object']."','".$nI['run']."')", $dbcnx);
}