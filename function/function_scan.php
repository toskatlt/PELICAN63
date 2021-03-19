<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$date_time = date("Y-m-d H:i:s");
$timestamp = date('Y-m-d G:i:s');

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   СКАНЫ     //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


if (isset($_POST['turn']) && isset($_POST['filename'])) {
	$files = $_POST['filename'];

	$filename_resize = '../scan/resize/'.$files.'.jpg';
	$filename_preview = '../scan/preview/'.$files.'.jpg';
	
	if ($_POST['turn'] == 'right') { $degrees = -90; }
	if ($_POST['turn'] == 'left') { $degrees = 90; }
	
	$source_resize = imagecreatefromjpeg($filename_resize);
	$source_preview = imagecreatefromjpeg($filename_preview);
	$rotate_resize = imagerotate($source_resize, $degrees, 0);
	$rotate_preview = imagerotate($source_preview, $degrees, 0);
	imagejpeg($rotate_resize, '../scan/resize/'.$files.'.jpg');
	imagejpeg($rotate_preview, '../scan/preview/'.$files.'.jpg');			
	imagedestroy($source_resize);
	imagedestroy($rotate_resize);
	imagedestroy($source_preview);
	imagedestroy($rotate_preview);
}

if (isset($_POST['save']) && isset($_POST['filename'])) {
	mysql_query("DELETE FROM `scanned` WHERE `filename`='".$_POST['filename']."'", $dbcnx);				
	$pieces = explode(" ", $_POST['hash']);
	
	$update_log = "SCAN: изменение описание скана ".$_POST['filename']." ";
	mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$_COOKIE['id']."', '".$date_time."', 'scan', '".$_POST['where']."', '".$update_log."')", $dbcnx);
	
	for ($s=0; $s<count($pieces); $s++) {						
		mysql_query("INSERT INTO `scanned`(`filename`, `hash`) VALUES ('".$_POST['filename']."','".$pieces[$s]."')", $dbcnx);
	}
	mysql_query("UPDATE `scan_doc` SET `whet`='".$_POST['count']."',`date`='".$_POST['date']."', `responsible`='".$_POST['who']."', `id_object`='".$_POST['where']."' WHERE `filename`='".$_POST['filename']."'", $dbcnx);
}

if (isset($_POST['del']) && isset($_POST['filename'])) {
	mysql_query("DELETE FROM `scanned` WHERE `filename`='".$_POST['filename']."'", $dbcnx);
	mysql_query("DELETE FROM `scan_doc` WHERE `filename`='".$_POST['filename']."'", $dbcnx);
}

function AllScanAdmin ($dbcnx) {
	$query = mysql_query("SELECT `id`, SUBSTRING_INDEX(`fio`, ' ', 2) as `fio`,`username`,`phone`,`id_position` FROM domain_user WHERE id_position IN ('1','2','14','67') and access > 7 ORDER BY fio ASC", $dbcnx);
	$n = mysql_num_rows($query);
	$result = array();
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($query);
		$result[] = $row;
	}
	return $result;
}

