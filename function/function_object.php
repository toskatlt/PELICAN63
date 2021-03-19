<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$date_time = date("Y-m-d H:i:s");
$timestamp = date('Y-m-d G:i:s');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   АВТОРИЗАЦИЯ - РЕГИСТРАЦИЯ   //////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function authorization_lite ($dbcnx, $id) {	
	$query = mysql_query("SELECT domain_user.*, CONCAT(SUBSTRING_INDEX(`domain_user`.`fio`, ' ', 1), ' ', SUBSTRING((SUBSTRING_INDEX(`domain_user`.`fio`, ' ', -2)),1,1), '.') AS `fio` FROM domain_user WHERE id = '".intval($id)."' ", $dbcnx);
	$userdata = mysql_fetch_assoc($query);	
	return $userdata;
}

function authorization ($dbcnx, $id) {	
	$query = mysql_query("SELECT domain_user.*, group.id as id_group FROM domain_user, position, `group` WHERE domain_user.id = '".intval($id)."' and domain_user.id_position=position.id and position.id_group=group.id", $dbcnx);
	$userdata = mysql_fetch_array($query);	
	return $userdata;
}

# Функция для генерации случайной строки
function generateCode ($length) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;  
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0,$clen)];  
    }
    return $code;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   МАГАЗИНЫ   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (isset($_POST['change'])) {
    $query = mysql_query("SELECT SUBSTRING_INDEX(ws.ip, '.', 3) as ip FROM ws, object, building as b WHERE ws.id_object=object.id and b.id=object.id_building and object.type = 'ПЕЛИКАН' and object.open=1 and ws.os LIKE '%linux%' and b.area IN ('".$_POST['change']."') GROUP BY SUBSTRING_INDEX(ws.ip, '.', 3)", $dbcnx);
    $n = mysql_num_rows($query);
    for ($i = 0; $i < $n; $i++) {
        $result[] = mysql_fetch_assoc($query);
    }

    $str = '';
    $s = 1;
    foreach ($result as $arr) {
        if ($n == $s++) {
            $str .= 'name ~ '.$arr['ip'].'.';
        } else {
            $str .= 'name ~ '.$arr['ip'].'. or ';
        }
    }

    //echo $str;
}

if (isset($_POST['ip_ves'])) {
    $flag = mysql_query("SELECT proizv FROM ves WHERE ip = '".$_POST['ip_ves']."'", $dbcnx);
    $new_flag = $flag == 0 ? 1 : 0;
    mysql_query("UPDATE ves SET proizv = '".$new_flag."' WHERE ip = '".$_POST['ip_ves']."'", $dbcnx);
}

