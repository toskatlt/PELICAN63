<?php
//include $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$date_time = date("Y-m-d H:i:s");
$timestamp = date('Y-m-d G:i:s');

// ВЫВОД ДАННЫХ ОБ АВТОМОБИЛЕ
function selectAvto ($dbcnx, $id_user) {
	$query = mysql_query("SELECT avto.*, domain_user.username as `username`, domain_user.fio as `fioAll`, CONCAT(SUBSTRING_INDEX(domain_user.fio, ' ', 1), ' ', SUBSTRING((SUBSTRING_INDEX(domain_user.fio, ' ', -2)),1,1), '.', SUBSTRING((SUBSTRING_INDEX(domain_user.fio, ' ', -1)),1,1), '.') AS fio FROM `avto`,`domain_user` WHERE domain_user.id=avto.id_domain_user and avto.id_domain_user='".$id_user."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

// ВЫВОД ВСЕГО СПИСКА ЗАПРАВОЧНЫХ СТАНЦИЙ
function allFueling ($dbcnx) {
	$query = mysql_query("SELECT * FROM `fueling` ORDER BY `fueling`.`name`", $dbcnx);		
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// ВЫВОД ВЫБРАННОЙ ЗАПРАВОЧНОЙ СТАНЦИИ 
function selectFueling ($dbcnx, $fueling) {
	$query = mysql_query("SELECT * FROM `fueling` WHERE id='".$fueling."'", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// КОЛИЧЕСТВО НЕ ОБРАБОТАННЫЙ ЧЕКОВ ПОЛЬЗОВАТЕЛЯ
function allFuelingCheckUser ($dbcnx, $id_user) {
	$query = mysql_query("SELECT count(*) FROM `fueling_check` WHERE `id_user`= '".$id_user."' and `point`='0' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['count(*)'];
}

// КОЛИЧЕСТВО НЕ ОБРАБОТАННЫЙ ЧЕКОВ ПОЛЬЗОВАТЕЛЯ
function allFuelingCheckUserReady ($dbcnx, $id_user) {
	$query = mysql_query("SELECT count(*) FROM `fueling_check` WHERE `id_user`= '".$id_user."' and `point`='1' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['count(*)'];
}

// ВЫВОД НЕ ОБРАБОТАННЫХ ЧЕКОВ ПОЛЬЗОВАТЕЛЯ
function allFuelingCheckUserNotUse ($dbcnx, $id_user) {
	$query = mysql_query("SELECT id, DATE_FORMAT(datetime, '%d.%m.%Y %H:%i' ) as datetime, fueling, liters, id_user FROM `fueling_check` WHERE `id_user`='".$id_user."' and `point`='0' ORDER BY UNIX_TIMESTAMP(STR_TO_DATE(datetime, '%Y-%m-%d')) ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// УДАЛЕНИЕ ЧЕКОВ ИЗ БАЗЫ
function delFuelingCheck ($dbcnx, $id) {
	mysql_query("DELETE FROM `fueling_check` WHERE id='".$id."' ", $dbcnx);
}

// ВЫВОД ОБРАБОТАННЫХ ЧЕКОВ ПОЛЬЗОВАТЕЛЯ
function allFuelingCheckUserUse ($dbcnx, $id_user, $date) {
	$query = mysql_query ("SELECT id, DATE_FORMAT(datetime, '%d.%m.%Y %H:%i' ) as datetime, fueling, liters, id_user FROM `fueling_check` WHERE `id_user`='".$id_user."' and `point`='1' and DATE_FORMAT(datetime, '%m.%Y') = '".$date."'", $dbcnx);
	$n = mysql_num_rows ($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allFuelingCheckYear ($dbcnx, $id_user) {
	$query = mysql_query ("SELECT DISTINCT(DATE_FORMAT(datetime, '%Y')) as year, count(DATE_FORMAT(datetime, '%Y')) as count FROM `fueling_check` WHERE `id_user`='".$id_user."' and `point`='1' GROUP BY year ORDER BY year DESC", $dbcnx);
	$n = mysql_num_rows ($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allFuelingCheckMonth ($dbcnx, $id_user, $year) {
	$query = mysql_query ("SELECT DISTINCT(DATE_FORMAT(datetime, '%m.%Y')) as datetime FROM `fueling_check` WHERE `id_user`='".$id_user."' and `point`='1' and DATE_FORMAT(datetime, '%Y') = '".$year."' ORDER BY UNIX_TIMESTAMP(STR_TO_DATE(datetime, '%Y-%m-%d')) DESC", $dbcnx);
	$n = mysql_num_rows ($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// ВЫВОД ВСЕХ НЕ ОБРАБОТАННЫХ ЧЕКОВ ПОЛЬЗОВАТЕЛЯ ЗА ИСКОМЫЕ СУТКИ
function userFuelingCheckInDay ($dbcnx, $id_user, $datetime) {
	$query = mysql_query("SELECT * FROM `fueling_check` WHERE id_user='".$id_user."' and DATE_FORMAT(datetime, '%Y-%m-%d')='".$datetime."' and `point`='0'", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// ВЫВОД 5 БЛИЖАЙШИХ МЕНЕЕ ИСКОМЫХ ДИСТАНЦИЙ ДО МАГАЗИНОВ
function selectTop5Distance ($dbcnx, $route, $unixtime) {
	$query = mysql_query("SELECT object.name, building.distance FROM object, building WHERE object.id_building = building.id and object.open='1' and building.distance < '".$route."' and UNIX_TIMESTAMP(object.date_open) < '".$unixtime."' ORDER BY building.distance DESC LIMIT 3,5", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// ВЫВОД 5 БЛИЖАЙШИХ МЕНЕЕ ИСКОМЫХ ДИСТАНЦИЙ ДО МАГАЗИНОВ
function selectDateCheckRoutet ($dbcnx) {
	$query = mysql_query("SELECT DISTINCT(`date`) FROM `fueling_check_routet` WHERE `check`='0' group by `date` ", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// ВЫВОД 5 БЛИЖАЙШИХ МЕНЕЕ ИСКОМЫХ ДИСТАНЦИЙ ДО МАГАЗИНОВ
function selectDateCheckRoutetFromDate ($dbcnx, $date) {
	$query = mysql_query("SELECT * FROM `fueling_check_routet` WHERE `date`='".$date."' and `check`='0' ", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// 
function selectSumInDayLiters ($dbcnx, $date, $id_user) {
	$query = mysql_query("SELECT sum(liters) as sum FROM `fueling_check` WHERE SUBSTRING_INDEX(datetime, ' ', 1) = '".$date."' and id_user = '".$id_user."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['sum'];
}