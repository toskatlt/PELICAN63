<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

?>
<style>
p.hometown { 
    font-weight: bolt;
	font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;
	font-size: 14px;
	color: #666;
	text-align: left;
}

</style>
<?php

//var_dump($_POST);

$date_time = date("y-m-d H:i:s");
if (isset($_GET['id'])) $id_object = $_GET['id'];
if (isset($_GET['e'])) $etype = $_GET['e'];

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	$uname = $userdata['id'];	
//////   0   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if (isset($_POST['submit0'])) { // ИЗМЕНЕНИЕ ДАННЫХ ДИРЕКТОРА В МАГАЗИНЕ
		$idObject = $idObject_get;
		$pel_adm = pel_adm ($dbcnx, $idObject);
			
		$dir_fio = $pel_adm[0];
		$dir_tel = $pel_adm[1];
		
		$arr=array();
		$arr=$_POST['dir'];
		
		if ((isset($dir_fio)) and (isset($dir_tel))) {
			if ($dir_fio != $arr[0]) {
			$update_log = "PEL_ADM:FIO ".$dir_fio." -> ".$arr[0]." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'pel_adm', '".$idObject."', '".$update_log."')";
			mysql_query($log,$dbcnx);
			}
			if ($dir_tel != $arr[1]) {
			$update_log = "PEL_ADM:TEL ".$dir_tel." -> ".$arr[1]." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'pel_adm', '".$idObject."', '".$update_log."')";
			mysql_query($log,$dbcnx);
			}
			// ИЗМИНЕНИЕ ДАННЫХ ДИРЕКТОРА В ТАБЛИЦЕ 'pel_adm'
			$update = "UPDATE `pel_adm` SET `fio`='".$arr[0]."',`telephone`='".$arr[1]."' WHERE `id_mag`='".$idObject."' ";
			mysql_query($update,$dbcnx);
		}
		else {
			if (isset($arr[0])) {
			$update_log = "PEL_ADM:FIO Добавлено: ".$arr[0]." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'pel_adm', '".$idObject."', '".$update_log."')";
			mysql_query($log,$dbcnx);
			}
			else {
				$arr[0]=0;
			}
			if (isset($arr[1])) {
			$update_log = "PEL_ADM:TEL Добавлено: ".$arr[1]." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'pel_adm', '".$idObject."', '".$update_log."')";
			mysql_query($log,$dbcnx);
			}
			else {
				$arr[1]=0;
			}
			$insert = "INSERT INTO `pel_adm`(`fio`, `telephone`, `id_mag`) VALUES ('".$arr[0]."','".$arr[1]."','".$idObject."')";
			mysql_query($insert,$dbcnx);
		}	
	}		
