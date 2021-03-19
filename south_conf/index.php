<?php
header("Content-Type: text/html; charset=utf-8");
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

include "config.php";
require_once("function.php");

$today = date("H:i d.m.y"); # 24:00 01.01.2000
$date_time = date("d-m-y H:i:s"); # 01-01-2000 24:00:00
$datetimeSQL = date("Y-m-d H:i:s"); # 2000-01-01 24:00:00

$ip = $_SERVER["REMOTE_ADDR"];
//$session = $_GET["session"];
$username = $_GET["username"];

//////// ПРОВЕРЯЕМ ЕСЛИ ЛИ В НАЗВАНИИ ДОМЕННОГО ПОЛЬЗОВАТЕЛЯ ПРИСТАВКА 'tsd' или 'tmc'
if (strpos($username, 'tsd') !== false) $tsd = "tsd"; 
else $tsd = "";
if (strpos($username, 'tmc') !== false) $tsd = "tmc";

$yeard = date("Y"); // текущий год
$monthd = date("n"); // текущий месяц
$month_newd = $monthd + 1; // след. месяц
$lastDayD = mktime(0, 0, 0, $month_newd, 0, $yeard); // последний день месяца
$day_now = date("j"); // текущий день
$hours_now = date("G"); // текущий час
$lastDayD_str = date('d', $lastDayD);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////# ДОСТУП В SOUTH #///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////// ПРОВЕРЯЕМ ЗАКРЫТ ЛИ ДОСТУП КО ВСЕМ ТЕРМИНАЛАМ В ТАБЛИЦЕ EXPEL_ALL    
$expelAll = expelAll ($dbcnx);
$access = $expelAll; # 0 или 1

//////// ЕСЛИ СЕГОДНЯ ПОСЛЕДНИЙ ДЕНЬ МЕСЯЦА И ВРЕМЯ БОЛЬШЕ 21
if (($day_now == $lastDayD_str) and ($hours_now >= 21)) {
	$access = 0;
}

//////// ЕСЛИ ДОСТУП ОТКРЫТ ДЛЯ ВСЕХ, ПРОВЕРЯЕМ ОТКРЫТ ЛИ ДОСТУП НА ТЕКУЩИЙ ТЕРМИНАЛ
if ($access == 1) {
	$expelTerm = expelTerm ($dbcnx, $ip);
	if (empty($expelTerm))  {
		echo "# Терминал ".$ip." не найден в базе! \r\n\r\n";
		$access = 1;
	} else {	
		$access = $expelTerm;
	}
}

//////// ЕСЛИ ДОСТУП ОТКРЫТ И НА ТЕРМИНАЛ, ПРОВЕРЯЕМ ОТКРЫТ ЛИ ДОСТУП ПЕРСОНАЛЬНО ДЛЯ ПОЛЬЗОВАТЕЛЯ
$allUserData = allUserData ($dbcnx, $username);
if (!empty($allUserData['id_object'])) {	
	$id = $allUserData['id_object'];
	if ($access == 1) {
		$access = $allUserData['run']; # 0 или 1
	}
}

