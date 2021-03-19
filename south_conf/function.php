<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function session_count_core ($dbcnx, $i, $ip_term) {
	$query = mysql_query("SELECT COUNT(*) FROM user_list WHERE core='".$i."' and ip='".$ip_term."' and run='1'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}

// проверка доступа к SOUTH всем пользователям
function expelAll ($dbcnx) {
	$query = mysql_query("SELECT access FROM expel_all", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['access'];
}

// проверка доступа к SOUTH всем пользователям
function expelTerm ($dbcnx, $ip) {
	$query = mysql_query("SELECT `run` FROM `terminal` WHERE `ip`='".$ip."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['run'];
}


function allUserData ($dbcnx, $username) {
	$query = mysql_query("SELECT * FROM domain_user WHERE username ='".$username."'", $dbcnx);
	$result = mysql_fetch_array($query);	
	return $result;
}

function selectMask ($dbcnx, $username) {
	$query = mysql_query("SELECT internet.mask, domain_user.id FROM internet, domain_user WHERE domain_user.id_object=internet.id_object and domain_user.username='".$username."'", $dbcnx);
	$result = mysql_fetch_assoc($query);	
	return $result;
}

function selectTerm ($dbcnx, $ip) {
	$query = mysql_query("SELECT * FROM terminal WHERE ip='".$ip."'", $dbcnx);
	$result = mysql_fetch_array($query);	
	return $result;
}

function selectVes ($dbcnx, $id) {
	$query = mysql_query("SELECT GROUP_CONCAT(SUBSTRING_INDEX(`ip`, '.', -1) SEPARATOR ' ') as sip FROM ves WHERE id_object='".$id."'", $dbcnx);
	$result = mysql_fetch_assoc($query);	
	return $result['sip'];
}