//////  end  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}
	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['id_group'] == "1") {	
		$selectObject = selectObject ($dbcnx, $id_object); $nameObject = $selectObject['name'];
		echo "<br><br><br><br><div class='card_body'>";
			
		echo "<div class='link'>/"; 
		if ($selectObject['type'] == 'МИНДАЛЬ') {				
			echo "<a href='ip_mindal' title='Магазины'> Магазины </a>";
		} elseif (($selectObject['type'] == 'ОФИС') or ($selectObject['type'] == 'СКЛАД')) {				
			echo "<a href='ip_office' title='Магазины'> Офис </a>";		
		} else {
			echo "<a href='ip_pelican' title='Магазины'> Магазины </a>";
		} 
		
		echo "/ <a href='card.php?id=".$id_object."' title='Пеликан'>".$nameObject."</a> / Редактирование</div> <div class='card'>";
		
		echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
		echo "<center><b>Редактирование данных магазина ".$nameObject."</b></center><br><p>";
		echo "<form name='shop' method='POST' action='editcard?id=".$id_object."&e=".$etype."'>";
		echo "<table>";	
//////   1   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 1) {
			//var_dump($_POST);
			//var_dump($selectObject);
			if (isset($_POST['submit'])) { // ИЗМЕНЕНИЕ ДАННЫХ МАГАЗИНА		
				function logging($dbcnx, $uname, $date_time, $table, $id_object, $update_log) {
					mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', '".$table."', '".$id_object."', '".$update_log."')", $dbcnx);
				}
				if ($_POST['name'] != $nameObject) {
					$table = 'object';
					$update_log = "NAME: ".$nameObject." -> ".$_POST['name']." ";
					logging ($dbcnx, $userdata['id'], $date_time, $table, $id_object, $update_log);
					mysql_query("UPDATE `object` SET `name`='".$_POST['name']."' WHERE `id`='".$selectObject['id']."'", $dbcnx);
				}
				if ($_POST['address'] != $selectObject['address']) {
					$table = 'building';
					$update_log = "ADDRESS: ".$selectObject['address']." -> ".$_POST['address']." ";
					logging ($dbcnx, $userdata['id'], $date_time, $table, $id_object, $update_log);
					mysql_query("UPDATE `building` SET `address`='".$_POST['address']."' WHERE `id`='".$selectObject['id_building']."'", $dbcnx);
				}
				if ($_POST['distance'] != $selectObject['distance']) {
					$table = 'object';
					$update_log = "DISTANCE: ".$selectObject['distance']." -> ".$_POST['distance']." ";
					logging ($dbcnx, $userdata['id'], $date_time, $table, $id_object, $update_log);
					mysql_query("UPDATE `building` SET `distance`='".$_POST['distance']."' WHERE `id`='".$selectObject['id_building']."'", $dbcnx);
				}
				
				$selectSouthConfInObject = selectSouthConfInObject ($dbcnx, $id_object);
				if ($_POST['comm'] != $selectSouthConfInObject['COMM']) {
					$table = 'south_conf';
					$update_log = "COMM: ".$selectSouthConfInObject['COMM']." -> ".$_POST['comm']." ";
					logging ($dbcnx, $userdata['id'], $date_time, $table, $id_object, $update_log);
					mysql_query("UPDATE `south_conf` SET `COMM`='".$_POST['comm']."' WHERE `id_object`='".$selectObject['id']."'", $dbcnx);
				}
				if ($_POST['group_chk'] != $selectSouthConfInObject['group_chk']) {
					$table = 'south_conf';
					$update_log = "GROUP_CHK: ".$selectSouthConfInObject['group_chk']." -> ".$_POST['group_chk']." ";
					logging ($dbcnx, $userdata['id'], $date_time, $table, $id_object, $update_log);
					mysql_query("INSERT INTO `south_conf` (`group_chk`, `id_object`, `omIn`, `omOut`) VALUES ('".$_POST['group_chk']."', '".$selectObject['id']."', '".$_POST['omIn']."', '".$_POST['omOut']."') ON DUPLICATE KEY UPDATE `group_chk`='".$_POST['group_chk']."'", $dbcnx);
				}
				if ($_POST['omIn'] != $selectSouthConfInObject['omIn']) {
					$table = 'south_conf';
					$update_log = "OM_IN: ".$selectSouthConfInObject['omIn']." -> ".$_POST['omIn']." ";
					logging ($dbcnx, $userdata['id'], $date_time, $table, $id_object, $update_log);
					mysql_query("UPDATE `south_conf` SET `omIn`='".$_POST['omIn']."' WHERE `id_object`='".$selectObject['id']."'", $dbcnx);
				}
				if ($_POST['omOut'] != $selectSouthConfInObject['omOut']) {
					$table = 'south_conf';
					$update_log = "OM_OUT: ".$selectSouthConfInObject['omOut']." -> ".$_POST['omOut']." ";
					logging ($dbcnx, $userdata['id'], $date_time, $table, $id_object, $update_log);
					mysql_query("UPDATE `south_conf` SET `omOut`='".$_POST['omOut']."' WHERE `id_object`='".$selectObject['id']."'", $dbcnx);
				}
				
				
				
////////////////////////////////////////////////////////////////////////////////////////////////	
		
				$selectJacartaInObject = selectJacartaInObject ($dbcnx, $id_object);
				if ($_POST['rsa'] != $selectSouthConfInObject['rsa']) {
					$table = 'egais';
					$update_log = "RSA: ".$selectSouthConfInObject['rsa']." -> ".$_POST['rsa']." ";
					logging ($dbcnx, $userdata['id'], $date_time, $table, $id_object, $update_log);
					mysql_query("INSERT INTO `egais` (`rsa`, `id_object`) VALUES ('".$_POST['rsa']."', '".$selectObject['id']."') ON DUPLICATE KEY UPDATE `rsa`='".$_POST['rsa']."'", $dbcnx);
				}
				if ($_POST['kpp'] != $selectSouthConfInObject['kpp']) {
					$table = 'egais';
					$update_log = "KPP: ".$selectSouthConfInObject['kpp']." -> ".$_POST['kpp']." ";
					logging ($dbcnx, $userdata['id'], $date_time, $table, $id_object, $update_log);
					mysql_query("INSERT INTO `egais` (`kpp`, `id_object`) VALUES ('".$_POST['kpp']."', '".$selectObject['id']."') ON DUPLICATE KEY UPDATE `kpp`='".$_POST['kpp']."'", $dbcnx);
				}
			
///////////////////////////////////////////////////////////////////////////////////////////////////
				

				if (!empty($_POST['kod1c'])) {
					mysql_query("INSERT INTO `kod1c` (`kod`, `id_object`) VALUES ('".$_POST['kod1c']."', '".$selectObject['id']."') ON DUPLICATE KEY UPDATE `kod`='".$_POST['kod1c']."'", $dbcnx);
					//echo "INSERT INTO `kod1c` (`kod`, `id_object`) VALUES ('".$_POST['kod1c']."', '".$selectObject['id']."') ON DUPLICATE KEY UPDATE `kod`='".$_POST['kod1c']."'<br>"; 
				}
			}
			$selectObject = selectObject ($dbcnx, $id_object); $nameObject = $selectObject['name'];
			echo "<tr><th class='input_edit'>Имя магазина: </th><th><input name='name' type='text' size='35' value='".$nameObject."'></th></tr>";
			echo "<tr><th class='input_edit'>Адрес: </th><th><input name='address' type='text' size='35' value='".$selectObject['address']."'></th></tr>";
			$emailInObject = emailInObject ($dbcnx, $id_object); $i=1;
			foreach ($emailInObject as $a) {
				echo "<tr><th class='input_edit'>Электронный адрес";
				if (count($emailInObject) > 1) echo " ".$i++;
				echo ": </th><th style='text-rendering: auto;color: initial;letter-spacing: normal;word-spacing: normal;text-transform: none;text-indent: 0px;text-shadow: none;display: inline-block;text-align: start;margin: 0em;font: 13.3333px Arial;'>".$a['email']."</th></tr>";
			}
			$phoneInObject = phoneInObject ($dbcnx, $id_object); $i=1;
			foreach ($phoneInObject as $a) {
				echo "<tr><th class='input_edit'>Телефонный номер";
				if (count($phoneInObject) > 1) echo " ".$i++;
				echo ": </th><th style='text-rendering: auto;color: initial;letter-spacing: normal;word-spacing: normal;text-transform: none;text-indent: 0px;text-shadow: none;display: inline-block;text-align: start;margin: 0em;font: 13.3333px Arial;'>".$a['number']."</th></tr>";
			}
			echo "<tr><th class='input_edit'>Удаленность: </th><th><input name='distance' type='text' size='35' value='".$selectObject['distance']."'></th></tr>";
			$selectSouthConfInObject = selectSouthConfInObject ($dbcnx, $id_object);
			echo "<tr><th class='input_edit'>COMM: </th><th><input name='comm' type='text' size='35' value='".$selectSouthConfInObject['COMM']."'></th></tr>";
			echo "<tr><th class='input_edit'>Группа чеков: </th><th><input name='group_chk' type='text' size='35' value='".$selectSouthConfInObject['group_chk']."'></th></tr>";
			$selectCod1C = selectCod1C ($dbcnx, $id_object);
			echo "<tr><th class='input_edit'>Код в 1С: </th><th><input name='kod1c' type='text' size='35' value='".$selectCod1C['kod']."'></th></tr>";
			
			echo "<tr><th class='input_edit'>Начало работы: </th><th><input name='omIn' type='time' size='35' value='".$selectSouthConfInObject['omIn']."'></th></tr>";
			echo "<tr><th class='input_edit'>Конец работы: </th><th><input name='omOut' type='time' size='35' value='".$selectSouthConfInObject['omOut']."'></th></tr>";
			
			$selectJacartaInObject = selectJacartaInObject ($dbcnx, $id_object);

			echo "<tr><th class='input_edit' colspan='2' style='height: 50px;'><center><b>ЕГАИС</b> ";
			if ($selectJacartaInObject['rub'] == 1) {
				echo "<input type='checkbox' checked>";
			} 
			elseif ($selectJacartaInObject['rub'] == 0) {
				echo "<input type='checkbox'>";	
			}
			
			echo "</center></th></tr>";
			
			if (empty($selectJacartaInObject)) { add_egais ($dbcnx, $id_object); }
			if (count($selectJacartaInObject['rsa']) < 1) { $selectJacartaInObject['rsa'] = ''; }
			if (count($selectJacartaInObject['kpp']) < 1) { $selectJacartaInObject['kpp'] = ''; }
			
			echo "<tr><th class='input_edit'>Номер RSA: </th><th><input name='rsa' type='text' size='35' value='".$selectJacartaInObject['rsa']."'></th></tr>";
			echo "<tr><th class='input_edit'>Номер КПП: </th><th><input name='kpp' type='text' size='35' value='".$selectJacartaInObject['kpp']."'></th></tr>";
			echo "</table>";
			echo "<br>";
			echo "<p><input type='submit' name='submit' value='Применить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=14' value='Добавить эл.адрес'/> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=15' value='Добавить тел.номер'/></p>";
			echo "</form>";
		}
