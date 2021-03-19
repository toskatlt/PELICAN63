<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

$date_time = date("y-m-d H:i:s");
if (isset($_GET['id'])) $id_object = $_GET['id'];
if (isset($_GET['e'])) $etype = $_GET['e'];

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	
	?>
	<script>
	let id_object = '<?php echo @$id_object?>';

	function checkingMailbox() {
		let inputMail = document.getElementById('inputMail').value;
		console.log(inputMail);
		$.ajax({			
			type: "POST",			
			url: "eupd/checkingMail.php",
			data: {
				'inputMail': inputMail
			},
			success: function(data) { 
				console.log(data);
				if (data == 'false') {
					console.log('Нужно создать?');
					if (confirm("Почтовый адрес '"+ inputMail +"' не найден в базе данных! Создать?")) { 
						console.log('Да');
						let name = prompt('Имя отображаемое в Адресной книге:', '');
						addMailbox(inputMail, 0, name); 
					} else { 
						console.log('Нет');
						addMailbox(inputMail, 1);
					}
				} else { 
					console.log('Нет');
					addMailbox(inputMail, 1);
				}
			},
		});
		setTimeout(
			function() {
			location.reload();
			}, 350
		);
	}

	function addMailbox(value1, value2, value3) {
		$.ajax({			
			type: "POST",			
			url: "eupd/checkingMail.php",
			data: {
				'addMailbox': value1,
				'type': value2,
				'id_object': id_object,
				'name': value3
			}
		});
	}

	</script>	
	<?php
	
	$uname = $userdata['id'];
	$selectObject = selectObject ($dbcnx, $id_object); $nameObject = $selectObject['name'];
	echo "<div class='card_body'>";
	echo "<div class='link'>/ <a href='ip_pelican.php' title='Магазины'>Магазины</a> / <a href='card.php?id=".$id_object."' title='Пеликан'>".$nameObject."</a> / <a href='editcard.php?id=".$id_object."&e=".$etype."' title='Пеликан'>Редактирование</a> / <a href='addcard.php?id=".$id_object."&e=".$etype."' title='Пеликан'>Добавление</a></div><div class='card'>";
	echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
	echo "<br><center><b>Добавление данных на объекте ".$nameObject."</b></center><br><br><p>";
	echo "<form name='duser' method='POST' action='addcard.php?id=".$id_object."&e=".$etype."'>";
	
//////   1   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	if (isset($_POST['submit'])) {
		echo "<br><br><br><br>";
		//echo var_dump($_POST["shop"])." - array";
		$update = "UPDATE `shop` SET `name`='".$_POST['shop'][1]."', `telephone`='".$_POST['shop'][4]."', `address_fact`='".$_POST['shop'][2]."', `inet`='".$_POST['shop'][6]."', `ip_router_wan`='".$_POST['shop'][5]."', `email`='".$_POST['shop'][3]."', `worktime`='".$_POST['shop'][7]."', `comm`='".$_POST['shop'][8]."', `group_chk`='".$_POST['shop'][9]."', `domain`='".$_POST['shop'][10]."' WHERE `id`='".$_POST['shop'][0]."'";
		mysql_query($update,$dbcnx);
		
		/*$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`) VALUES ('".$uname."', '".$date_time."', 'shop', '".$_POST['shop'][0]."')";
		mysql_query($log);*/
		echo "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`) VALUES ('".$userdata['id']."', '".$date_time."', 'shop', '".$_POST['shop'][0]."')";
		/*header( 'Location: '.$_SERVER['PHP_SELF'].'?id='.$_POST['shop'][0].'&e=1');
		die();	*/	
	}
