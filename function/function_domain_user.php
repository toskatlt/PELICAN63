<?php
//include $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$date_time = date("Y-m-d H:i:s");
$timestamp = date('Y-m-d G:i:s');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// ВЫВОД ПОЛЬЗОВАТЕЛЕЙ ПО ПОИСКОВОМУ ЗАПРОСУ
function allUser ($dbcnx) {
	$query = mysql_query("SELECT domain_user.id, domain_user.username, domain_user.fio, domain_user.phone, domain_user.id_object, domain_user.run FROM domain_user ORDER BY `domain_user`.`fio` ASC", $dbcnx);
//	$query = mysql_query("SELECT domain_user.id, domain_user.username, domain_user.fio, domain_user.phone, domain_user.id_object, position.position FROM domain_user, position WHERE domain_user.id_position=position.id ORDER BY `domain_user`.`fio` ASC", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}	
	return $result;
}

function allUserSelectObject ($dbcnx, $type) {
	$query = mysql_query("SELECT domain_user.id, domain_user.username, domain_user.fio, domain_user.phone, domain_user.id_object, domain_user.run FROM domain_user, object WHERE object.id=domain_user.id_object and object.type='".$type."' and domain_user.run='1' ORDER BY `domain_user`.`fio` ASC", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}	
	return $result;
}


// ОБЩЕЕ КОЛИЧЕСТВО ПОЛЬЗОВАТЕЛЕЙ
function sumAllDomainUserRunActive ($dbcnx) {
	$query = mysql_query("SELECT COUNT(*) FROM `domain_user` WHERE run = 1", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['COUNT(*)'];
}

// ВЫВОД ПОЛЬЗОВАТЕЛЕЙ ПО ПОИСКОВОМУ ЗАПРОСУ
function findUserByName ($dbcnx, $find) {
	$query = mysql_query("SELECT domain_user.id, domain_user.username, domain_user.fio, domain_user.phone, domain_user.id_object, domain_user.run FROM domain_user WHERE domain_user.fio LIKE '%".$find."%' ORDER BY `domain_user`.`fio` ASC", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}	
	return $result;
}

// ВЫВОД ПОЧТЫ ПОЛЬЗОВАТЕЛЕЙ
function selectEmailToUser ($dbcnx, $id_domain_user) {
	$query = mysql_query("SELECT email.email FROM email, domain_user WHERE domain_user.id=email.id_domain_user and domain_user.id='".$id_domain_user."' ", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['email'];
}

// ВЫВОД РАБОЧЕГО МЕСТА ПОЛЬЗОВАТЕЛЯ
function selectObjectToUser ($dbcnx, $id_domain_user) {
	$query = mysql_query("SELECT object.id, object.name, object.type, SUBSTRING_INDEX(building.address, ',', -2) as address FROM object, domain_user, building WHERE domain_user.id_object=object.id and object.id_building=building.id and domain_user.id='".$id_domain_user."' ", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}	
	return $result;
}
	
// ВЫВОД ОТДЕЛА И ДОЛЖНОСТИ ПОЛЬЗОВАТЕЛЯ
function selectPositionToUser ($dbcnx, $id_domain_user) {
	$query = mysql_query("SELECT `position`.position, `group`.name FROM `position`, `group`, `domain_user` WHERE position.id_group=group.id and position.id=domain_user.id_position and domain_user.id = '".$id_domain_user."' ", $dbcnx);
	$n = mysql_num_rows($query);	
	if ($n > 0) {
		for ($i = 0; $i < $n; $i++) {
			$result[] = mysql_fetch_assoc($query);
		}	
		return $result;
	}
}

function selectSquadUser ($dbcnx05, $user) {
	$query = mysql_query("SELECT user FROM `passwd` WHERE `user`='".$user."'", $dbcnx05);
	$row = mysql_fetch_assoc($query);
	return $row['user'];
}