//////   2 +  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 2) {
			$result = mysql_num_rows(mysql_query("SELECT * FROM `internet` WHERE `id_object`='".$id_object."'", $dbcnx));
			if ($result < 1) {
				mysql_query("INSERT INTO `internet`(`id_object`) VALUES ('".$id_object."')", $dbcnx);
			}
			if (isset($_POST['submit2'])) { // ИЗМЕНЕНИЕ ДАННЫХ ИНТЕРНЕТ СОЕДИНЕНИЯ
				$myip = $_POST['ext_ip']; 
				$arr_myip = explode (".", $myip);			
				
				if ((!isset($myip)) and (($arr_myip[0] > 255)  or ($arr_myip[0] == 0) or ($arr_myip[1] > 255) or ($arr_myip[2] > 255) or ($arr_myip[3] > 255))) { 
					echo "<br><br><br><br>";
					echo "<center>Что за херню ты ввел? Разве это IP адрес? <b>".$myip[0]."</b>!</center>";
				} else {
					$result = selectInternetInObject ($dbcnx, $id_object);
					$type_conn = $result['type'];
					$agreement = $result['agreement'];
					$contract = $result['contract'];
					$ext_ip = $result['ext_ip'];
					$id_isp = $result['id_isp'];
					$note = $result['note'];

					if ($_POST['ext_ip'] != $ext_ip) {
						$update_log = "EXT_IP ".$ext_ip." -> ".$_POST['ext_ip']." ";
						$log = "INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'internet', '".$id_object."', '".$update_log."')";
						mysql_query($log,$dbcnx);
					}
					if ($_POST['agreement'] != $agreement) {
						$update_log = "AGREEMENT ".$agreement." -> ".$_POST['agreement']." ";
						$log = "INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'internet', '".$id_object."', '".$update_log."')";
						mysql_query($log,$dbcnx);
					}
					if ($_POST['contract'] != $contract) {
						$update_log = "CONTRACT ".$contract." -> ".$_POST['contract']." ";
						$log = "INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'internet', '".$id_object."', '".$update_log."')";
						mysql_query($log,$dbcnx);
					}
					if ($_POST['providers'] != $id_isp) {
						$update_log = "ID_ISP ".$isp." -> ".$_POST['providers']." ";
						$log = "INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'internet', '".$id_object."', '".$update_log."')";
						mysql_query($log,$dbcnx);
					}
					if ($_POST['type_conn'] != $type_conn) {
						$update_log = "TYPE_CONN ".$type_conn." -> ".$_POST['type_conn']." ";
						$log = "INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'internet', '".$id_object."', '".$update_log."')";
						mysql_query($log,$dbcnx);
					}
					if ($_POST['note'] != $note) {
						$update_log = "NOTE ".$note." -> ".$_POST['note']." ";
						$log = "INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'internet', '".$id_object."', '".$update_log."')";
						mysql_query($log,$dbcnx);
					}

					mysql_query("UPDATE `internet` SET `id_isp`='".$_POST['providers']."', `type`='".$_POST['type_conn']."', `agreement`='".$_POST['agreement']."', `contract`='".$_POST['contract']."', `ext_ip`='".$_POST['ext_ip']."', `mask`='".$_POST['mask']."', `note`='".$_POST['note']."' WHERE `id`='".$_POST['id']."'", $dbcnx);
				}
			}
			$selectInternetInObject = selectInternetInObject ($dbcnx, $id_object);
			//$selectProviders = selectProviders ($dbcnx, $selectInternetInObject['type']);
			if (isset($selectInternetInObject)) {	
				$type_conn = $selectInternetInObject['type'];			
				echo "<input name='id' type='hidden' size='2' value='".$selectInternetInObject['id']."'>";
				echo "<tr><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>IP роутера: </th><th style='text-align:left;'><input name='ext_ip' type='text' size='15' value='".$selectInternetInObject['ext_ip']."' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}'></th></tr>";
				echo "<tr><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>Внутренняя подсеть: </th><th style='text-align:left;'><input name='mask' type='text' size='15' value='".$selectInternetInObject['mask']."' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}' placeholder='192.168.4'></th></tr>";
				echo "<tr><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>Номер подключения: </th><th style='text-align:left;'><input name='agreement' type='text' size='15' value='".$selectInternetInObject['agreement']."'></th></tr>";
				echo "<tr><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>Номер договора: </th><th style='text-align:left;'><input name='contract' type='text' size='15' value='".$selectInternetInObject['contract']."'></th></tr>";
				$allProviders = allProviders ($dbcnx);		
				echo "<tr><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>Провайдер: </th><th style='text-align:left;'>";
				echo "<select size='1' name='providers'>";
				if ($selectInternetInObject['id_isp'] == '0') echo "<option value='0' SELECTED> </option>";
					foreach ($allProviders as $i) {
						if ($selectInternetInObject['id_isp'] == $i['id']) echo "<option value='".$i['id']."' SELECTED>".$i['name']."</option>";
						else echo "<option value='".$i['id']."'>".$i['name']."</option>";
					}
				echo "</select>";
				echo "<tr><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>Тип подключения: </th><th style='text-align:left;'>";
				echo "<select size='1' name='type_conn'>";
				$isp = ['ADSL','ETHERNET','3G','4G'];
				if (!isset($type_conn) and ($type_conn != '0')) echo "<option value='0' SELECTED> </option>";
				foreach ($isp as $i) {
					if ($type_conn == $i) echo "<option value='".$type_conn."' SELECTED>".$type_conn."</option>";
					else echo "<option value='".$i."'>".$i."</option>";
				}
				echo "</select>";	
				echo "</th></tr>";
				echo "<tr><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>Заметка: </th><th>
					<textarea name='note' cols='40' rows='3'>";
				if ($selectInternetInObject['note'] != '0') echo $selectInternetInObject['note'];
				echo "</textarea></th></tr>";
			}
			echo "</tr>";	
			echo "</table>";		
			echo "<p><a href='card.php?id=".$id_object."'><button> Назад </button></a> <input type='submit' name='submit".$etype."' value='Применить'>";
			if (!isset($selectInternetInObject['ext_ip'])) echo "<input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/>";
			echo "</p>";
			echo "</form>";
		}	
