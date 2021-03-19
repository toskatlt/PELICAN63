<?php
//include $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$timestamp = date('Y-m-d G:i:s');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   ФАСАД IMG   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function insertFasadImg ($dbcnx, $date, $email, $filename, $server) {
	$query = "SELECT id_object FROM `email` WHERE `email` LIKE '".$email."'";
	$result = mysql_query($query, $dbcnx);	
	$row = mysql_fetch_assoc($result);
	if ($row['id_object'] > 0) {
		$insert = "INSERT INTO `fasad_img`(`id_object`, `email`, `date`, `filename`, `server`) VALUES ('".$row['id_object']."','".$email."','".$date."','".$filename."','".$server."')";
		mysql_query($insert, $dbcnx);
	}
}
// количество фотографий обьекта в бд 
function countAllFasadImg ($dbcnx, $id_object) {
	$query = "SELECT count(*) FROM fasad_img WHERE id_object='".$countAllFasadImg."'";
	$result = mysql_query($query,$dbcnx);	
	if (!$result)
		die(mysql_error($dbcnx));	
	$row = mysql_fetch_assoc($result);
	$countAllFasadImg = $row['count(*)'];	
	return $countAllFasadImg;
}



?>