//////// ДЛЯ ПОЛЬЗОВАТЕЛЕЙ `meandr`, `it` И `muxa` ДОСТУП В << SOUTH >> ОТКРЫТ ВСЕГДА
if (($username == 'meandr') or ($username == 'it') or ($username == 'muxa') or ($username == 'sp')) {
	$access = 2;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////// ЗАПРАШИВАЕМ МАСКУ ПОДСЕТИ МАГАЗИНА ВОШЕДШЕГО ПОЛЬЗОВАТЕЛЯ
$selectMask = selectMask ($dbcnx, $username);

/////////// ВЫВОДИМ ДАННЫЕ В ТЕКСТОВЫЙ ДОКУМЕНТ
echo "# Сгенерированный конфиг SOUTH_CONFIG.INI. Дата создания файла: ".$today."\r\n";
echo "# Имя пользователя ".$username."\r\n";
echo "# Ваш IP адрес: ".$ip." \r\n";
echo "# ID магазина: ".$id." \r\n";
//echo "# Домен: ".$domain." \r\n";
echo "# МASK подсети: ".$selectMask['mask'].". \r\n";
echo "MASK=".$selectMask['mask'].".\r\n";

/////////// ЕСЛИ БЫЛ ЗАПУЩЕН SOUTH_PIVO /////////////////////////////////////////////////
if (isset($_GET["pivo"])) { $southdir = "h:\south_pivo\\"; } 
else { $southdir = "h:\south\\"; }

echo "# Рабочий каталог SOUTH \r\n";
echo "WORKDIR=".$southdir."\r\n";
echo "WORKDIRSCALE=".$southdir."Scale\r\n\r\n";
echo "TSD=".$tsd."\r\n";
echo "RUN=".$access."\r\n\r\n";
echo "# Установка ядра процессора для работы \r\n"; 
   
if (!empty($expelTerm)) {   
	$selectTerm = selectTerm ($dbcnx, $ip);
	$ip_term = $selectTerm['ip'];

	for ($i=1; $i <= $selectTerm['core']; $i++) {	
		$session_count_core = session_count_core ($dbcnx, $i, $ip_term);
		$user_core[$i] = $session_count_core;
	}

	$last_core = array_keys($user_core, min($user_core))[0];
	echo "# Менее загруженное ядро ".$last_core." на терминале ".$ip_term." \r\n";
	mysql_query("UPDATE user_list SET core='".$last_core."' WHERE name_user='".$username."'", $dbcnx);
} else { 
	$last_core = 1; 
}


echo "# last_core = '".$last_core."' \r\n";
$PROC = [2,4,8,16,32,64,128,256,512,1024];
echo "PROC=".$PROC[($last_core-1)]." \r\n\r\n";

echo "# Номер виртуального порта штрихэтикетника (default: LPT2) \r\n";
echo "LPTPORT=LPT2\r\n\r\n";

$query_shtrich = mysql_fetch_assoc(mysql_query("SELECT * FROM `shtrich` WHERE `id_user`='".$selectMask['id']."'", $dbcnx));
if (!empty($query_shtrich)) {
	$print_name = $query_shtrich['share_strh'];
	$print_ip = $query_shtrich['ip'];
} else {
	$query_mod = mysql_fetch_assoc(mysql_query("SELECT printers.print_name, ws.ip FROM ws, printers WHERE printers.print_name in ('Citizen','Datamax') and printers.id_object='".$id."' and ws.id=printers.id_ws", $dbcnx));
	$print_name = $query_mod['print_name'];
	$print_ip = $query_mod['ip'];
}

echo "# Имя SMB-шары (Citizen, Datamax) \r\n";
echo "SHTRIH_MODEL=".$print_name."\r\n\r\n";

echo "# IP машины куда подключен штрихэтикетник \r\n";
echo "SHTRIH_IP=".$print_ip."\r\n\r\n";

echo "# IP машин куда скидывать прайс магазина (sample: 84 85) \r\n";
echo "COPY_PRC_IP=";

if (isset($_GET["pivo"])) {
	$q_PRC_IP = mysql_fetch_assoc(mysql_query("SELECT GROUP_CONCAT(SUBSTRING_INDEX(`ip`, '.', -1) SEPARATOR ' ') as ip FROM ws WHERE id_object='".$id."' and os = 'ZHMORE'"));
} else {
	$q_PRC_IP = mysql_fetch_assoc(mysql_query("SELECT GROUP_CONCAT(SUBSTRING_INDEX(`ip`, '.', -1) SEPARATOR ' ') as ip FROM ws WHERE id_object='".$id."' and type = '0' and os != 'ZHMORE' and os != 'ATOL'"));
}

echo $q_PRC_IP['ip']."\r\n\r\n";
echo "# COMM магазина - путь к базе данных магазина \r\n";

$comm = mysql_fetch_assoc(mysql_query("SELECT * FROM south_conf WHERE id_object='".$id."'"));
if (isset($comm['COMM'])) {
	echo "COMM=".strtolower($comm['COMM'])."\r\n\r\n";
} else {
	echo "COMM=commXX\r\n\r\n";
}

$selectVes = selectVes ($dbcnx, $id);	
echo "# IP адреса весов DIGI (sample: 6 7 8) \r\n";
echo "IP_VES=".$selectVes."\r\n\r\n";

$squery = mysql_fetch_assoc(mysql_query("SELECT * FROM log_session ORDER BY date LIMIT 1"));

if (isset($squery['date'])) { 
	$sDate = $squery['date']; 
} else { 
	$sDate = $date_time; 
}

$d = strtotime("-14 day", strtotime(date("Y-m-d H:i:s")));

if ($d <= strtotime($sDate)) {
	mysql_query("INSERT INTO `log_session`(`date`, `domain_user`, `ip_term`, `core`) VALUES ('".$datetimeSQL."','".$username."','".$ip."','".$last_core."')");
} else { 
	mysql_query("UPDATE `log_session` SET `date`='".$datetimeSQL."', `domain_user`='".$username."', `ip_term`='".$ip."', `core`='".$last_core."' WHERE `id`='".$squery['id']."'");	 
}