//////   3 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 3) {
			if (isset($_POST['submit3'])) { // ИЗМЕНЕНИЕ ДАННЫХ РАБОЧИЙ СТАНЦИЙ В МАГАЗИНЕ	
				for ($i=0;$i<count($_POST['ip']);$i++) {	 
					$arr_ip = explode (".", $_POST['ip'][$i]);	
					if (($arr_ip[3] < 1) and ($arr_ip[3] > 255)) {
						echo "<br><br><br><br>";
						echo "<center><b>Разве IP адреса вида [ '".$_POST['ip'][$i]."' ] допустимы конституцией РФ?</b></center><br>";
					} else {
						$selectWs = selectWs ($dbcnx, $_POST['id'][$i]);
						if ($selectWs['ip'] != $_POST['ip'][$i]) {
							$update_log = "IP ".$selectWs['ip']." -> ".$_POST['ip'][$i]." ";
							mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."','".$date_time."','ws','".$id_object."','".$update_log."')",$dbcnx);		
						}
						if ($selectWs['os'] != $_POST['os'][$i]) {
							$update_log = "OS ".$selectWs['os']." -> ".$_POST['os'][$i]." ";
							mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."','".$date_time."','ws','".$id_object."','".$update_log."')",$dbcnx);
						}
						if ($selectWs['title'] != $_POST['title'][$i]) {
							$update_log = "TITLE ".$selectWs['title']." -> ".$_POST['title'][$i]." ";
							mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."','".$date_time."','ws','".$id_object."','".$update_log."')",$dbcnx);
						}
						mysql_query("UPDATE `ws` SET `ip`='".$_POST['ip'][$i]."', `os`='".$_POST['os'][$i]."', `title`='".$_POST['title'][$i]."'  WHERE `id`='".$selectWs['id']."'", $dbcnx);
						//echo "UPDATE `ws` SET `ip`='".$_POST['ip'][$i]."', `os`='".$_POST['os'][$i]."'  WHERE `id`='".$selectWs['id']."'<br>";
					}	
				}
			}			
			$i=1; $wsInObject = wsInObject ($dbcnx, $id_object);
			if (count($wsInObject) > 0) {	
				foreach ($wsInObject as $a) {
					echo "<b>ПК [".$i++."]: </b><input name='ip[]' type='text' size='16' value='".$a['ip']."' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}'> ";
					echo "<input name='id[]' type='hidden' size='2' value='".$a['id']."'>";
					echo "&nbsp;<select size='1' name='os[]' style='height:21px;'>";
						echo "<option value='' selected></option>";
					$wsAllOS = wsAllOS ($dbcnx);
					foreach ($wsAllOS as $w) {
						if ($w['os'] == $a['os']) {
							echo "<option value='".$w['os']."' selected>".$w['os']."</option>";
						} else { 
							echo "<option value='".$w['os']."'>".$w['os']."</option>"; 
						}	
					}
					echo "</select>";
					echo "&nbsp;<input name='title[]' type='text' size='16' value='".$a['title']."' placeholder='описание'>";
					echo "<br><br>";	
				}
			} else { echo "на объекте нет рабочих станций <br><br>"; }	
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Применить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   4 +  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 4) {
			if (isset($_POST['submit4'])) { // ИЗМЕНЕНИЕ ДАННЫХ КАСС В МАГАЗИНЕ
				for ($i=0; $i<count($_POST['id']); $i++) {				
					$arr_myip = explode (".", $_POST['ip'][$i]);
					if ($arr_myip[2] > 255){
						echo "<br><br><br><br>";
						echo "<center><b>Разве IP адреса вида [ '".$_POST['ip'][$i]."' ] допустимы конституцией РФ?</b></center><br>";
					} else {
						$selectWs = selectWs ($dbcnx, $_POST['id'][$i]);			
						if ($selectWs['ip'] != $_POST['ip'][$i]) {
							$update_log = "IP ".$selectWs['ip']." -> ".$_POST['ip'][$i]." ";
							mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."','".$date_time."','ws','".$id_object."','".$update_log."')",$dbcnx);					
							mysql_query("UPDATE `ws` SET `ip`='".$_POST['ip'][$i]."' WHERE `id`='".$_POST['id'][$i]."'",$dbcnx);
						}
					}	
				}		
			}		
			$selectWsInObject = selectWsInObject ($dbcnx, $id_object, 1); $i = 1;
			if (count($selectWsInObject) > 0) {
				foreach ($selectWsInObject as $a) {
					echo "<input name='id[]' type='hidden' size='2' value='".$a['id']."'>";
					echo "Касса [".$i++."]: <input name='ip[]' type='text' size='20' value='".$a['ip']."' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}'><br><br>";
				}
			} else { echo "на объекте нет касс <br><br>"; }	
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Применить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   5 +  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 5) {
			if (isset($_POST['submit5'])) {
				for ($i=0; $i<count($_POST['id']); $i++){
					$arr_ip = explode (".", $_POST['ip'][$i]);	
					if (($arr_ip[3] < 1) and ($arr_ip[3] > 255)) {
						echo "<br><br><br><br>";
						echo "<center><b>Разве IP адреса вида [ '".$arr[$i+1]."' ] допустимы конституцией РФ?</b></center>";
					} else {
						$selectVes = selectVes ($dbcnx, $_POST['id'][$i]);
						if ($selectVes['ip'] != $_POST['ip'][$i]) {
							$update_log = "VES ".$selectVes['ip']." -> ".$_POST['ip'][$i]." ";
							mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."','".$date_time."','ves','".$id_object."','".$update_log."')", $dbcnx);	
							mysql_query("UPDATE `ves` SET `ip`='".$_POST['ip'][$i]."' WHERE `id`='".$_POST['id'][$i]."' ", $dbcnx);
						}
					}	
				}
			}		
			$vesInObject = vesInObject($dbcnx, $id_object); $i = 1;
			foreach ($vesInObject as $a) {
				echo "<input name='id[]' type='hidden' size='2' value='".$a['id']."'>";
				echo "Весы [".$i++."]: <input name='ip[]' type='text' size='20' value='".$a['ip']."' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}'><br><br>";	
			}
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Применить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}	
//////   7   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 7) {
			if (isset($_POST['submit7'])) { // ИЗМЕНЕНИЕ ДАННЫХ ШТРИХ ЭТИКЕТОК В МАГАЗИНЕ
				echo "<br>";
				for ($i=0; $i<count($_POST['shtrih']); $i++){
					$selectShtrich = selectShtrich ($dbcnx, $_POST['username'][$i]);
					if (($_POST['ip'][$i] != '') and ($_POST['shtrih'][$i] != '')) {
						if (!empty($selectShtrich)) {
							mysql_query("UPDATE `shtrich` SET `id_object`='".$id_object."',`ip`='".$_POST['ip'][$i]."',`share_strh`='".$_POST['shtrih'][$i]."' WHERE `id_user`='".$_POST['username'][$i]."'", $dbcnx);
						} else {
							mysql_query("INSERT INTO `shtrich`(`id_object`, `id_user`, `ip`, `share_strh`) VALUES ('".$id_object."','".$_POST['username'][$i]."','".$_POST['ip'][$i]."','".$_POST['shtrih'][$i]."')", $dbcnx);
						}
					}
				}		
			}
			
			// ЕСЛИ ПРИНТЕР ШТРИХ ЭТИКЕТОК НЕ БОЛЕЕ 1-ГО В МАГАЗИНЕ, ОН СТАНОВИТЬСЯ ПО УМОЛЧАНИЮ 
			$selectDomainUserInObjectNotTSD = selectDomainUserInObjectNotTSD ($dbcnx, $id_object);
			/*
			$printersShtrihInObject = printersShtrihInObject ($dbcnx, $id_object);
			if (count($printersShtrihInObject) == 1) {
				foreach ($selectDomainUserInObjectNotTSD as $a) {
					//mysql_query("INSERT INTO `shtrich`(`id_object`, `id_user`, `ip`, `share_strh`) VALUES ('".$id_object."','".$a['id']."','".$printersShtrihInObject[0]['ip']."','".$printersShtrihInObject[0]['print_name']."') ON DUPLICATE KEY UPDATE `ip`='".$printersShtrihInObject[0]['ip']."', `share_strh`='".$printersShtrihInObject[0]['print_name']."'", $dbcnx);
				}
			}
			*/
			
			echo "<table>";
			foreach ($selectDomainUserInObjectNotTSD as $a) {
				echo "<tr><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>";
				echo $a['username'];
				echo "<input name='username[]' type='hidden' size='2' value='".$a['id']."'>";
				echo "</th><th>";
				echo "<select size='1' name='shtrih[]'>";
				echo "<option value='' selected></option>";
				$selectShtrich = selectShtrich ($dbcnx, $a['id']);
				var_dump($selectShtrich);
				$shtrih = ['Citizen','Datamax'];
				foreach ($shtrih as $s) {
					if ($s == $selectShtrich['share_strh']) { echo "<option value='".$s."' selected>".$s."</option>"; }
					else { echo "<option value='".$s."'>".$s."</option>"; }
				}
				echo "</select>";
				echo "</th><th>";
				$selectWsInObjectOnlyWin = selectWsInObjectOnlyWin ($dbcnx, $id_object);
				echo "<select size='1' name='ip[]'>";
				echo "<option value='' selected></option>";
				foreach ($selectWsInObjectOnlyWin as $b) {	
					if ($selectShtrich['ip'] == $b['ip']) { echo "<option value='".$b['ip']."' selected>".$b['ip']."</option>"; }
					else { echo "<option value='".$b['ip']."'>".$b['ip']."</option>"; }							
				}
				echo "</th>";
				echo "</select>";
			}
			echo "</table>";
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Применить'></p>";
			echo "</form>";
		}			
