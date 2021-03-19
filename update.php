<?php

/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */ // include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

require_once("./function/function_ip_pelican_04.php");

function selectAllShop1 ($dbcnx04p) {
	$query = mysql_query("SELECT shop.group_chk, opener.id_user FROM opener, shop WHERE shop.id=opener.id_mag", $dbcnx04p);
	$n = mysql_num_rows($query);
	$result = array();	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}	
	return $result;
}

$selectAllShop = selectAllShop1 ($dbcnx04p);

$i=1;
foreach ($selectAllShop as $sas) {
	
	
	echo $sas['group_chk']." <br>";
	echo $sas['id_user']." <br>";
	
	
	
	//echo $sas['group_chk'];
	$query = mysql_query("SELECT `id_object` FROM `south_conf` WHERE group_chk='".$sas['group_chk']."' ", $dbcnx);
	$n = mysql_fetch_array($query);
	
	echo $n['id_object']." - id объекта <br>";
	
	if (!empty($n['id_object'])) {
		//mysql_query("INSERT INTO `opener`(`id_domain_user`, `id_object`) VALUES ('".$sas['id_user']."','".$n['id_object']."') ", $dbcnx);
	}
	
	//echo "INSERT INTO `email`(`id_object`, `email`) VALUES ('".$i."','".$sas['email']."')";
	
	/*
	//echo $sas['address_fact']." - адрес <br>";
	preg_match( '/адрес:.*?\d+,\sРФ,\s(.*?$)/siu' , $sas['address_fact'] , $address );
	
	mysql_query("INSERT INTO `building`(`id`, `address`, `area`, `distance`) VALUES (".$i.",'".$address[1]."', '".$sas['gorod']."', '".$sas['distance']."')", $dbcnx);
	mysql_query("INSERT INTO `object`(`id`, `id_building`, `type`, `name`, `date_open`, `open`) VALUES ('".$i."', '".$i."', 'ПЕЛИКАН', '".$sas['name']."', '".$sas['date_open']."', '".$sas['online']."')",$dbcnx);
////////    ЕГАИС    //////////////////////////////	
	echo "<b>------- ЕГАИС -------</b><br>";
	mysql_query("INSERT INTO `egais`(`id_object`, `ip`, `date_key`, `rsa`, `kpp`, `bild_number`) VALUES ('".$i."', '".$sas['ip_key']."', '".$sas['date_key']."','".$sas['rsa']."','".$sas['kpp']."','".$sas['buildNumber']."')", $dbcnx);
////////     ТСД     //////////////////////////////		
	$selectTSD = selectTSD ($dbcnx04, $sas['id']);
	if (isset($selectTSD[0])) { echo "<b>------- ТСД -------</b><br>"; }	
	foreach ($selectTSD as $tsd) {
		mysql_query("INSERT INTO `tsd`(`id_object`, `imei`, `serial`) VALUES ('".$i."', '".$tsd['imei']."', '".$tsd['serial']."')", $dbcnx);	
	}
////////     РАБОЧИЕ СТАНЦИИ     //////////////////////////////		
	$selectWS = selectWS ($dbcnx04, $sas['id']);
	echo "<b>------- РАБОЧИЕ СТАНЦИИ -------</b><br>";
	foreach ($selectWS as $ws) {
		mysql_query("INSERT INTO `ws`(`id_object`, `ip`, `os`, `os_key`, `type`) VALUES ('".$i."','".$ws['ip_ws']."','".$ws['os_ws']."','".$ws['key']."', '0')", $dbcnx);		
	}
////////     КАССЫ     //////////////////////////////		
	$selectWSkas = selectWSkas ($dbcnx04, $sas['id']);
	echo "<b>------- РАБОЧИЕ СТАНЦИИ - КАССЫ -------</b><br>";
	foreach ($selectWSkas as $wskas) {
		mysql_query("INSERT INTO `ws`(`id_object`, `ip`, `os`, `puppet`, `udev`, `type`) VALUES ('".$i."','".$wskas['ip_kas']."','LINUX','".$wskas['puppet']."','".$wskas['udev']."', '1')", $dbcnx);	
	}
////////     ВЕСЫ     //////////////////////////////		
	$selectVes = selectVes ($dbcnx04, $sas['id']);
	echo "<b>------- ВЕСЫ -------</b><br>";
	foreach ($selectVes as $ves) {
		mysql_query("INSERT INTO `ves`(`id_object`, `ip`) VALUES ('".$i."','".$ves['ip_ves']."')", $dbcnx);	
	}	
////////     ИНТЕРНЕТ     //////////////////////////////		
	$selectInternet = selectInternet ($dbcnx04, $sas['id']);
	$shop_mask_select = shop_mask_select ($dbcnx04, $sas['id']);
	echo "<b>------- ИНТЕРНЕТ -------</b><br>";
	foreach ($selectInternet as $int) {
		mysql_query("INSERT INTO `internet`(`id_object`, `id_isp`, `type`, `agreement`, `ext_ip`, `router`, `note`, `mask`) VALUES ('".$i."', '".$int['id_isp']."', '".$int['type_conn']."', '".$int['Num']."', '".$int['ext_ip']."', '".$int['router']."', '".$int['note']."', '".$shop_mask_select[0]['mask']."')", $dbcnx);		
	}
////////     ВИДЕО     //////////////////////////////		
	$selectVideo = selectVideo ($dbcnx04, $sas['id']);
	if (isset($selectVideo[0])) { echo "<b>------- ВИДЕО -------</b><br>"; }
	foreach ($selectVideo as $vid) {
		mysql_query("INSERT INTO `video`(`id_object`, `ip`, `model`, `channel`, `hdd`, `type`, `login`, `pass`) VALUES ('".$i."', '".$vid['ip_video']."', '".$vid['model']."', '".$vid['channel']."', '".$vid['harddrive']."', '".$vid['type']."', 'admin', '".$vid['pass']."')", $dbcnx);
	}				
////////     ПОЛЬЗОВАТЕЛИ     //////////////////////////////		
	$selectUser = selectUser ($dbcnx04, $sas['id']);
	if (isset($selectUser[0])) { echo "<b>------- ПОЛЬЗОВАТЕЛИ -------</b><br>"; }
	foreach ($selectUser as $usr) {
		mysql_query("INSERT INTO `domain_user`(`username`, `access`, `id_object`, `run`) VALUES ('".$usr['username']."', '1', '".$i."', '1')", $dbcnx);
	}	

////////    ПЕЛИКАФЕ    //////////////////////////////		
/*	if ($sas['pcafe'] = '1') {
		echo "INTO `object`(`id_building`, `type`, `name`, `date_open`, `open`) VALUES ('".$i."', 'ПЕЛИКАФЕ', '".$sas['name']."', '".$sas['date_open']."', '".$sas['online']."') <br>";
	}
*/	
	echo "<br>";
	echo "--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------";
	echo "<br><br>"; $i++;
	
}

?>

<? /* FOOTER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); ?>