<?php
//include $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$date_time = date("Y-m-d H:i:s");
$timestamp = date('Y-m-d G:i:s');

function functionCount ($query, $dbconnect) {
	$query = mysql_query($query, $dbconnect);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}	


// ВЫВОД ВСЕХ РАБОЧИХ СТАНЦИЙ С ТИПОМ=2 ИЗ ТАБЛИЦЫ ws
function terminal_all ($dbcnx) {
	$result = mysql_query("SELECT * FROM `terminal` ORDER BY REPLACE(ip, '.', '') ASC",$dbcnx);
	$n = mysql_num_rows($result);
	$return = array();	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$return[] = $row;
	}	
	return $return;
}

function session_online ($dbcnx) {
	$result = mysql_query("SELECT * FROM user_list",$dbcnx);
	$n = mysql_num_rows($result);
	$return = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$return[] = $row;
	}
	return $return;
}

function session_online_all ($dbcnx) {
	$query = "SELECT COUNT(*) FROM user_list WHERE run='1'";
	$result = functionCount ($query, $dbcnx);
	return $result;
}

function domain_users_terminal_online ($ip_term, $dbcnx) {
	$query = "SELECT COUNT(*) FROM user_list WHERE ip='".$ip_term."' and run='1'";
	$result = functionCount ($query, $dbcnx);
	return $result;
}

function domain_users_terminal ($ip_term, $dbcnx) {
	$query = "SELECT COUNT(*) FROM user_list WHERE ip='".$ip_term."'";
	$result = functionCount ($query, $dbcnx);
	return $result;
}

function countTerminal ($dbcnx) {
	$query = "SELECT COUNT(*) FROM `terminal`";
	$result = functionCount ($query, $dbcnx);
	return $result;
}

function session_count_core ($dbcnx, $i, $ip) {
	$query = "SELECT COUNT(*) FROM user_list WHERE core='".$i."' and ip='".$ip."' and run='1'";
	$result = functionCount ($query, $dbcnx);
	return $result;
}

function session_online_terminal ($dbcnx, $i, $ip) {
	$query = mysql_query("SELECT * FROM user_list WHERE ip='".$ip."' and core='".$i."' and run='1' ",$dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectDomainUserTerminal ($dbcnx, $duser) {
	$query = mysql_query("SELECT * FROM domain_user WHERE username='".$duser."' ",$dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function terminal_select ($dbcnx, $id) {
	$query = mysql_query("SELECT * FROM terminal WHERE id='".$id."'",$dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}	
	return $result;
}

function user_list_select ($dbcnx, $user) {
	$query = mysql_query("SELECT * FROM `user_list` WHERE `name_user`='".$user."' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}