//////   8   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 8) {
			if (isset($_POST['submit8'])) { // ИЗМЕНЕНИЕ ДАННЫХ ВЕСОВ В МАГАЗИНЕ
				for ($i=0; $i<count($_POST['id']); $i++) {
					$arr_myip = explode (".", $_POST['ip'][$i]);
					if (($arr_myip[0] > 255) or ($arr_myip[0] == 0) or ($arr_myip[1] > 255) or ($arr_myip[2] > 255) or ($arr_myip[3] > 255)) {
						echo "<br><br><br><br>";
						echo "<center>Что за херню ты ввел? Разве это IP адрес? ".$arr_myip[0].".<b>".$arr_myip[1]."</b>.".$arr_myip[2].".".$arr_myip[3]."!</center>";
					}
					else {
						/*if ($ipvid != $_POST['video'][$i]) {
							$update_log = "VIDEO:IP_VIDEO ".$ipvid." -> ".$_POST['video'][$i+1]." ";
							$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'video', '".$_POST['shop'][0]."', '".$update_log."')";
							mysql_query($log,$dbcnx);
						}
						if ($model != $_POST['video'][$i]) {
							$update_log = "VIDEO:MODEL ".$model." -> ".$_POST['video'][$i+2]." ";
							$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'video', '".$_POST['shop'][0]."', '".$update_log."')";
							mysql_query($log,$dbcnx);
						}
						if ($channel != $_POST['video'][$i]) {
							$update_log = "VIDEO:CHANNEL ".$channel." -> ".$_POST['video'][$i+3]." ";
							$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'video', '".$_POST['shop'][0]."', '".$update_log."')";
							mysql_query($log,$dbcnx);
						}
						if ($harddrive != $_POST['video'][$i]) {
							$update_log = "VIDEO:HDD ".$harddrive." -> ".$_POST['video'][$i+4]." ";
							$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'video', '".$_POST['shop'][0]."', '".$update_log."')";
							mysql_query($log,$dbcnx);
						}
						if ($cam != $_POST['video'][$i]) {
							$update_log = "VIDEO:CAM ".$cam." -> ".$_POST['video'][$i+5]." ";
							$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'video', '".$_POST['shop'][0]."', '".$update_log."')";
							mysql_query($log,$dbcnx);
						}
						*/
						// ИЗМИНЕНИЕ ДАННЫХ ВИДЕОРЕГИСТРАТОРОВ В ТАБЛИЦЕ 'video'
						
						mysql_query("UPDATE `video` SET `ip`='".$_POST['ip'][$i]."', `model`='".$_POST['model'][$i]."', `channel`='".$_POST['channel'][$i]."', `hdd`='".$_POST['hdd'][$i]."', `login`='".$_POST['login'][$i]."', `pass`='".$_POST['pass'][$i]."' WHERE `id`='".$_POST['id'][$i]."' ", $dbcnx);
					} 	
				}	
			}	
			$video = video ($dbcnx, $id_object); $s = 1;
			echo "<table>";
			$r = 0;
			foreach ($video as $v) {
				echo "<input name='id[".$r."]' type='hidden' size='2' value='".$v['id']."'>";
				echo "<tr><td class='input_edit' colspan='2' style='height: 35px;'><center><b>ВИДЕОСЕРВЕР [ ".$s++." ]</b></center></td></tr>";
				echo "<tr><td class='input_edit'>IP адрес: </td><td><input name='ip[".$r."]' type='text' size='15' value='".$v['ip']."' pattern='((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){3}$'></td></tr>";
				
				echo "<tr><td class='input_edit'>Модель: </td><td>";
				echo "<select size='1' name='model[".$r."]'>";
				echo "<option value='' selected></option>";

				$model = ['RVI', 'RVIHD', 'RVIHDR', 'GV'];
					foreach ($model as $value) {
						if ($v['model'] == $value) {
							echo "<option value='".$value."' selected> ".$value." </option>";
						} else {
							echo "<option value='".$value."'> ".$value." </option>";
						}
					}

				echo "</select>";
				echo "</td></tr>";
				
				echo "<tr><td class='input_edit'>Кол-во каналов: </td><td><select size='1' name='channel[".$r."]'>";
				echo "<option value='' selected></option>";
					$channel = ['4' => '4 канала', '8' => '8 каналов', '16' => '16 каналов', '32' => '32 канала'];
					foreach ($channel as $key=>$c) {
						if ($v['channel'] == $key) {
							echo "<option value='".$key."' selected> ".$c." </option>";
						} else {
							echo "<option value='".$key."'> ".$c." </option>";
						}
					}
				echo "</select></td></tr>";
				echo "<tr><td class='input_edit'>Объем HDD: </td><td><select size='1' name='hdd[".$r."]'>";
				echo "<option value='' selected></option>";
					$hdd = ['250' => '250 Гб', '500' => '500 Гб', '750' => '750 Гб', '1000' => '1 Тб', '2000' => '2 Тб', '4000' => '4 Тб'];
					foreach ($hdd as $key=>$h) {
						if ($v['hdd'] == $key) {
							echo "<option value='".$key."' selected> ".$h." </option>";
						} else {
							echo "<option value='".$key."'> ".$h." </option>";
						}
					}
				echo "</select></td></tr>";
				echo "<tr><td class='input_edit'>Логин | Пароль: </td><td><input name='login[".$r."]' type='text' size='10' value='".$v['login']."'> <input name='pass[".$r."]' type='text' size='10' value='".$v['pass']."'></td></tr>";
				echo "<tr><td colspan='2' style='height: 35px;'></td></tr>";
				$r++;
			}
			echo "</table>";
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Применить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   0   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 0) {
			echo "<br><br><br><br><div id='card_body'>";
			echo "<div class='link'>/ <a href='ip_pelican.php' title='Магазины'>Магазины</a> / <a href='card.php?id=".$idObject."' title='Пеликан'>".$shop_name."</a> / <a href='editcard.php?id=".$idObject."&e=".$etype."' title='Пеликан'>Редактирование</a></div><div class='card'>";
			echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
			echo "<br><center><b>Редактирование данных директора магазина ".$shop_name."</b></center><br><br><p>";
			echo "<form name='dir' method='POST' action='editcard.php?id=".$idObject."&e=".$etype."'>";
			echo "<input name='shop[]' type='hidden' size='2' value='".$idObject."'>";

			$pel_adm = pel_adm ($dbcnx, $idObject);
			
			$dir_fio = $pel_adm[0];
			$dir_tel = $pel_adm[1];
			
			echo "ФИО Директора: <input name='dir[]' type='text' size='30' value='".$dir_fio."'><br><br>";
			echo "Телефон Директора: <input name='dir[]' type='text' size='20' value='".$dir_tel."'><br><br>";

			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$idObject."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Применить'> </p>";
			echo "</form>";
		}
