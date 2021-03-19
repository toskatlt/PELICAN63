<?php
//include $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$timestamp = date('Y-m-d G:i:s');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   АДМИНЫ   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function AllAdminOnline ($dbcnx) {
	$query = mysql_query("SELECT `id`, SUBSTRING_INDEX(`fio`, ' ', 2) as `fio`,`username`,`phone`,`id_position` FROM domain_user WHERE id_position IN ('1','2','14') and access > 7 ORDER BY fio ASC",$dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function dutyAdmin ($dbcnx) {
	$query = mysql_query("SELECT `id`, SUBSTRING_INDEX(`fio`, ' ', 2) as `fio`,`username`,`phone`,`id_position` FROM domain_user WHERE id_position IN ('1','2','14') and access > 7 and run=1 ORDER BY fio ASC",$dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function nearestDuty ($dbcnx) {
	$query = mysql_query("SELECT domain_user.id, SUBSTRING_INDEX(domain_user.fio, ' ', 2) as `fio`, domain_user.phone, calendar.date, calendar.id_domain_user FROM domain_user, calendar WHERE calendar.id_domain_user=domain_user.id AND calendar.date >= CURDATE() ORDER BY calendar.date ASC LIMIT 2",$dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function admin25top ($dbcnx) {
	$query = mysql_query("SELECT domain_user.id, SUBSTRING_INDEX(domain_user.fio, ' ', 2) as `fio`, domain_user.phone, calendar.date, calendar.id, calendar.id_domain_user FROM domain_user, calendar WHERE calendar.id_domain_user=domain_user.id ORDER BY calendar.date desc limit 25",$dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function duty_admin ($dbcnx) {
	$query = mysql_query("SELECT domain_user.fio, domain_user.phone, calendar.date, calendar.id_domain_user FROM domain_user, calendar WHERE calendar.id_domain_user=domain_user.id AND calendar.date >= CURDATE() ORDER BY calendar.date ASC LIMIT 1", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
} 

// СУММА ОТКРЫТЫХ МАГАЗИНОВ У АДМИНИСТРАТОРА
function countOpenShopUser ($dbcnx, $id) {
	$query = mysql_query("SELECT count(*) FROM opener WHERE id_domain_user = '".$id."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['count(*)'];
}

function allCountOpenShopUser ($dbcnx) {
	$query = mysql_query("SELECT domain_user.fio, count(*) as count FROM opener, domain_user WHERE opener.id_domain_user=domain_user.id and opener.id_domain_user in (SELECT DISTINCT(id_domain_user) FROM `opener` WHERE id_domain_user != 0) GROUP BY opener.id_domain_user ORDER BY `count` DESC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}
