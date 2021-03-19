<?php
header('Content-type: text/html; charset=utf-8');
include "config.php";

$uploaddir = '/var/www/html/uploads/';
$uploadfile = $uploaddir . basename('INN_out.csv');

$handle = fopen($uploadfile, "r");

		// echo "<b>".$uploadfile." - ОБРАБАТЫВАЕМЫЙ ФАЙЛ [ от 16 ноября 2018 ]</b><br><br>";
		//////////////////////////////////////////////////////////////////////////////////////////
		while (($data = fgetcsv($handle, 4000, ";")) !== FALSE) {
			for ($c=0; $c < 1; $c++) {
				
				$data[0] = iconv("cp866", "utf8", $data[0]); // ФИО
				$data[1] = iconv("cp866", "utf8", $data[1]); // ID сотрудника
				$data[2] = iconv("cp866", "utf8", $data[2]); // Должность
				$data[3] = iconv("cp866", "utf8", $data[3]); // ID подразделения
				$data[4] = iconv("cp866", "utf8", $data[4]); // Имя подразделения
				$data[5] = iconv("cp866", "utf8", $data[5]); // ИНН
				
				// echo '<b>'.$i++.'. Табельный номер сотрудника: '.$data[1].'</b><br><br>';
				
				$pos1 = stripos($data[4], 'зал');
				if ($pos1 !== false) {
					preg_match( '@Торговый зал\s(.*?)$@siu', $data[4], $z); 
					$name_subpivision = $z[1];
				} else {
					$name_subpivision = $data[4];
				}
				
//////////////////////////////////////////// WORK_SUBDIVISION ////////////////////////////////////////////////////////////////////////////////////////////////////				
				$res_WS = mysql_query("SELECT count(*) FROM `work_subdivision` WHERE `id` = '".$data[3]."' AND `name` = '".$name_subpivision."'", $dbcnx);
					//echo '<b>Проверка наличия подразделения [ '.$name_subpivision.' ] в базе:</b><br>';
				$row_WS = mysql_fetch_row($res_WS);
				if ($row_WS[0] == 0) {
					// echo '<b style="color:red;">&nbsp;&nbsp;&nbsp; Подразделение НЕ найдено. Добавление подразделения в БД со значением `idx`=2 (как вновь добавленное): </b><br>';
					mysql_query('INSERT INTO `work_subdivision`(`id`, `name`, `id_object` , `idx`) VALUES ("'.$data[3].'", "'.$name_subpivision.'", "0", "2")', $dbcnx);
					// echo '&nbsp;&nbsp;&nbsp; INSERT INTO `work_subdivision`(`id`, `name`, `id_object` , `idx`) VALUES ("'.$data[3].'", "'.$name_subpivision.'", "0", "2") <br>';
				} else {
					// echo '<b style="color:green;">&nbsp;&nbsp;&nbsp; Подразделение найдено. Подтверждение актуальности подразделения: </b><br>';
					mysql_query("UPDATE `work_subdivision` SET `idx`='1' WHERE `id` = '".$data[3]."' and `name` = '".$name_subpivision."'", $dbcnx);
					// echo "&nbsp;&nbsp;&nbsp; UPDATE `work_subdivision` SET `idx`='1' WHERE `id` = '".$data[3]."' and `name` = '".$name_subpivision."' <br>";
				}
				
				echo "<br><br>";
				
//////////////////////////////////////////// WORK_POSITION ////////////////////////////////////////////////////////////////////////////////////////////////////
				$res_WP = mysql_query("SELECT count(*) FROM `work_position` WHERE `id_work_gpoup` = '".$data[3]."' AND `name` = '".$data[2]."'", $dbcnx);
				// echo '<b>Проверка наличия должности [ '.$data[2].' ] на подразделении [ '.$name_subpivision.' ] в базе:</b><br>';
				// echo "&nbsp;&nbsp;&nbsp; SELECT count(*) FROM `work_position` WHERE `id_work_gpoup` = '".$data[3]."' AND `name` = '".$data[2]."'<br>";
				$row_WP = mysql_fetch_row($res_WP);
				if ($row_WP[0] == 0) {
					// echo '<b style="color:red;">&nbsp;&nbsp;&nbsp; Должность НЕ найдена. Добавление должности: </b><br>';
					if ($data[2] == 'Администратор торгового зала') $access = '3';
					elseif ($data[2] == 'Директор магазина') $access = '5';
					elseif ($data[2] == 'Товаровед') $access = '4';
					elseif ($data[2] == 'Продавец-кассир') $access = '2';
					else $access = '0';
					
					mysql_query('INSERT INTO `work_position`(`id_work_gpoup`, `name`, `access`, `idx`) VALUES ("'.$data[3].'", "'.$data[2].'", "'.$access.'", "2")', $dbcnx);
					// echo '&nbsp;&nbsp;&nbsp; INSERT INTO `work_position`(`id_work_gpoup`, `name`, `access`, `idx`) VALUES ("'.$data[3].'", "'.$data[2].'", "'.$access.'", "2") <br>';
				} else {
					// echo '<b style="color:green;">&nbsp;&nbsp;&nbsp; Должность найдена. Подтверждение актуальности должности: </b><br>';
					mysql_query("UPDATE `work_position` SET `idx`='1' WHERE `id_work_gpoup` = '".$data[3]."' AND `name` = '".$data[2]."'", $dbcnx);
					// echo "&nbsp;&nbsp;&nbsp; UPDATE `work_position` SET `idx`='1' WHERE `id_work_gpoup` = '".$data[3]."' AND `name` = '".$data[2]."' <br>";
				}		
				
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
				echo '<br><br>';
				
				
				// ВЫВОД ПОЗИЦИИ
				$query_WP = mysql_query("SELECT id FROM `work_position` WHERE `id_work_gpoup` = '".$data[3]."' AND `name` = '".$data[2]."'", $dbcnx);
				// echo "&nbsp;&nbsp;&nbsp; SELECT id FROM `work_position` WHERE `id_work_gpoup` = '".$data[3]."' AND `name` = '".$data[2]."' <br>";
				$result_WP = mysql_fetch_assoc($query_WP); // $result_WP['id']
				// echo '<b>ID должности: '.$result_WP['id'].'</b><br>';
				// ВЫВОД ID ОБЪЕКТА
				$query_WS = mysql_query("SELECT id_object FROM `work_subdivision` WHERE `id` = '".$data[3]."' AND `name` = '".$name_subpivision."'", $dbcnx);
				// echo "&nbsp;&nbsp;&nbsp; SELECT id_object FROM `work_subdivision` WHERE `id` = '".$data[3]."' AND `name` = '".$name_subpivision."' <br>";
				$result_WS = mysql_fetch_assoc($query_WS); // $result_WS['id_object']
				// echo '<b>ID объекта: '.$result_WS['id_object'].'</b><br>';
				// echo "<br>";
				// WORK_DOMAIN_USER
				$res_WDU = mysql_query("SELECT count(*) FROM `work_domain_user` WHERE `t_number` = '".$data[1]."'", $dbcnx);
				$row_WDU = mysql_fetch_row($res_WDU);
				// var_dump($row_WDU);
				// echo "<b>Поиск сотрудника по id номеру</b><br>";
				// echo "&nbsp;&nbsp;&nbsp; SELECT count(*) FROM `work_domain_user` WHERE `t_number` = '".$data[1]."''<br><br>";
				
				preg_match('/(\w+)\s(\w{1})/siu', $data[0], $nameshort);
				$fio = $nameshort[1].'.'.$nameshort[2];
				
				// echo "<br>";
				/*
				if (($result_WS['id_object'] == '285' ) OR ($result_WS['id_object'] == '298')) {
					$str2url = str2url($fio);
					echo "<b> ".$row_WDU[0]." ".$data[0]." -> ".$fio." -> ".$str2url."</b> ";	
				}
				elseif ($data[2] == 'Директор магазина') {
					echo "<b> ".$row_WDU[0]." ".$data[0]." </b> ";	
					$selectUprav = selectUprav ($dbcnx, $result_WS['id_object']);
					if (isset($selectUprav['username'])) echo ' <b style="color:blue;">[ '.$selectUprav['username'].' ]</b>';		
					
				}
				*/			
				
				$passwd = randomNumber(8);
				
				$checkPasswd = checkPasswd($passwd);
				// echo 'Уникальность пароля: '.$checkPasswd.' <br>';
				
				while ($checkPasswd = 0) {
					// echo '<b>Пароль не уникален!</b><br>';
					$passwd = randomNumber(8);
					$checkPasswd = checkPasswd($passwd, $dbcnx);
				}	
				
				if (!empty($data[5])) {
					// echo '<b>Пароль: '.$passwd.'</b> [ '.strlen($passwd).' ]<br><br>';
				} else {
					// echo '<b>Пароль: нет ИНН<br><br>';
					$passwd = 0;
				}
				
				
				if ($row_WDU[0] > '0') {
					// echo '<b style="color:green;">&nbsp;&nbsp;&nbsp; Сотрудник найден. Обновление данных: </b><br>';	
					mysql_query("UPDATE `work_domain_user` SET `id_position`='".$result_WP['id']."', `id_object`='".$result_WS['id_object']."', `fio`='".$data[0]."', `inn`='".$data[5]."', `passwd`='".$passwd."', `date`='".$date."', `idx`='1' WHERE `t_number`='".$data[1]."' ", $dbcnx);
					// echo "&nbsp;&nbsp;&nbsp; UPDATE `work_domain_user` SET `id_position`='".$result_WP['id']."', `id_object`='".$result_WS['id_object']."', `fio`='".$data[0]."', `inn`='".$data[5]."', `passwd`='".$passwd."', `date`='".$date."',  `idx`='1' WHERE `t_number`='".$data[1]."'<br>";
				} else {
					// echo '<b style="color:red;">&nbsp;&nbsp;&nbsp; Сотрудник не найден. Добавление данных: </b><br>';
					mysql_query("INSERT INTO `work_domain_user`(`fio`, `id_position`, `id_object`, `t_number`, `inn`, `passwd`, `date`, `idx`) VALUES ('".$data[0]."', '".$result_WP['id']."', '".$result_WS['id_object']."', '".$data[1]."', '".$data[5]."', '".$passwd."', '".$date."', '1')", $dbcnx);
					// echo "&nbsp;&nbsp;&nbsp; INSERT INTO `work_domain_user`(`fio`, `id_position`, `id_object`, `t_number`, `inn`, `passwd`, `date`, `idx`) VALUES ('".$data[0]."', '".$result_WP['id']."', '".$result_WS['id_object']."', '".$data[1]."', '".$data[5]."', '".$passwd."', '".$date."', '1')<br>";
				}
				// echo "<br><br><hr><br>";
				// echo "</span>";	
			}
		}
		mysql_query ("DELETE FROM `work_subdivision` WHERE `idx`='0'", $dbcnx);
		mysql_query ("DELETE FROM `work_position` WHERE `idx`='0'", $dbcnx);
		//mysql_query ("DELETE FROM `work_domain_user` WHERE `idx`='0'", $dbcnx);
	}
}	

fclose($handle);