//////   2   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if (isset($_POST['submit2'])) {
			$ext_ip = $_POST['ext_ip'];
			$Num = $_POST['Num'];
			$isp = $_POST['isp'];
			$type_conn = $_POST['type_conn'];
			$router = $_POST['router'];		
			
			if (isset($ext_ip)){
			$update_log = "INTERNET Добавлено 'IP адрес': ".$ext_ip." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['id']."', '".$date_time."', 'ws', '".$shop_id_get."', '".$update_log."')";
			mysql_query($log,$dbcnx);
			}
			if (isset($Num)){
			$update_log = "INTERNET Добавлено 'номер подключения': ".$Num." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['id']."', '".$date_time."', 'ws', '".$shop_id_get."', '".$update_log."')";
			mysql_query($log,$dbcnx);
			}
			if (isset($isp)){
			$update_log = "INTERNET Добавлено 'провайдер': ".$isp." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['id']."', '".$date_time."', 'ws', '".$shop_id_get."', '".$update_log."')";
			mysql_query($log,$dbcnx);
			}
			if (isset($type_conn)){
			$update_log = "INTERNET Добавлено 'тип подключения': ".$type_conn." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['id']."', '".$date_time."', 'ws', '".$shop_id_get."', '".$update_log."')";
			mysql_query($log,$dbcnx);
			}					
			if (isset($router)){
			$update_log = "INTERNET Добавлено 'модель роутера': ".$router." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['id']."', '".$date_time."', 'ws', '".$shop_id_get."', '".$update_log."')";
			mysql_query($log,$dbcnx);
			}		
			
			$insert = "INSERT INTO `internet` (`type_conn`, `Num`, `ext_ip`, `isp`, `id_mag`, `router`) VALUES ('".$type_conn."', '".$Num."', '".$ext_ip."', '".$isp."', '".$shop_id_get."', '".$router."')";
			mysql_query($insert,$dbcnx);
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$shop_id_get.'&e=2');
			die();
			
		}
//////  end  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
//////   1   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	if ($etype == 1) {				
		echo "<br><br><br><br><div id='card_body'>";
		echo "<div class='link'>/ <a href='ip_pelican.php' title='Магазины'>Магазины</a> / <a href='addcard.php?e=".$etype."' title='Пеликан'>Добавление</a></div><div class='card'>";
		echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
		echo "<br><center><b>Добавление в базу данных нового магазина</b></center><br><br><p>";
		echo "<form name='shop' method='POST' action='addcard.php?id?e=".$etype."'>";
		echo "Имя магазина: <input name='shop[]' type='text' size='25' placeholder='П 14/3 кв'><br><br>";
		echo "Адрес: <input name='shop[]' type='text' size='31' placeholder='Жилина 9'><br><br>";
		echo "Электронный адрес: <input name='shop[]' type='text' size='31' placeholder='p20@neo.rus'><br><br>";
		echo "Телефонный номер: <input name='shop[]' type='text' size='31' placeholder='555657'><br><br>";
		echo "Внешний IP адрес: <input name='shop[]' type='text' size='30' placeholder='10.10.10.120' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}'><br><br>";
		echo "Номер интернет подключения: <input name='shop[]' type='text' size='20' placeholder='(1741)000666'><br><br>";
		echo "Рабочее время: <input name='shop[]' type='text' size='31' placeholder='9:00-23:00'><br><br>";
		echo "<p><input type='submit' name='submit' value='Изменить'></p>";
		echo "</form>";
	}
//////   2   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	if ($etype == 2) {
		echo "<br><br><br><br><div id='card_body'>";
		echo "<div class='link'>/ <a href='ip_pelican.php' title='Магазины'>Магазины</a> / <a href='card.php?id=".$shop_id."' title='Пеликан'>".$shop_name."</a> / <a href='editcard.php?id=".$shop_id."&e=".$etype."' title='Пеликан'>Редактирование</a> / <a href='addcard.php?id=".$shop_id."&e=".$etype."' title='Пеликан'>Добавление</a></div><div class='card'>";
		echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
		echo "<br><center><b>Добавление рабочих станций в магазине ".$shop_name."</b></center><br><br><p>";
		echo "<form name='ws' method='POST' action='addcard.php?id=".$shop_id."&e=".$etype."'>";

		echo "IP роутера: <input name='ext_ip' type='text' size='20' placeholder='1.0.0.0' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}'><br>";
		echo "Номер подключения: <input name='Num' type='text' size='20' placeholder='(1700)000001'><br>";
		echo "Провайдер: <input name='isp' type='text' size='20' placeholder='АИСТ, ИНФОЛАДА, DOM.RU'><br>";
		echo "Тип подключения: <input name='type_conn]' type='text' size='20' placeholder='ADSL, 3G, ETHERNET'><br>";
		echo "Тип Роутера: <input name='router' type='text' size='20' placeholder='TPLINK, DLINK'><br>";
		
		echo "<input name='ws[]' type='hidden' size='2' value='".$shop_id."'>";
		echo "<p><input type='button' onclick=javascript:window.location='editcard.php?id=".$shop_id."&e=".$etype."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Добавить'> <input type='button' onclick=javascript:window.location='delcard.php?id=".$shop_id."&e=".$etype."' value='Удалить'/></p>";
		echo "</form>";
	}
