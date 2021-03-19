<?php
header("Content-Type: text/html; charset=utf-8");
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

include "../config.php";
require_once("../function/function_object.php");

$date_time = date("Y-m-d H:i:s");

if (isset($_GET['rsa_fio'])) { $id = $_GET['rsa_fio']; }
if (isset($_POST['rsa_fio'])) { $id = $_POST['rsa_fio']; }

echo "<b>".$id."</b> id объекта <br>";
$b = selectJacartaInObject ($dbcnx, $id);
echo "<b>".$b['ip']."</b> - ip адресс АТОЛ <br><br>";
if (!empty($b['ip'])) {
	exec('ping -c1 -w1 '.$b['ip'].' > /dev/null && echo 1|| echo 0', $output);
	if ($output[0] == '1') {
		
		mysql_query("INSERT INTO `logos`(`id_object`, `datetime`, `type`) VALUES ('".$id."','".$date_time."', 'RSA UPD - ".$id."')", $dbcnx);
		
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
				//echo $update_log." - update_log - FIO<br>";
				mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('0', '".$date_time."', 'egais', '".$id."', '".$update_log."')", $dbcnx);
			} else {
				//echo $b['fio']." - ФИО ответственного за ключ не изменилась<br>";
			}	
		}
		echo "<br>";
		
		$arr =  array(
						'username' => urlencode('user'), 
						'password' => urlencode('Password_1'),
				); //значения для формы в виде поле => значение	
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://'.$b['ip'].'/settings/login');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, $agent);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $arr);
		$out = curl_exec($curl);
		curl_close($curl);

		preg_match( '/session=(.*?);/sui' , $out , $settings );

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://'.$b['ip'].'/settings/get_device_id');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_COOKIE, "settings-session=".$settings[1]);
		$out = curl_exec($curl);
		
		preg_match( '/кабинете:\s<b>(.*?)</sui' , $out, $identifier); //+
		mysql_query("UPDATE `egais` SET `identifier`='".$identifier[1]."' WHERE `id_object`= '".$id."' ", $dbcnx);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://'.$b['ip'].'/settings/utm_key');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_COOKIE, "settings-session=".$settings[1]);
		$out2 = curl_exec($curl);
		
		preg_match( '@ключи:</label>.*?<p>&nbsp;&nbsp;(\w+)<@sui', $out2, $token); //+
		mysql_query("UPDATE `egais` SET `token`='".$token[1]."' WHERE `id_object`= '".$id."' ", $dbcnx);
		
	} else {
		mysql_query("INSERT INTO `logos`(`id_object`, `datetime`, `type`) VALUES ('".$id."','".$date_time."', 'no ping')", $dbcnx);	
	}	
}

$output = null;