//////   9 +  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 9) {
			if (isset($_POST['submit9'])) { // ИЗМЕНЕНИЕ ДАННЫХ ДОМЕННЫХ ПОЛЬЗОВАТЕЛЕЙ НА ОБЬЕКТЕ
				for ($i=0; $i<count($_POST['username']); $i++) {
					$selectDomainUser = selectDomainUser ($dbcnx, $_POST['id'][$i]);
					$username = $selectDomainUser[0]['username'];
					if ($username != $_POST['username'][$i]) {
						$update_log = "USERNAME ".$username." -> ".$_POST['username'][$i]." ";
						mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."','".$date_time."','domain_user','".$id_object."','".$update_log."')", $dbcnx);		
						mysql_query("UPDATE `domain_user` SET `username`='".$_POST['username'][$i]."' WHERE `id`='".$_POST['id'][$i]."'", $dbcnx);
					}
				}		
			}
			$selectDomainUserInObject = selectDomainUserInObject ($dbcnx, $id_object); $i=1;
			foreach ($selectDomainUserInObject as $a) {
				echo "<input name='id[]' type='hidden' size='2' value='".$a['id']."'>";
				echo "Пользователь [".$i++."]: <input name='username[]' type='text' size='20' value='".$a['username']."'><br><br>";
			}
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Применить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   11 +  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 11) {
			// ИЗМЕНЕНИЕ ДАННЫХ ТСД В МАГАЗИНЕ
			if (isset($_POST['submit11'])) {
				foreach ($_POST['tsd'] as $tsd) {
					/*if ($model != $_POST['video'][$i]) {
						$update_log = "VIDEO: MODEL ".$model." -> ".$_POST['video'][$i+2]." ";
						$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'video', '".$_POST['shop'][0]."', '".$update_log."')";
						mysql_query($log, $dbcnx);
					}*/
					$update = "UPDATE `tsd` SET `s/n`='".$tsd['serial']."' WHERE `id`='".$tsd['id']."' ";
					mysql_query($update, $dbcnx);
				}
			}	
			$tsdInObject = tsdInObject ($dbcnx, $id_object);		
			if (isset($tsdInObject[0]['imei'])) {
				$i = 1;
				foreach ($tsdInObject as $a) {
					echo "<input name='tsd[".($i-1)."][id]' type='hidden' size='2' value='".$a['id']."'>";
					echo "ТСД [".$i."]: <b>S/N: </b> <input name='tsd[".($i-1)."][serial]' type='text' size='14' value='".$a['s/n']."'> <br>";
					echo "<br><br>"; $i++;
				}	
			} else {
				echo "в магазине ТСД нет <br><br>";
			}	
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Применить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   13 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 13) {	
			$whoOpener = whoOpener ($dbcnx, $id_object); $i = 1;
			if (isset($whoOpener)) {
				foreach ($whoOpener as $a) {	
					echo "[".$i++."]: ".$a['fio']." <br><br>";
				}	
			} else {
				echo "к магазину ответственные не закреплены <br><br>";
			}
			echo "<br>";

			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}		
//////   14 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 14) {	
			$emailInObject = emailInObject ($dbcnx, $id_object); $i = 1;
			if (isset($emailInObject)) {
				foreach ($emailInObject as $a) {	
					echo "[".$i++."]: ".$a['email']." <br><br>";
				}	
			} else {
				echo "в магазине нет электронного адреса <br><br>";
			}
			echo "<br>";

			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}	
//////   15 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 15) {	
			$phoneInObject = phoneInObject ($dbcnx, $id_object); $i = 1;
			if (isset($phoneInObject)) {
				foreach ($phoneInObject as $a) {	
					echo "[".$i++."]: ".$a['number']." <br><br>";
				}	
			} else {
				echo "в магазине нет телефона <br><br>";
			}
			echo "<br>";

			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}			
//////  end  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
}	

echo "<br><br>";	
include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");