//////   3 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	if ($etype == 3) {
		if (isset($_POST['submit3'])) {
			$arr_ip = explode (".", $_POST['ip']);
			if (($arr_myip[3] < 1) and ($arr_myip[3] > 255)) {
				echo "<br><br><br><br>";
				echo "Вы ввели не полный IP адрес: ".$_POST['ip']."??";
			} else {
				$update_log = "+ Добавлено: ".$_POST['ip']." ";
				mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$userdata['id']."','".$date_time."','ws','".$id_object."','".$update_log."')", $dbcnx);
				mysql_query("INSERT INTO `ws` (`ip`, `id_object`, `type`, `os`) VALUES ('".$_POST['ip']."', '".$id_object."', '0', '".$_POST['os']."')", $dbcnx);
			}
		}
		$i=1; $wsInObject = wsInObject ($dbcnx, $id_object);
		if (count($wsInObject) > 0) {	
			foreach ($wsInObject as $a) {
				echo "ПК [".$i++."]: ".$a['ip']." ".$w['os']."<br><br>";
			}
		} else { echo "на объекте нет рабочих станций <br><br>"; }	
		$mask = selectInternetMaskInObject ($dbcnx, $id_object);
		echo "ПК [".$i."]: <input name='ip' type='text' size='20' value='".$mask."' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}'>";
		echo "	<select size='1' name='os' style='height: 21px;'>";
		echo "<option value='' selected></option>";
		$wsAllOS = wsAllOS ($dbcnx);
		foreach ($wsAllOS as $w) {
			echo "<option value='".$w['os']."'>".$w['os']."</option>"; 
		}
		echo"</select>";	
		echo "<p><input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Добавить'> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
		echo "</form>";
	}
//////   4 +  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($etype == 4) {
		if (isset($_POST['submit4'])) {
			$arr_myip = explode (".", $_POST['ip']);	
			if (($arr_myip[3] < 1) or ($arr_myip[3] > 255)) {
				echo "<br><br><br><br>";
				echo "Вы ввели не верный IP адрес: ".$_POST['ip']."?? <br><br>";
			} else {
				$update_log = "+ Добавлено: ".$_POST['ip']." ";
				mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$userdata['id']."','".$date_time."','ws','".$id_object."','".$update_log."')",$dbcnx);
				mysql_query("INSERT INTO `ws` (`ip`, `id_object`, `type`, `os`) VALUES ('".$_POST['ip']."', '".$id_object."', '1', 'LINUX')", $dbcnx);
			}
		}	
		$selectWsInObject = selectWsInObject ($dbcnx, $id_object, 1); $i=1;
		foreach ($selectWsInObject as $a) {
			echo "Касса [".$i++."]: ".$a['ip']." <br><br>";
		}
		$mask = selectInternetMaskInObject ($dbcnx, $id_object);
		if (empty($mask)) {
			echo "Касса [".$i."]: <input name='ip' type='text' size='20' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}'>";
		} else {
			echo "Касса [".$i."]: <input name='ip' type='text' size='20' value='".$mask.".' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}'>";
		}
		
		echo "<p><input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Добавить'> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
		echo "</form>";
	}
