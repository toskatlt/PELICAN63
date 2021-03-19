<?php
header("Content-Type: text/html; charset=utf-8");
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

include "../config.php";
require_once("../function/function_object.php");

$date_time = date("Y-m-d H:i:s");

if (isset($_POST['id'])) { $id = $_POST['id']; }
//if (isset($_GET['id'])) { $id = $_GET['id']; }

//echo "<b>".$id."</b> id объекта <br>";
$b = selectJacartaInObject ($dbcnx, $id);
//echo "<b>".$b['ip']."</b> - ip адресс АТОЛ <br><br>";

if (!empty($b['ip'])) {
	exec('ping -c1 -w1 '.$b['ip'].' > /dev/null && echo 1|| echo 0', $output);
	if ($output[0] == '1') {
		
		mysql_query("INSERT INTO `logos`(`id_object`, `datetime`, `type`) VALUES ('".$id."','".$date_time."', 'RSA UPD - ".$id."')", $dbcnx);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://".$b['ip'].":8080");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		$text = curl_exec($ch);
		curl_close($ch);
		
		
		////////// RSA ///////////////////////////////////////////////////////////////////////
		preg_match('/Сертификат RSA.*?по\s(\d{4}-\d{2}-\d{2})/usi' , $text , $newdate_rsa);
		if (isset($newdate_rsa[1])) {
			if ($b['date_rsa'] != $newdate_rsa[1]) {
				$update_new = "UPDATE `egais` SET `date_rsa`='".$newdate_rsa[1]."' WHERE `id`='".$b['id']."'";
				mysql_query($update_new, $dbcnx);
				$update_log = "DATE ".$b['date_rsa']." -> ".$newdate_rsa[1]." ";
				echo $update_log." - update_log - RSA<br>";
				mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('0', '".$date_time."', 'egais', '".$id."', '".$update_log."')", $dbcnx);
			} else {
				echo $b['date_rsa']." - дата RSA не изменилась<br>";
			}	
		}
		
		echo "<br>";
		////////// ГОСТ ///////////////////////////////////////////////////////////////////////
		preg_match('/Сертификат ГОСТ.*?по\s(\d{4}-\d{2}-\d{2})/usi' , $text , $newdate_gost);
		if (isset($newdate_gost[1])) {
			if ($b['date_gost'] != $newdate_gost[1]) {
				$update_new = "UPDATE `egais` SET `date_gost`='".$newdate_gost[1]."' WHERE `id`='".$b['id']."'";
				mysql_query($update_new, $dbcnx);
				$update_log = "DATE ".$b['date_gost']." -> ".$newdate_gost[1]." ";
				echo $update_log." - update_log - GOST<br>";
				mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('0', '".$date_time."', 'egais', '".$id."', '".$update_log."')", $dbcnx);
			} else {
				echo $b['date_gost']." - дата ГОСТ не изменилась<br>";
			}	
		}
		
		echo "<br>";
		////////// ВЕРСИЯ ПО ///////////////////////////////////////////////////////////////////////
		preg_match('/Версия ПО.*?div class.*?>(.*?)</usi' , $text , $num);
		if ((isset($num[1])) and (!empty($num[1]))) {
			if ($b['bild_number'] != $num[1]) {
				$update_new = "UPDATE `egais` SET `bild_number`='".$num[1]."' WHERE `id`='".$b['id']."'";
				mysql_query($update_new, $dbcnx);
				$update_log = "DATE ".$b['bild_number']." -> ".$num[1]." ";
				echo $update_log." - update_log - Версия<br>";
				mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('0', '".$date_time."', 'egais', '".$id."', '".$update_log."')", $dbcnx);
			} else {
				echo $b['bild_number']." - Версия ПО не изменилась<br>";
			}
		}
		////////// FSRAR ID ///////////////////////////////////////////////////////////////////////
		preg_match( '/class="doc-link">FSRAR-RSA-(.*?)<\/pre>/' , $text , $link);
		if (isset($link[1])) {
			if ($b['rsa'] != $link[1]) {
				mysql_query("UPDATE `egais` SET `rsa`='".$link[1]."' WHERE `id`='".$b['id']."'", $dbcnx);
				$update_log = "RSA ".$b['rsa']." -> ".$link[1]." ";
				echo $update_log." update_log - Номер RSA<br>";
				mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('0', '".$date_time."', 'egais', '".$id."', '".$update_log."')", $dbcnx);
			} else {
				echo $b['rsa']." - номер RSA не изменился<br>";
			}
		}	
		
		echo "<br>";
		
		/*
		////////// FIO ///////////////////////////////////////////////////////////////////////
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://".$b['ip'].":8080/info/certificate/GOST");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		$text = curl_exec($ch);
		curl_close($ch);
		
		preg_match('/SURNAME=(.*?),/usi' , $text , $surname);
		preg_match('/GIVENNAME=(.*?),/usi' , $text , $givename);
		
		if ((isset($surname[1])) and (isset($givename[1]))) {
			$fio = $surname[1]." ".$givename[1];
			$fio = html_entity_decode($fio, ENT_NOQUOTES, 'UTF-8');
			mysql_query("INSERT INTO `egais_fio_all`(`fio`) VALUES ('".$fio."')", $dbcnx);
			if ($b['fio'] != $fio) {
				$update_new = "UPDATE `egais` SET `fio`='".$fio."' WHERE `id`='".$b['id']."'";
				mysql_query($update_new, $dbcnx);
				$update_log = "DATE ".$b['fio']." -> ".$fio." ";
				echo $update_log." - update_log - FIO<br>";
				mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('0', '".$date_time."', 'egais', '".$id."', '".$update_log."')", $dbcnx);
			} else {
				echo $b['fio']." - ФИО ответственного за ключ не изменилась<br>";
			}	
		}
		
		echo "<br>";
		*/
	}
}

$output = null;