function allObject ($dbcnx) {
	$query = mysql_query("SELECT building.address, building.area, object.* FROM building, object WHERE building.id=object.id_building and object.open in (1,2) and object.type='ПЕЛИКАН' ORDER BY `object`.`name` ASC", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allObjectLimit ($dbcnx, $start, $end) {
	$query = mysql_query("SELECT object.* FROM object WHERE object.open in (1,2) and object.type='ПЕЛИКАН' LiMIT ".$start.",".$end."", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allObjectOffice ($dbcnx) {
	$query = mysql_query("SELECT building.address, SUBSTRING_INDEX(building.address, ',', -2) as short_area, building.area, object.* FROM building, object WHERE building.id=object.id_building and object.open in (1,2) and (object.type='ОФИС' or object.type='СКЛАД' or object.type='СТЕЛЛА') ORDER BY `object`.`name` ASC", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function ObjectOffice ($dbcnx) {
	$query = mysql_query("SELECT building.address, SUBSTRING_INDEX(building.address, ',', -2) as short_area, building.area, object.* FROM building, object WHERE building.id=object.id_building and object.open in (1,2) and object.type='ОФИС' ORDER BY `object`.`name` ASC", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allObjectMindal ($dbcnx) {
	$query = mysql_query("SELECT building.address, building.area, object.* FROM building, object WHERE building.id=object.id_building and object.open in (1,2) and object.type='МИНДАЛЬ' ORDER BY cast(SUBSTRING_INDEX(object.name, '№', -1) as unsigned) ASC", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allObjectWithArea ($dbcnx, $area) {
	$query = mysql_query("SELECT building.address, building.area, object.* FROM building, object WHERE building.id=object.id_building and object.open in (1,2) and object.type='ПЕЛИКАН' and building.area = '".$area."' ORDER BY `object`.`name` ASC", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allObjectClose ($dbcnx) {
	$query = mysql_query("SELECT building.address, building.area, object.* FROM building, object WHERE building.id=object.id_building and object.open = 0 ORDER BY `object`.`name` ASC", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allObjectNoBuilding ($dbcnx) {
	$query = mysql_query("SELECT *  FROM `object` WHERE `id_building` = 0 ORDER BY `open` ASC", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allObjectPelicafe ($dbcnx) {
    $sql = "
        SELECT object.*  
        FROM object o
        LEFT JOIN dop_form df ON df.id_object = o.id_object
        WHERE df.cafe = 1
    ";
    $query = mysql_query($sql, $dbcnx);
    $n = mysql_num_rows($query);
    for ($i = 0; $i < $n; $i++) {
        $result[] = mysql_fetch_assoc($query);
    }
    return $result;
}

function allObjectBakery ($dbcnx) {
    $sql = "
        SELECT object.*  
        FROM object o
        LEFT JOIN dop_form df ON df.id_object = o.id_object
        WHERE df.bakery = 1
    ";
    $query = mysql_query($sql, $dbcnx);
    $n = mysql_num_rows($query);
    for ($i = 0; $i < $n; $i++) {
        $result[] = mysql_fetch_assoc($query);
    }
    return $result;
}

function allObjectProduction ($dbcnx) {
    $sql = "
        SELECT object.*  
        FROM object o
        LEFT JOIN dop_form df ON df.id_object = o.id_object
        WHERE df.production = 1
    ";
    $query = mysql_query($sql, $dbcnx);
    $n = mysql_num_rows($query);
    for ($i = 0; $i < $n; $i++) {
        $result[] = mysql_fetch_assoc($query);
    }
    return $result;
}

function allObjectEmail ($dbcnx) {
	$query = mysql_query("SELECT building.address, building.area, object.*, email.email FROM building, object, email WHERE building.id=object.id_building and email.id_object=object.id and object.open in (1,2) and object.type='ПЕЛИКАН' ORDER BY `object`.`name` ASC", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function taxcomObject ($dbcnx, $idTaxcom) {
	$query = mysql_query("SELECT object.* FROM `kod1c`, `object` WHERE kod1c.id_object=object.id and kod1c.taxcom='".$idTaxcom."'", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allObjectWithAreaEmail ($dbcnx, $area) {
	$query = mysql_query("SELECT building.address, building.area, object.*, email.email FROM building, object, email WHERE building.id=object.id_building and email.id_object=object.id and object.open in (1,2) and object.type='ПЕЛИКАН' and building.area = '".$area."' ORDER BY `object`.`name` ASC", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function findObjectByName ($dbcnx, $find) {
	$query = mysql_query("SELECT building.address, building.area, object.* FROM building, object WHERE building.id=object.id_building and object.open in (1,2) and object.type='ПЕЛИКАН' and object.name LIKE '%".$find."%' ORDER BY `object`.`name` ASC", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}	
	return $result;
}

function findObjectByNameInCloses ($dbcnx, $find) { // поиск по названию магазинов из списка закрытых
	$query = mysql_query("SELECT building.address, building.area, object.* FROM building, object WHERE building.id=object.id_building and object.open = '0' and object.type='ПЕЛИКАН' and object.name LIKE '%".$find."%' ORDER BY `object`.`name` ASC", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}	
	return $result;
}

// ВЫБОР ОБЬЕКТА ПО ID
function selectObject ($dbcnx, $id) { 
	return mysql_fetch_assoc( mysql_query("SELECT building.address, building.area, building.distance, object.* FROM building, object WHERE building.id=object.id_building and object.id='".$id."'", $dbcnx)); 
}
function objectFromEmail ($dbcnx, $email) { 
	return mysql_fetch_assoc( mysql_query("SELECT object.name, object.id FROM email, object WHERE email.id_object=object.id and email.email = '".$email."'", $dbcnx)); 
}

function whoseEmail ($dbcnx, $email) { 
	$query = mysql_query("SELECT * FROM email WHERE email = '".$email."'", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result; 
}

// СУММА КОЛИЧЕСТВА МАГАЗИНОВ ПЕЛИКАН С ИНДИКАТОРОМ 'online' РАВНЫМ '1'
function selectCountObject ($dbcnx) {
	$query = "SELECT COUNT(*) as id FROM object WHERE open=1";
	$result = mysql_query($query,$dbcnx);
	if (!$result)
		die(mysql_error($dbcnx));
	$row = mysql_fetch_assoc($result);
	$selectCountObject = $row['id'];
	return $selectCountObject;
}

// СУММА КОЛИЧЕСТВА МАГАЗИНОВ ПЕЛИКАН С ИНДИКАТОРОМ 'online' РАВНЫМ '1' и type='ПЕЛИКАН'
function selectAllPelicanCountObject ($dbcnx) {
	$query = mysql_query("SELECT COUNT(*) FROM object WHERE open=1 and `type` LIKE 'ПЕЛИКАН'", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['COUNT(*)'];
}

// СУММА КОЛИЧЕСТВА РАБОЧИХ СТАНЦИЙ С ТИПОМ (type) 1
function countWsLinuxInObject ($dbcnx, $id_object) {
	$query = "SELECT COUNT(*) as id FROM ws WHERE type = '1' and id_object = '".$id_object."'";
	$result = mysql_query($query,$dbcnx);
	$row = mysql_fetch_assoc($result);
	$countWsLinuxInObject = $row['id'];
	return $countWsLinuxInObject;
}

function countAllWsLinuxInObject ($dbcnx) {
	$query = mysql_query("SELECT COUNT(*) as id FROM ws, object WHERE ws.type = '1' and ws.id_object=object.id and object.open=1", $dbcnx);
	$row = mysql_fetch_assoc($query);
	$result = $row['id'];
	return $result;
}

// ПОИСК ОБЬЕКТОВ ПО ИМЕНИ
function findObject ($dbcnx, $find_object) {
	//$query = "SELECT building.address, building.area, object.* FROM building, object, internet WHERE building.id=object.id_building and internet.id_object=object.id_building and object.open in (1,2) and (object.name like '%".$find_object."%' OR internet.mask like '%".$find_object."%') ORDER BY `object`.`name` ASC";
	$query = "SELECT building.address, building.area, object.* FROM building, object WHERE building.id=object.id_building and object.open in (1,2) and object.name like '%".$find_object."%' ORDER BY `object`.`name` ASC";
	$result = mysql_query($query,$dbcnx);
	if (!$result)
		die(mysql_error($dbcnx));
	$n = mysql_num_rows($result);
	$findObject = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$findObject[] = $row;
	}
	return $findObject;
}

function selectSouthConfInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM south_conf WHERE id_object='".$id_object."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function selectCod1C ($dbcnx, $id_object) {
	$query = mysql_query("SELECT work_domain_user.* FROM `work_domain_user`, `work_position` WHERE work_domain_user.id_position = work_position.id and work_domain_user.id_object='".$id_object."' and work_domain_user.idx='1' and work_position.name = 'Директор магазина'
", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function selectEgaisFio ($dbcnx, $fio) {
	$query = mysql_query("SELECT 1 FROM `egais_fio_all` WHERE fio='".$fio."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function selectAtolInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM `egais` WHERE `id_object`='".$id_object."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

// ВЫВОД ТИПА КАМЕР НАБЛЮДЕНИЯ НА ОБЪЕКТЕ
function videCamSelectInObject ($dbcnx, $id) {
	$query = "SELECT * FROM `video` WHERE id_object='".$id."' ";
	$result = mysql_query($query,$dbcnx);	
	if (!$result)
		die(mysql_error($dbcnx));		
	$n = mysql_num_rows($result);
	$videCamSelectInObject = array();	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$videCamSelectInObject[] = $row;
	}
	return $videCamSelectInObject;
}

// 
function selectAllArea ($dbcnx) {
	$query = mysql_query("SELECT DISTINCT(area) FROM `building` WHERE area != ''", $dbcnx);		
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// ПЕРЕВОД IP РОУТЕРА В СТРОКУ ДЛЯ TABLESORTER
function piecesIPRoute ($ipRoute) {
	if (isset($ipRoute)) {
		$pieces = explode(".", $ipRoute);
		if (isset($pieces[2])) {
			if (strlen($pieces[2]) == 1) {
				$pieces[2] = "00".$pieces[2];
			} elseif (strlen($pieces[2]) == 2) {
				$pieces[2] = "0".$pieces[2];
			}
			$piecesIPRoute = $pieces[0]."".$pieces[1].".".$pieces[2];
			return $piecesIPRoute;
		}
	}
}

function allSelectInternetInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM internet WHERE internet.id_object='".$id_object."'", $dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function selectInternetInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM internet WHERE id_object='".$id_object."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function selectInternetInObjectISP ($dbcnx, $id_object) {
	$query = mysql_query("SELECT isp.name, isp.phone, internet.* FROM internet, isp WHERE id_object='".$id_object."' and internet.id_isp=isp.id", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function selectProviders ($dbcnx, $id) {
	$query = mysql_query("SELECT * FROM isp WHERE id='".$id."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

// ВЫБОР ПРИНТЕРОВ ШТРИХ ЭТИКЕТОК В ВЫБРАННОМ МАГАЗИНЕ
function shtrih_shop ($dbcnx, $id_object) {
	$query = mysql_query("SELECT domain_users.username, domain_users.share_strh, domain_users.id_shtrih FROM domain_users,ws WHERE domain_users.id_mag='".$id_object."' and ws.id_mag='".$id_object."' GROUP BY domain_users.username", $dbcnx);
	$n = mysql_num_rows($query);
	$shtrih_shop = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function allProviders ($dbcnx) {
	$query = mysql_query("SELECT * FROM `isp`",$dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

// ВЫВОД IP АДРЕСС РОУТЕРА ВЫБРАННОГО МАГАЗИНА
function selectInternetMaskInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT mask FROM `internet` WHERE id_object='".$id_object."'", $dbcnx);			
	$row = mysql_fetch_assoc($query);
	$result = $row['mask'];
	return $result;
}

function format_value ($value) {
	return empty($value) ? ' ' : htmlspecialchars($value);
}

// ДАТЫ ОТКРЫТИЯ МАГАЗИНОВ ПО ГОДАМ
function openAllShopDate ($dbcnx, $year) {
	$query = "SELECT `id`,`name`,`date_open` FROM `object` WHERE open='1' and YEAR(`date_open`) = '".$year."' ORDER BY `object`.`date_open`  DESC ";
	$result = mysql_query($query,$dbcnx);	
	$n = mysql_num_rows($result);
	$openAllShopDate = array();	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$openAllShopDate[] = $row;
	}
	return $openAllShopDate;
}

// ВЫВОД ДАННЫХ О ВИДЕОНАБЛЮДЕНИИ НА ОБЪЕКТЕ
function videoCamSelectInObject ($dbcnx, $id_object) {
	$query = "SELECT * FROM `video` WHERE `id_object`='".$id_object."' ";
	$result = mysql_query($query,$dbcnx);	
	$n = mysql_num_rows($result);
	$videoCamSelectInObject = array();	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$videoCamSelectInObject[] = $row;
	}
	return $videoCamSelectInObject;
}

// VIDEOREG камеры с видеорегистраторов 
function resize ($width, $height, $i, $width_img, $height_img, $shop_comm, $ip) {	
	$im = imagecreatetruecolor($width, $height);
	$im1 = imagecreatefromjpeg("/var/www/html/www/video/".$shop_comm."/".$ip."/".$i.".jpg");	
	imagealphablending($im, true);
	imagealphablending($im, 1);
	imagealphablending($im1, 1);	
	imagecopyresampled($im, $im1, 0, 0, 0, 0, $width, $height, $width_img, $height_img);	
	imagejpeg($im, "/var/www/html/www/video/".$shop_comm."/".$ip."/full/".$i.".jpg");
	imagedestroy($im); 
}

//
function selectDomainUserInObject ($dbcnx, $id_object) {
	//$query = mysql_query("SELECT * FROM `domain_user` WHERE `id_object`='".$id_object."' and run='1'",$dbcnx);
	$query = mysql_query("SELECT * FROM `domain_user` WHERE `id_object`='".$id_object."'", $dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function selectDomainUserInObjectNotTSD ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM `domain_user` WHERE `id_object`='".$id_object."' and `username` NOT LIKE '%tsd%' ORDER BY `id` DESC",$dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function selectShtrichAll ($dbcnx, $id_object) {
	$query = mysql_query("SELECT domain_user.username, shtrich.* FROM shtrich, domain_user WHERE shtrich.id_object = '".$id_object."' and shtrich.id_user = domain_user.id",$dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function selectShtrich ($dbcnx, $id_user) {
	$query = mysql_query("SELECT * FROM `shtrich` WHERE `id_user`='".$id_user."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function selectDomainUser ($dbcnx, $id) {
	$query = mysql_query("SELECT * FROM `domain_user` WHERE `id`='".$id."' ",$dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

// ВЫВОД ДАННЫХ О ЕГАИС ПО НОМЕРУ ОБЪЕКТА
function selectEgais ($dbcnx, $id_object) {
	$query = "SELECT `id`, `ip`, `date_rsa`, `date_gost`, `rsa`, `kpp`, `bild_number` FROM `egais` WHERE `id_object`='".$id_object."' ";
	$result = mysql_query($query,$dbcnx);
	$selectEgais = mysql_fetch_assoc($result);
	return $selectEgais;
}

// 
function domainUserOnline ($dbcnx, $username) {
	$query = "SELECT id FROM `session_online` WHERE `name`='".$dus_username."'";
	$result = mysql_query($query,$dbcnx);
	$n = mysql_num_rows($result);
	$domainUserOnline = array();	
	for ($i = 0; $i < $n; $i++){
		$row = mysql_fetch_assoc($result);
		$domainUserOnline[] = $row;
	}
	return $domainUserOnline;
}

// ВЫВОД ИЗ ТАБЛИЦЫ ws ПО ВЫБРАННОМУ ОБЬЕКТУ с типом
function selectWsInObject ($dbcnx, $id_object, $type) {
	$query = "SELECT id, ip, os, puppet, udev, title FROM ws WHERE id_object='".$id_object."' and type='".$type."'";
	$result = mysql_query($query,$dbcnx);
	$n = mysql_num_rows($result);
	$selectWsInObject = array();	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$selectWsInObject[] = $row;
	}
	return $selectWsInObject;
}

function selectWsInObjectOnlyWin ($dbcnx, $id_object) {
	$query = mysql_query("SELECT id, ip, os, puppet, udev FROM ws WHERE id_object='".$id_object."' and type='0' and os !='ATOL'", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

// ВЫВОД ДАННЫХ ПИНПАДА ПО ID_WS
function selectPinpadInObject ($dbcnx, $id_ws) {
	$query = "SELECT * FROM `pinpad` WHERE id_ws='".$id_ws."'";
	$result = mysql_query($query,$dbcnx);
	$n = mysql_num_rows($result);
	$selectPinpadInObject = array();	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$selectPinpadInObject[] = $row;
	}
	return $selectPinpadInObject;
}

// ВЫБОР TSD В ВЫБРАННОМ МАГАЗИНЕ
function tsdInObject ($dbcnx240, $mask) {
	$query = mysql_query("SELECT * FROM `tsd_log` WHERE `lanip` LIKE '%".$mask.".%' and DATE_SUB(NOW(), INTERVAL 3 DAY) < datetime and idx='1' ORDER BY `datetime`", $dbcnx240);
	for ($i = 0; $i < mysql_num_rows($query); $i++) {
		$result[] = mysql_fetch_assoc($query);
	}	
	return $result;
}

function selectRouterModel ($dbcnx240, $mask) {
	$query = mysql_query("SELECT * FROM `stat` WHERE `lanip` LIKE '%".$mask.".%' ORDER BY `stat`.`id`  DESC LIMIT 1", $dbcnx240);
	$row = mysql_fetch_assoc($query);
	return $row;
}

function printersInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT printers.print_name, printers.id, ws.ip FROM printers, ws WHERE printers.id_ws=ws.id and printers.id_object='".$id_object."'", $dbcnx);
	for ($i = 0; $i < mysql_num_rows($query); $i++) $result[] = mysql_fetch_assoc($query);
	return $result;
}

function printersShtrihInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT ws.ip, printers.* FROM printers, ws WHERE printers.id_object = '".$id_object."' and printers.id_ws=ws.id and (printers.print_name = 'Citizen' or printers.print_name ='Datamax')", $dbcnx);
	for ($i = 0; $i < mysql_num_rows($query); $i++) $result[] = mysql_fetch_assoc($query);
	return $result;
}

function vesInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM ves WHERE id_object='".$id_object."'", $dbcnx);
	for ($i = 0; $i < mysql_num_rows($query); $i++) $result[] = mysql_fetch_assoc($query);
	return $result;
}

function selectTSD ($dbcnx, $id) { return mysql_fetch_assoc( mysql_query("SELECT * FROM `tsd` WHERE `id`='".$id."'", $dbcnx)); }
function selectVes ($dbcnx, $id) { return mysql_fetch_assoc( mysql_query("SELECT * FROM `ves` WHERE `id`='".$id."'", $dbcnx)); }
function selectWs ($dbcnx, $id) { return mysql_fetch_assoc( mysql_query("SELECT * FROM `ws` WHERE `id`='".$id."'", $dbcnx)); }

// СПИСОК АДМИНОВ КОТОРЫЕ ЯВЛЯЮТСЯ ОТВЕТСТВЕННЫМ ЗА ОТКРЫТИЕ ВЫБРАННОГО МАГАЗИНА
function whoOpener ($dbcnx, $id_object) {
	$query = mysql_query("SELECT domain_user.id, domain_user.fio, opener.id FROM opener, domain_user WHERE opener.id_domain_user = domain_user.id and opener.id_object='".$id_object."' ORDER BY  domain_user.fio ASC", $dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

// СПИСОК АДМИНОВ КОТОРЫЕ НЕ ЯВЛЯЮТСЯ ОТВЕТСТВЕННЫМ ЗА ОТКРЫТИЕ ВЫБРАННОГО МАГАЗИНА
function adminUserWhoNotOpener ($dbcnx, $id_object) {
	$query = mysql_query("SELECT domain_user.id, domain_user.fio FROM domain_user, position WHERE domain_user.id NOT IN (SELECT id_domain_user as id FROM opener WHERE opener.id_object='".$id_object."') and domain_user.id_position=position.id and position.id_group = 1 and domain_user.access > 7 ORDER BY domain_user.fio ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}	
	return $result;
}

function videoInObject ($dbcnx, $id_object) {
	$query = "SELECT * FROM video WHERE id_object='".$id_object."' ORDER BY ip ASC";
	$result = mysql_query($query,$dbcnx);
	$n = mysql_num_rows($result);
	$videoInObject = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$videoInObject[] = $row;
	}
	return $videoInObject;
}

function scannersInWs ($dbcnx, $id_ws) {
	$query = "SELECT * FROM scanners WHERE id_ws='".$id_ws."' ORDER BY `scanners`.`model` DESC";
	$result = mysql_query($query,$dbcnx);
	$n = mysql_num_rows($result);
	$scannersInWs = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$scannersInWs[] = $row;
	}
	return $scannersInWs;
}

function vesInWs ($dbcnx, $ip_ws) {
	$query = mysql_query("SELECT * FROM `ves_kas` WHERE ip='".$ip_ws."'", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['type'];
}

function emailInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM `email` WHERE id_object='".$id_object."' ", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function emailInDomainUser ($dbcnx, $id_domain_user) {
	$query = mysql_query("SELECT * FROM `email` WHERE id_domain_user = '".$id_domain_user."' ", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['id'];
}

function phoneInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM `phone` WHERE id_object='".$id_object."' ORDER BY number DESC", $dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function honeywellInObject ($dbcnx, $id_object) {
	$query = "SELECT count(scanners.id) FROM ws, scanners WHERE ws.id=scanners.id_ws and ws.id_object='".$id_object."' and scanners.model='1450g'";
	$result = mysql_query($query,$dbcnx);
	$row = mysql_fetch_assoc($result);
	$honeywellInObject = $row['count(scanners.id)'];
	return $honeywellInObject;
}

function sessionUserStat ($dbcnx, $user) {
	$query = mysql_query("SELECT terminal.id as idterm, user_list.* FROM terminal, user_list WHERE terminal.ip=user_list.ip and `name_user`='".$user."'", $dbcnx);
	$n = mysql_num_rows($query);
	$result = array();	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}	
	return $result;
}

function wsInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM `ws` WHERE id_object = '".$id_object."' and type = '0' ", $dbcnx);
	$n = mysql_num_rows($query);
	$result = array();	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}	
	return $result;
}

function selectIDwithIPws ($dbcnx, $ip) {
	$query = mysql_query("SELECT `id_object` FROM `ws` WHERE `ip`='".$ip."' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['id_object'];
}

function wsAllOS ($dbcnx) {
	$query = mysql_query("SELECT distinct(os) FROM `ws` WHERE os != '0'", $dbcnx);
	$n = mysql_num_rows($query);
	$result = array();	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}	
	return $result;
}

function selectIPWSInObject ($dbcnx, $id) {
	$query = mysql_query("SELECT ip FROM `egais` WHERE `id`='".$id."'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function selectDHCPleases ($dbcnx240, $mask) {
	$query = mysql_query("SELECT * FROM `dhcp_leases` WHERE SUBSTRING_INDEX(`lanip`, '.', 3) = '".$mask."' and `datetime` > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -30 DAY)) ORDER BY `type` ASC", $dbcnx240);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}	
	return $result;
}

function selectDHCPleasesCountFR ($dbcnx240, $mask) {
	$query = mysql_query("SELECT count(*) FROM `dhcp_leases` WHERE SUBSTRING_INDEX(`lanip`, '.', 3) = '".$mask."' and type='fr' ", $dbcnx240);
	$result = mysql_fetch_assoc($query);
	return $result['count(*)'];
}

function selectALLDHCPleasesCountFR ($dbcnx240) {
	$query = mysql_query("SELECT count(*) FROM `dhcp_leases` WHERE type='fr' ", $dbcnx240);
	$result = mysql_fetch_assoc($query);
	return $result['count(*)'];
}

function selectSessionUser ($dbcnx, $domain_user) {
	$query = mysql_query("SELECT max(date), ip_term FROM `log_session` WHERE domain_user='".$domain_user."' GROUP by ip_term", $dbcnx);
	$n = mysql_num_rows($query);
	$selectSessionPel = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

// СУММА СКАНЕРОВ 1450g НА ОБЪЕКТАХ
function selectHandScan ($dbcnx) {
	$result = mysql_query("SELECT COUNT(*) FROM scanners WHERE model='1450g' ", $dbcnx);
	$row = mysql_fetch_assoc($result);
	return $row['COUNT(*)'];
}

function video ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM video WHERE id_object='".$id_object."' ORDER BY ip ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function all_email ($dbcnx) {
	$query = mysql_query("SELECT email.email from email, object WHERE email.id_object=object.id and (object.open = 2 or object.open = 1) and email.email != ''", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function all_email_tlt ($dbcnx) {
	$query = mysql_query("SELECT email.email FROM building, object, email WHERE building.id=object.id_building and email.id_object=object.id and object.open in (1,2) and object.type='ПЕЛИКАН' and building.area = 'TLT' ORDER BY `object`.`name` ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function sale_otdel ($dbcnx) { // ВЫВОД СПИСКА СОТРУДНИКОВ ОТДЕЛА ПРОДАЖ
	$query = mysql_query("SELECT email.email as email FROM `domain_user`, `email`, `group`, `position` WHERE email.id_domain_user=domain_user.id and domain_user.id_position=position.id and group.id=position.id_group and (group.id=14 or group.id=5) and domain_user.run=1", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function auditors_otdel ($dbcnx) { // ВЫВОД СПИСКА СОТРУДНИКОВ ОТДЕЛА РЕВИЗОРОВ
    $query = mysql_query("SELECT email.email as email FROM `domain_user`, `email`, `group`, `position` WHERE email.id_domain_user=domain_user.id and domain_user.id_position=position.id and group.id=position.id_group and group.id=4 and domain_user.run=1", $dbcnx);
    $n = mysql_num_rows($query);
    for ($i = 0; $i < $n; $i++) {
        $result[] = mysql_fetch_assoc($query);
    }
    return $result;
}

function allPosition ($dbcnx) {
	$query = mysql_query("SELECT * FROM `position`", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectPositionIngroup ($dbcnx, $group) {
	$query = mysql_query("SELECT * FROM `position` WHERE id_group='".$group."'", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectPosition ($dbcnx, $id_domain_user) {
	$query = mysql_query("SELECT position.id, position.position FROM `domain_user`, `position` WHERE domain_user.id_position=position.id and domain_user.id='".$id_domain_user."'", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}


function allGroup ($dbcnx) {
	//$query = mysql_query("SELECT * FROM `group` ORDER BY `group`.`id` ASC", $dbcnx);
	$query = mysql_query("SELECT * FROM `group` ORDER BY `group`.`name` ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function dop_form ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM `dop_form` WHERE `id_object`='".$id_object."'", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectObjectWithPelicafe ($dbcnx) {
	$query = mysql_query("SELECT building.address, building.area, object.*, email.email FROM building, object, email, pelicafe WHERE object.id=pelicafe.id_object and building.id=object.id_building and email.id_object=object.id ORDER BY `object`.`name` ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allOfficeUserEmailSelectGroup ($dbcnx, $group_name) {
	$query = mysql_query("SELECT domain_user.*, email.email, position.position, group.name FROM domain_user, email, position, `group` WHERE domain_user.id=email.id_domain_user and domain_user.id_position=position.id and position.id_group=group.id and group.name = '".$group_name."' and domain_user.run = 1 ORDER BY `position`.`access` DESC, `position`.`position` ASC,`domain_user`.`fio` ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allOfficeUserEmail ($dbcnx) {
	$query = mysql_query("SELECT domain_user.*, email.email, position.position, group.name FROM domain_user, email, position, `group` WHERE domain_user.id=email.id_domain_user and domain_user.id_position=position.id and position.id_group=group.id ORDER BY `email`.`email` ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function userListSelect ($dbcnx, $du) {
	$query = mysql_query("SELECT * FROM `user_list` WHERE `name_user`='".$du."' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function check20th ($dbcnx, $id_object) {
	$query = mysql_query("SELECT count(*) as not_work FROM `ws` WHERE `check20th` < '9' and `id_object`='".$id_object."' and `os`='LINUX'", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function check20thAll ($dbcnx) {
	$query = mysql_query("SELECT count(*) as not_work FROM `ws`,`object` WHERE object.id=ws.id_object and object.open = 1 and ws.`check20th` < '9' and ws.`os`='LINUX'", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function averageNumberOfChecks ($dbcnx, $mask) {
	$query = mysql_query("SELECT sum(quantity) as A, count(*) as B, ROUND(sum(quantity)/count(*)) as C FROM `cash_vouchers_sum` WHERE `ip` LIKE '%".$mask.".%' ORDER BY `ip` ASC", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['C'];		
}

function countWSlinux ($dbcnx, $id_object) {
	$query = mysql_query("SELECT count(*) as D FROM `ws` WHERE `id_object`='".$id_object."' and `os`='LINUX' and `run`='1'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['D'];		
}	

function reitingAllPelican ($dbcnx) {
	$query = mysql_query("SELECT object.id, internet.mask FROM internet, object WHERE object.id=internet.id_object and object.open in (1,2) and object.type='ПЕЛИКАН' and object.open='1' ORDER BY `object`.`name` ASC", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$idObject_mask[] = mysql_fetch_assoc($query);
	}
	
	$i=0;
	foreach ($idObject_mask as $iOm) {
		$s = averageNumberOfChecks ($dbcnx, $iOm['mask']);
		$d = countWSlinux ($dbcnx, $iOm['id']);
		if (!empty($s)) { $idObject_mask[$i++]['C'] = $s*$d; }
		else { $idObject_mask[$i++]['C'] = 1*$d; }
		
	}	

	foreach($idObject_mask as $key=>$arr) {
		$idObject_C[$key]=$arr['C'];
	}
	 
	array_multisort($idObject_C, SORT_NUMERIC, $idObject_mask);
	return $idObject_mask;
}	

// ВЫВОД 5 ПОСЛЕДНИХ ПРОВЕРОК БД СКЛАДА
function checkBD ($dbcnx, $id_object) {
	$query = mysql_query("SELECT south_chk.* FROM south_chk, south_conf WHERE south_conf.id_object = '".$id_object."' and south_conf.name_sklada = south_chk.sklad ORDER BY `south_chk`.`date` DESC LIMIT 5", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;		
}

// ВЫВОД ПОСЛЕДНЕЙ ПРОВЕРКИ ВСЕХ БД СКЛАДОВ МАГАЗИНОВ
function checkBD1 ($dbcnx) {
	$query = mysql_query("SELECT p1.id, p1.name, p3.date, p3.log, p3.id as ch_id FROM object p1, south_conf p2 LEFT JOIN (SELECT south_chk.* FROM south_chk INNER JOIN (SELECT MAX(`date`) AS date, sklad FROM `south_chk` GROUP BY `sklad` ORDER BY `date` DESC) q ON (q.date = south_chk.date) AND (q.sklad = south_chk.sklad) ORDER BY south_chk.`date` DESC) p3 ON (SUBSTRING(p2.name_sklada,1,8) = p3.sklad) WHERE p1.id = p2.id_object AND p1.open = 1 ORDER BY `p3`.`date` DESC", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;		
}

// SELECT south_chk.* FROM south_chk INNER JOIN (SELECT MAX(`date`) AS date, sklad FROM `south_chk` GROUP BY `sklad` ORDER BY `date` DESC) q ON (q.date = south_chk.date) AND (q.sklad = south_chk.sklad) ORDER BY south_chk.`date` DESC

// ВЫВОД ВЫБРАННОЙ ПРОВЕРИ БД СКЛАДА
function selectCheckBD ($dbcnx, $id_check) {
	$query = mysql_query("SELECT * FROM `south_chk` WHERE id='".$id_check."'", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;		
}

// ВЫБОР ОТЧЕТОВ ПРОВЕРОК БД НЕ ОТПРАВЛЕННЫЕ МАГАЗИНАМ
function selectCheckBDnotOut ($dbcnx) {
	$query = mysql_query("SELECT * FROM `south_chk` WHERE `out`='0' ORDER BY `date` DESC", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;		
}

function objectEmailToNameSklad ($dbcnx, $sklad) {
	$query = mysql_query("SELECT email.email AS e FROM `south_chk`, `south_conf`, `email` WHERE south_chk.sklad = south_conf.name_sklada and south_conf.id_object = email.id_object and south_chk.sklad='".$sklad."' limit 1", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['e'];	
}


function selectEmployees ($dbcnx, $id_object) {
	$query = mysql_query("SELECT work_domain_user.fio, work_position.name, work_position.access FROM `work_domain_user`, `work_position` WHERE work_domain_user.id_position=work_position.id and work_domain_user.id_object = '".$id_object."' and work_domain_user.idx ='1' ORDER BY `work_position`.`access` DESC", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;		
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   EGAIS    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Сумма всех записей в таблице EGAIS где DATE_KEY (срок окончания действия ключа) истекает не раньше чем через 18 дней и магазин открыт (open='1')
function sumAllJacarta ($dbcnx) {
	$query = mysql_query("SELECT count(*) FROM egais, object WHERE object.id=egais.id_object and ((egais.date_rsa- INTERVAL 18 DAY < NOW()) or (egais.date_gost- INTERVAL 18 DAY < NOW())) and object.open='1'", $dbcnx);	
	$row = mysql_fetch_assoc($query);
	$result = $row['count(*)'];	
	return $result;
}

function allJacarta ($dbcnx) {
	//$query = mysql_query("SELECT object.name, egais.* FROM egais, object WHERE egais.id_object = object.id and object.open != '0' and length(egais.ip)>5 and object.type='ПЕЛИКАН'", $dbcnx);
	$query = mysql_query("SELECT * FROM object WHERE object.open != '0' and (object.type='ПЕЛИКАН' or object.name LIKE '%ЛПО%' or object.name LIKE '%Витрина%') ORDER BY `object`.`type`  ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[]  = mysql_fetch_assoc($query);
	}
	return $result;
}

function allJacartaPlus ($dbcnx) {
	$query = mysql_query("SELECT object.name, egais.* FROM egais, object WHERE egais.id_object = object.id and object.open != '0' and length(egais.ip)>5 and object.type='ПЕЛИКАН'", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[]  = mysql_fetch_assoc($query);
	}
	return $result;
}

function allJacartaBuilding ($dbcnx) {
	$query = mysql_query("SELECT object.name, SUBSTRING_INDEX(building.address, ',', -3) as address, egais.* FROM egais, object, building WHERE egais.id_object = object.id and object.id_building = building.id and object.open!='0'", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[]  = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectJacartaInObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT object.name, egais.*, building.address FROM egais, object, building WHERE egais.id_object = object.id and building.id = object.id_building and object.id='".$id_object."' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function countJacartaNewVersion ($dbcnx) {
	$query = mysql_query("SELECT count(*) FROM `egais` WHERE `bild_number` LIKE '%2.%' ORDER BY `bild_number` DESC ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['count(*)'];
}

// Общее количество ключей RUTOKEN в магазинах
function countRutoken ($dbcnx) {
	$query = mysql_query("SELECT count(*) FROM `egais` WHERE token='rutoken'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['count(*)'];
}

function add_egais ($dbcnx, $id_object) {
	mysql_query("INSERT INTO `pelican`.`egais` (`id_object`) VALUES ('".$id_object."')", $dbcnx);
}	

function rsa_date ($dbcnx) {
	$query = mysql_query("SELECT DISTINCT(date) FROM `log_rsa_online` WHERE 1 ORDER BY `log_rsa_online`.`date` DESC LIMIT 10", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function rsa_date_offline ($dbcnx, $date) {
	$query = mysql_query("SELECT COUNT(*) FROM `log_rsa_online` WHERE date='".$date."' and online='0'", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['COUNT(*)'];
}

function query_offline_now ($dbcnx) {
	$query = mysql_query("SELECT object.name, object.id, log_rsa_online.ip, building.address FROM object, building, log_rsa_online WHERE log_rsa_online.date=(SELECT date FROM `log_rsa_online` GROUP BY date DESC LIMIT 1) and log_rsa_online.online='0' and object.id=log_rsa_online.id_object and building.id = object.id_building GROUP BY log_rsa_online.id_object ORDER BY object.name ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   ЗАПРАВКА    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function allCartridge ($dbcnx) {
	$query = mysql_query("SELECT * FROM `cartridge`", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[]  = mysql_fetch_assoc($query);
	}
	return $result;
}

function select_month ($dbcnx) {
	$year_today = date("Y"); $month_today = date("m");
	$query = mysql_query("SELECT sum(zapravka.count), cartridge.name FROM zapravka, cartridge WHERE zapravka.date >=  '".$year_today."-".$month_today."-01' and cartridge.id=zapravka.type GROUP BY TYPE ORDER BY sum(zapravka.count) DESC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function select_minus1 ($dbcnx) {
	$year_today = date("Y"); $month_today = date("m"); $month_minus1 = date("m", strtotime('- 1 month'));
	$query = mysql_query("SELECT sum(zapravka.count), cartridge.name FROM zapravka, cartridge WHERE zapravka.date >=  '".$year_today."-".$month_minus1."-01' and zapravka.date < '".$year_today."-".$month_today."-01' and cartridge.id=zapravka.type GROUP BY TYPE ORDER BY sum(zapravka.count) DESC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function select_minus2 ($dbcnx) {
	$month_today = date("m"); $month_minus2 = date("m", strtotime('-2 month')); $month_minus1 = date("m", strtotime('-1 month'));
	$year_today = date("Y");
		if ($month_minus2 > 11) { $year_last = date('Y', strtotime('-1 years')); } 
		else { $year_last = $year_today; }
	$query = mysql_query("SELECT sum(zapravka.count), cartridge.name FROM zapravka, cartridge WHERE  zapravka.date >=  '".$year_last."-".$month_minus2."-01' and zapravka.date < '".$year_today."-".$month_minus1."-01' and cartridge.id=zapravka.type GROUP BY TYPE ORDER BY sum(zapravka.count) DESC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function switcher ($text,$reverse=false) {
  $str[0] = array(
    "й","ц","у","к","е","н","г","ш","щ","з","х","ъ",
    "ф","ы","в","а","п","р","о","л","д","ж","э",
    "я","ч","с","м","и","т","ь","б","ю"
  );
  $str[1]= array(
    "q","w","e","r","t","y","u","i","o","p","[","]",
    "a","s","d","f","g","h","j","k","l",";","'",
    "z","x","c","v","b","n","m",",","."
  );
  $out = array();
  foreach($str[0] as $i=>$key){
    $out[0][$i] =  '#'.str_replace(array('.',']','['),array('\.','\]','\['),  $str[ $reverse ? 0:1][$i]).'#ui';
    $out[1][$i] =  $str[$reverse ? 1:0][$i] ;
  };
  return preg_replace($out[0], $out[1], $text);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   POSTFIXADMIN   //////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// ОБНОВЛЕНИЕ АЛЬЯСА ALL_SHOPS
function add_alias ($dbcnx_pf, $all) {
	$query = "UPDATE `alias` SET `goto`='".$all."' WHERE `address`='all_shops@neo63.ru' ";
	mysql_query($query, $dbcnx_pf);
}

// ОБНОВЛЕНИЕ АЛЬЯСА ALL_SUPERVISORS
function add_alias_sale_otdel ($dbcnx_pf, $all) {
	$query = "UPDATE `alias` SET `goto`='".$all."' WHERE `address`='all_supervisors@neo63.ru' ";
	mysql_query($query, $dbcnx_pf);
}

// ОБНОВЛЕНИЕ АЛЬЯСА ALL_AUDITORS
function add_alias_auditors_otdel ($dbcnx_pf, $all) {
    $query = "UPDATE `alias` SET `goto`='".$all."' WHERE `address`='all_auditors@neo63.ru' ";
    mysql_query($query, $dbcnx_pf);
}

// ОБНОВЛЕНИЕ АЛЬЯСА ALL_SHOPS_TLT
function add_alias_tlt ($dbcnx_pf, $all) {
	$query = "UPDATE `alias` SET `goto`='".$all."' WHERE `address`='all_shops_tlt@neo63.ru' ";
	mysql_query($query, $dbcnx_pf);
}

// ПРОВЕРКА НАЛИЧИЯ ЗАПИСИ В БД С ТЕКУЩИМ ИМЕНЕМ ПОЛЬЗОВАТЕЛЯ
function selectUser ($dbcnx_pf, $username) {
	$query = mysql_query("SELECT `username` FROM `mailbox` WHERE `username`='".$username."' ",$dbcnx_pf);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allUserPA ($dbcnx_pf) {
	$query = mysql_query("SELECT * FROM `mailbox` ",$dbcnx_pf);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function allUserAlias ($dbcnx_pf) {
	$query = mysql_query("SELECT * FROM `alias` WHERE  `name` IS NOT NULL",$dbcnx_pf);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function console_log( $data ){
  echo '<script>';
  echo 'console.log('. json_encode( $data ) .')';
  echo '</script>';
}
?>