//////   5 +  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($etype == 5) {
		if (isset($_POST['submit5'])) {
			$arr_myip = explode (".", $_POST['ip']);
			if (($arr_myip[3] < 1) and ($arr_myip[3] > 255)) {
				echo "<br><br><br><br>";
				echo "Вы ввели не верный IP адрес:  ".$_POST['ip']."??";
			} else {
				$update_log = "+ Добавлено: ".$_POST['ip']." ";
				mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_mag`,`inquiry`) VALUES ('".$userdata['id']."','".$date_time."','ves','".$id_object."','".$update_log."')", $dbcnx);
				mysql_query("INSERT INTO `ves`(`ip`, `id_object`) VALUES ('".$_POST['ip']."', '".$id_object."')", $dbcnx);
			}
		}
		$vesInObject = vesInObject($dbcnx, $id_object); $i = 1;
		foreach ($vesInObject as $a) {
			echo "Весы [".$i++."]: ".$a['ip']."<br><br>";	
		}
		echo "Весы [".$i."]: <input name='ip' type='text' size='20' pattern='\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}'>";
	
		echo "<p><input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Добавить'> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
		echo "</form>";
	}
//////   8   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($etype == 8) {
		if (isset($_POST['submit8'])) {
			$arr_myip = explode (".", $_POST['ip']);		
			if ($arr_myip[3] == 0) {
				echo "<br><br><br><br><center><b>Вы ввели не верный ip адрес: ".$_POST['ip']."??</b></center>";
			} elseif (empty($_POST['model'])) {
				echo "<br><br><br><br><center><b>Не введена модель видеорегистратора</b></center>";
			} elseif (empty($_POST['channel'])) {
				echo "<br><br><br><br><center><b>Не выбранно количество каналов</b></center>";
			} else {
				$update_log = "VIDEO Добавлено: ".$_POST['ip']." ";
				mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['id']."', '".$date_time."', 'video', '".$id_object."', '".$update_log."')", $dbcnx);
				mysql_query("INSERT INTO `video`(`id_object`, `ip`, `model`, `channel`, `hdd`, `login`, `pass`) VALUES ('".$id_object."', '".$_POST['ip']."', '".$_POST['model']."', '".$_POST['channel']."', '".$_POST['hdd']."', '".$_POST['login']."', '".$_POST['pass']."')", $dbcnx);
			}
		}
		
		$mask = selectInternetMaskInObject ($dbcnx, $id_object);

		echo "<table>";
		echo "<tr><td class='input_edit'>IP адрес: </td><td><input name='ip' type='text' size='15' value='".$mask.".' pattern='((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){3}$'></td></tr>";
		echo "<tr><td class='input_edit'>Модель: </td><td><input name='model' type='text' size='20' placeholder='RVI, RVIHD, RVIHDR, PANDA, GV'></td></tr>";
		echo "<tr><td class='input_edit'>Кол-во каналов: </td><td><select size='1' name='channel'>";
		echo "<option value='' selected></option>";
			$channel = ['4' => '4 канала', '8' => '8 каналов', '16' => '16 каналов', '24' => '24 канала', '32' => '32 канала'];
			foreach ($channel as $key=>$c) {
				echo "<option value='".$key."'> ".$c." </option>";
			}
		echo "</select></td></tr>";
		echo "<tr><td class='input_edit'>Объем HDD: </td><td><select size='1' name='hdd'>";
		echo "<option value='' selected></option>";
			$hdd = ['250' => '250 Гб', '500' => '500 Гб', '750' => '750 Гб', '1000' => '1 Тб', '2000' => '2 Тб', '4000' => '4 Тб'];
			foreach ($hdd as $key=>$h) {
				echo "<option value='".$key."'> ".$h." </option>";
			}
		echo "</select></td></tr>";
		echo "<tr><td class='input_edit'>Логин | Пароль: </td><td><input name='login' type='text' size='10' value='".$v['login']."' placeholder='admin'> <input name='pass' type='text' size='10' value='".$v['pass']."'></td></tr>";
		echo "<tr><td colspan='2' style='height: 35px;'></td></tr>";
		echo "</table>";	
			
		echo "<p><input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Добавить'> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
		echo "</form>";
	}
//////   9 +  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($etype == 9) {
		if (isset($_POST['submit9'])) {
			if(preg_match("#^[a-z0-9_.]+$#", $_POST['duser'])) {
				$update_log = "+ Добавлено: ".$_POST['duser']." ";
				mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$userdata['id']."','".$date_time."','domain_user','".$id_object."','".$update_log."')",$dbcnx);		
				mysql_query("INSERT INTO `domain_user` (`username`, `id_object` , `run`) VALUES ('".$_POST['duser']."','".$id_object."','1')", $dbcnx);
				
				exec("sudo ssh root@10.63.0.105 dcedit add ".$_POST['duser'], $output);
			}
		}
		$selectDomainUserInObject = selectDomainUserInObject ($dbcnx, $id_object); $i=1;
		foreach ($selectDomainUserInObject as $a) {
			echo "Пользователь [".$i++."]: <b>".$a['username']."</b><br><br>";
		}
		echo "Пользователь [".$i."]: <input name='duser' type='text' size='20' placeholder='min 4 символа, буквы или цифры'>";
		echo "<p><input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Добавить'> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
		echo "</form>";
	}
//////   11 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($etype == 11) {
		if (isset($_POST['submit11'])) {
			$update_log = "+ Добавлено: ".$_POST['add_tsd']." ";
			mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$userdata['id']."','".$date_time."','tsd','".$id_object."','".$update_log."')", $dbcnx);
			mysql_query("INSERT INTO `tsd` (`id_object`, `s/n`) VALUES ('".$id_object."', '".$_POST['add_tsd']."')", $dbcnx);
		}
		$tsdInObject = tsdInObject ($dbcnx, $id_object); $i = 1;
		foreach ($tsdInObject as $a) {		
			echo "ТСД [".$i++."]: <b>S/N: </b> ".$a['s/n']."<br><br>";
		}
		echo "ТСД [".$i."]: <b>S/N: </b> <input name='add_tsd' type='text' size='14' value='".$a['s/n']."'> <br>"; 
		echo "<p><input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Добавить'> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
		echo "</form>";
	}
//////   13 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($etype == 13) {
		if (isset($_POST['submit13'])) {
			$update_log = "+ Ответственный: ".$_POST['add']." ";
			mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$userdata['id']."','".$date_time."','opener','".$id_object."','".$update_log."')", $dbcnx);
			mysql_query("INSERT INTO `opener` (`id_object`, `id_domain_user`) VALUES ('".$id_object."','".$_POST['add']."')", $dbcnx);
		}
		$whoOpener = whoOpener ($dbcnx, $id_object); $i = 1;
		if (count($whoOpener) > 0) {
			foreach ($whoOpener as $a) {	
				echo "[".$i++."]: ".$a['fio']." <br><br>";
			}	
		}
		
		echo "[".$i."]: ";
		
		$adminUserWhoNotOpener = adminUserWhoNotOpener ($dbcnx, $id_object);
		echo "<select size='1' name='add' style='width: 250px;height: 24px;'>";
		echo "<option value='0'></option>";
			foreach ($adminUserWhoNotOpener as $a) {
				echo "<option value='".$a["id"]."'>".$a["fio"]."</option>";
			}
		echo "</select>";
		echo "<p><input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Добавить'> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
		echo "</form>";
	}
//////   14 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($etype == 14) {
		$emailInObject = emailInObject ($dbcnx, $id_object); $i = 1;
		if (isset($emailInObject)) {
			foreach ($emailInObject as $a) {	
				echo "Электронный адрес [".$i++."]: ".$a['email']." <br><br>";
			}	
		} else {
			echo "в магазине нет электронного адреса <br><br>";
		}
		echo "Электронный адрес [".$i."]: <input name='inputMail' id='inputMail' type='text' size='20'>";
		echo "</form>";
		echo "<br><br>";
		echo "<p><input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Добавить' onclick=\"checkingMailbox();\"> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
	}
//////   15 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($etype == 15) {
		if (isset($_POST['submit15'])) {
			$update_log = "+ Тел.номер: ".$_POST['add']." ";
			mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$userdata['id']."','".$date_time."','phone','".$id_object."','".$update_log."')", $dbcnx);
			mysql_query("INSERT INTO `phone`(`id_object`,`number`) VALUES ('".$id_object."','".$_POST['add']."')", $dbcnx);
		}
		$phoneInObject = phoneInObject ($dbcnx, $id_object); $i = 1;
		if (isset($phoneInObject)) {
			foreach ($phoneInObject as $a) {	
				echo "Телефонный номер [".$i++."]: ".$a['number']." <br><br>";
			}	
		} else {
			echo "в магазине нет телефона <br><br>";
		}
		echo "Телефонный номер [".$i."]: <input name='add' type='text' size='20'>";

		echo "<p><input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Добавить'> <input type='button' onclick=javascript:window.location='delcard.php?id=".$id_object."&e=".$etype."' value='Удалить'/></p>";
		echo "</form>";
	}		
//////  end  //////////////////////////////////////////////////////////////////////////////////////////////////////////////		
}

echo "<br><br>";	
include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");