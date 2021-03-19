<?php
//include $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$date_time = date("Y-m-d H:i:s");
$timestamp = date('Y-m-d G:i:s');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   МАГАЗИНЫ   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function selectAllShop ($dbcnx04p) {
	$query = "SELECT * FROM shop WHERE online='1'";
	$result = mysql_query($query,$dbcnx04p);	
	
	$n = mysql_num_rows($result);
	$selectAllShop = array();
	for ($i = 0; $i < $n; $i++)
	{
		$row = mysql_fetch_assoc($result);
		$selectAllShop[] = $row;
	}
	return $selectAllShop;
}

function selectWSkas ($dbcnx04p, $id) {
	$query = "SELECT * FROM `ws_kas` WHERE id_mag='".$id."'";
	$result = mysql_query($query,$dbcnx04p);	
	
	$n = mysql_num_rows($result);
	$selectWSkas = array();
	for ($i = 0; $i < $n; $i++)
	{
		$row = mysql_fetch_assoc($result);
		$selectWSkas[] = $row;
	}
	return $selectWSkas;
}

function shop_mask_select ($dbcnx04p, $id) {
	$query = "SELECT SUBSTRING_INDEX(`ip_ws`, '.', 3) as mask FROM `ws` where id_mag='".$id."' limit 1";
	$result = mysql_query($query,$dbcnx04p);
	
	if (!$result)
		die(mysql_error($dbcnx04p));
		
	$n = mysql_num_rows($result);
	$shop_mask_select = array();
	
	for ($i = 0; $i < $n; $i++)
	{
		$row = mysql_fetch_assoc($result);
		$shop_mask_select[] = $row;
	}

	return $shop_mask_select;
}

function selectVideo ($dbcnx04p, $id) {
	$query = "SELECT * FROM  `video` , `videoreg_type` WHERE video.ip_video = videoreg_type.ip and video.id_mag='".$id."'";
	$result = mysql_query($query,$dbcnx04p);	
	
	$n = mysql_num_rows($result);
	$selectVideo = array();
	for ($i = 0; $i < $n; $i++)
	{
		$row = mysql_fetch_assoc($result);
		$selectVideo[] = $row;
	}
	return $selectVideo;
}

function selectUser ($dbcnx04p, $id) {
	$query = "SELECT * FROM `domain_users` WHERE `id_mag`='".$id."'";
	$result = mysql_query($query,$dbcnx04p);	
	
	$n = mysql_num_rows($result);
	$selectUser = array();
	for ($i = 0; $i < $n; $i++)
	{
		$row = mysql_fetch_assoc($result);
		$selectUser[] = $row;
	}
	return $selectUser;
}

?>