function scan_all_count ($dbcnx) {
	$query = mysql_query("SELECT COUNT(DISTINCT filename) AS filename FROM `scan_doc`", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result['filename'];
}

function last_date_scan ($dbcnx) {
	$query = mysql_query("SELECT date FROM `scan_doc` WHERE date != '0000-00-00' limit 1", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function top10_hash($dbcnx) {
	$query = mysql_query("SELECT DISTINCT COUNT( hash ), LOWER( hash ) FROM `scanned` WHERE `hash` != ' ' GROUP BY hash ORDER BY COUNT( hash ) DESC LIMIT 20", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectAllPositionType($dbcnx) {
	$query = mysql_query("SELECT * FROM `position_type` ORDER BY `position_type`.`id` ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectAllPositionModel($dbcnx) {
	$query = mysql_query("SELECT * FROM `position_model` ORDER BY `position_model`.`id` ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scan_all ($dbcnx, $start, $per_page) {
	//$query = mysql_query("SELECT scan_doc.filename, scanned.hash, scan_doc.date FROM scanned, scan_doc WHERE scan_doc.filename=scanned.filename ORDER BY scan_doc.id DESC LIMIT ".$start.",".$per_page, $dbcnx);
	$query = mysql_query("SELECT scan_doc.id, scan_doc.filename, scan_doc.date FROM scan_doc ORDER BY scan_doc.id DESC LIMIT ".$start.",".$per_page, $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scan_find_hash ($dbcnx, $find, $start, $per_page) {
	$query = "SELECT scan_doc.id, scan_doc.filename, scanned.hash, scan_doc.date FROM scanned, scan_doc  WHERE scanned.hash LIKE '%".$find."%' and scan_doc.filename=scanned.filename LIMIT ".$start.",".$per_page;
	$result = mysql_query($query,$dbcnx);
	$n = mysql_num_rows($result);
	for ($i = 0; $i < $n; $i++) {
		$scan_find_hash[] = mysql_fetch_assoc($result);
	}	
	return $scan_find_hash;
}

function scan_find_object ($dbcnx, $id_object, $start, $per_page) {
	$query = mysql_query("SELECT * FROM `scan_doc` WHERE `id_object`='".$id_object."' ORDER BY `date` DESC LIMIT ".$start.",".$per_page, $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scan_find_admin ($dbcnx, $responsible, $start, $per_page) {
	$query = mysql_query("SELECT * FROM `scan_doc` WHERE `responsible`='".$responsible."' LIMIT ".$start.",".$per_page, $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scan_find_admin_count ($dbcnx, $responsible) {
	$query = mysql_query("SELECT count(*) FROM `scan_doc` WHERE `responsible`='".$responsible."'", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['count(*)'];
}

function scan_find_object_count ($dbcnx, $id_object) {
	$query = mysql_query("SELECT count(*) FROM `scan_doc` WHERE `id_object`='".$id_object."'", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['count(*)'];
}

function scanObjectCount ($dbcnx, $id_object) {
	$query = mysql_query("SELECT count(*) FROM scan_doc, scan_sklad WHERE scan_sklad.id = scan_doc.id_object and scan_sklad.id_object='".$id_object."'", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['count(*)'];
}

function scanObjectAktCount ($dbcnx, $id_object) {
	$query = mysql_query("SELECT count(*) FROM scan_doc, scan_sklad, scanned WHERE scan_sklad.id = scan_doc.id_object and scan_doc.filename=scanned.filename and scan_sklad.id_object='".$id_object."' and scanned.hash LIKE '%Акт%'", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['count(*)'];
}

function scanAllselectObject ($dbcnx, $id_object) {
	$query = mysql_query("SELECT * FROM scan_doc, scan_sklad WHERE scan_sklad.id = scan_doc.id_object and scan_sklad.id_object='".$id_object."'", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scan_find_count ($dbcnx, $find) {
	$query = "SELECT count(filename) FROM scanned  WHERE hash LIKE '%".$find."%' ";	
	$result = mysql_query($query,$dbcnx);
	$n = mysql_num_rows($result);	
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$scan_find_count[] = $row;
	}	
	return $scan_find_count;
}

function scan_hash ($dbcnx, $filename) {
	$query = mysql_query("SELECT group_concat(distinct `hash`) AS `hash` FROM scanned WHERE `filename`='".$filename."' ", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scan_hash_date ($dbcnx, $filename) {
	$query = mysql_query("SELECT `date`,`whet`,`responsible`,`id_object` from scan_doc WHERE `filename`='".$filename."' ", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function whet ($dbcnx, $filename) {
	$query = mysql_query("SELECT `whet` FROM `scan_doc` WHERE `filename`='".$filename."' ", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function admin_user_id ($dbcnx) {
	$query = mysql_query("SELECT `id`, SUBSTRING_INDEX(`fio`, ' ', 2) as `fio` FROM domain_user WHERE id_position='1' and access > 7 ORDER BY fio ASC", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scan_find_hash_post ($dbcnx, $where, $adminID) {
	if ($adminID > '0') {
		$query = mysql_query("SELECT count(scanned.filename), scan_doc.id, scan_doc.filename, scanned.hash, scan_doc.date FROM scanned, scan_doc WHERE scan_doc.responsible = '".$adminID."' and ".$where." GROUP BY scanned.filename", $dbcnx);
	} else {	
		$query = mysql_query("SELECT count(scanned.filename), scan_doc.id, scan_doc.filename, scanned.hash, scan_doc.date FROM scanned, scan_doc WHERE ".$where." GROUP BY scanned.filename", $dbcnx);
	}
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scan_find_data_post ($dbcnx, $where, $start, $per_page) {	
	$query = mysql_query("SELECT count(scanned.filename), scan_doc.id, scan_doc.filename, scanned.hash, scan_doc.date FROM scanned, scan_doc WHERE ".$where." GROUP BY scanned.filename LIMIT ".$start.",".$per_page."", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scan_find_data_post_count ($dbcnx, $start, $end) {
	$query = mysql_query("SELECT COUNT(*) FROM scan_doc WHERE scan_doc.date >= '".$start."' and scan_doc.date <= '".$end."' ", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scan_find_hash_post_count ($dbcnx, $where) {
	$query = "SELECT COUNT(scanned.filename) FROM scanned, scan_doc WHERE ".$where;
	$result = mysql_query($query, $dbcnx);
	$n = mysql_num_rows($result);
	for ($i = 0; $i < $n; $i++) {
		$row = mysql_fetch_assoc($result);
		$scan_find_hash_post_count[] = $row;
	}
	return $scan_find_hash_post_count;
}

function scan_publicat ($dbcnx, $filename) {
	$query = mysql_query("SELECT * FROM `scan_doc` WHERE `oldname`='".$filename."' ", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectExtensionScanDoc ($dbcnx, $filename) {
	$query = mysql_query("SELECT SUBSTRING_INDEX(`oldname`, '.', -1) as ext FROM `scan_doc` WHERE `filename`='".$filename."' ", $dbcnx);
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// ВЫЗОВ ВСЕГО СПИСКА СКЛАДОВ КУДА СИСТЕМНАЯ СЛУЖБА МОГЛА ПЕРЕДАТЬ ИЛИ ЗАБРАТЬ ОБОРУДОВАНИЕ
function scan_sklad ($dbcnx) {
	$query = mysql_query("SELECT `id`, `sklad` FROM `scan_sklad` ORDER BY `sklad` ASC ", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function scanNotWhet ($dbcnx) {
	$query = mysql_query("SELECT count(*) as count FROM `scan_doc` WHERE `whet` = '0'", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['count'];
}

function lastScanId ($dbcnx) {
	$query = mysql_query("SELECT id FROM scan_doc ORDER BY id DESC LIMIT 1", $dbcnx);
	$row = mysql_fetch_assoc($query);
	return $row['id'];
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////