<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

echo "<script src='/js/spin/spin.js'></script>";
echo "<script src='/js/spin/hideshow.js'></script>";

echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">';

require_once("function/function_staff.php");
require_once("function/function_scan.php");
$date_time = date("y-m-d H:i:s");


if (isset($_GET['id'])) $id_object = $_GET['id'];
if (isset($_GET['email'])) {
	
	if (strpos($_GET['email'], 'mindal') == true) { 
		?> <script> location.replace("/ip_mindal.php"); </script> 
	<?php }
	
	if(strpos($_GET['email'], 'neo.rus') == true) {
		$_GET['email'] = str_replace('neo.rus', 'neo63.ru', $_GET['email']);
	} 
	
	$whoseEmail = whoseEmail ($dbcnx, $_GET['email']);
	if ($whoseEmail[0]['id_domain_user'] != 0) {
		$url = "/editpers?id=".$whoseEmail[0]['id_domain_user']."";
		?> 
		<script> 
			var URL = '<?php echo $url;?>';
			location.replace(URL);
		</script> 
		<?php
	} else {
		$id_object = $whoseEmail[0]['id_object'];
	}	
	
	//$objectFromEmail = objectFromEmail($dbcnx, $_GET['email']);
	//$id_object = $objectFromEmail['id'];	
}

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	
	$uname = $userdata['id'];
	if (in_array($userdata['access'], array(3, 4, 5, 6, 7, 8, 9, 10))) {	
		/*
		// ЕСЛИ ПРИНТЕР ШТРИХ ЭТИКЕТОК НЕ БОЛЕЕ 1-ГО В МАГАЗИНЕ, ОН СТАНОВИТЬСЯ ПО УМОЛЧАНИЮ 
		$selectDomainUserInObjectNotTSD = selectDomainUserInObjectNotTSD ($dbcnx, $id_object);
		$printersShtrihInObject = printersShtrihInObject ($dbcnx, $id_object);
		if (count($printersShtrihInObject) == 1) {
			foreach ($selectDomainUserInObjectNotTSD as $a) {
				mysql_query("INSERT INTO `shtrich`(`id_object`, `id_user`, `ip`, `share_strh`) VALUES ('".$id_object."','".$a['id']."','".$printersShtrihInObject[0]['ip']."','".$printersShtrihInObject[0]['print_name']."') ON DUPLICATE KEY UPDATE `ip`='".$printersShtrihInObject[0]['ip']."', `share_strh`='".$printersShtrihInObject[0]['print_name']."'", $dbcnx);
			}
		}
		*/
		if (isset($_POST['submit_du'])) {
			$u = userListSelect ($dbcnx, $_POST['domain_user']);
			if (isset($u['name_user'])){
				mysql_query("INSERT INTO `kill_list`(`id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run`) VALUES ('".$u['id']."','".$u['ip']."','".$u['datetime']."','".$u['name_comp']."','".$u['name_user']."','".$u['domain_name']."','".$u['password']."','1')", $dbcnx);
		////   LOG   ///////////////////////////////
				$update_log = "KILL_USER: пользователь ".$_POST['domain_user']." ";
				mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'kill_user', '".$id_object."', '".$update_log."')", $dbcnx);
			}
		}
		
		if (isset($_POST['submitOutAllUser_x'])) {
			$selectDomainUserInObject = selectDomainUserInObject ($dbcnx, $id_object);

			for ($i=0; $i < count($selectDomainUserInObject); $i++) {
				mysql_query("INSERT INTO kill_list (`id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run`) SELECT `id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run` FROM user_list WHERE user_list.name_user='".$selectDomainUserInObject[$i]['username']."'", $dbcnx);
				//echo "INSERT INTO kill_list (`id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run`) SELECT `id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run` FROM user_list WHERE user_list.name_user='".$selectDomainUserInObject[$i]['username']."'<br>";
			}
		////   LOG   ///////////////////////////////
			$update_log = "KILL_USER: пользователи магазина ".$id_object." ";
			mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'kill_user', '".$id_object."', '".$update_log."')", $dbcnx);
			
		}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//array(4) { ["domain_user"]=> string(12) "lenina_oper2" ["submit_block1_x"]=> string(1) "4" ["submit_block1_y"]=> string(1) "6" ["submit_block1"]=> string(12) "lenina_oper2" } 
		
		if (isset($_POST['submit_block1'])) {
			$du = $_POST['domain_user'];
			$update = "UPDATE `domain_user` SET `run`='0' WHERE `username`='".$du."'";
			mysql_query($update, $dbcnx);
		////   LOG   ///////////////////////////////	
			$update_log = "BLOCK_USER: пользователь ".$du." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['user_login']."', '".$date_time."', 'kill_user', ' - ', '".$update_log."')";
			mysql_query($log, $dbcnx);			
		}
		if (isset($_POST['submit_block0'])) {
			$du = $_POST['domain_user'];
			$update = "UPDATE `domain_user` SET `run`='1' WHERE `username`='".$du."'";
			mysql_query($update, $dbcnx);
		////   LOG   ///////////////////////////////	
			$update_log = "UNBLOCK_USER: пользователь ".$du." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['user_login']."', '".$date_time."', 'domain_user', ' - ', '".$update_log."')";
			mysql_query($log, $dbcnx);		
		}
		if (isset($_POST['submitAllBlock_x'])) {
			$update = "UPDATE `domain_user` SET `run`='0' WHERE `id_object`='".$id_object."'";
			mysql_query($update, $dbcnx);
		////   LOG   ///////////////////////////////	
			$update_log = "BLOCK_USER: пользователи магазина ".$id_object." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['user_login']."', '".$date_time."', 'domain_user', ' - ', '".$update_log."')";
			mysql_query($log, $dbcnx);		
		}
		if (isset($_POST['submitAllUnblock_x'])) {
			$update = "UPDATE `domain_user` SET `run`='1' WHERE `id_object`='".$id_object."'";
			mysql_query($update, $dbcnx);
		////   LOG   ///////////////////////////////	
			$update_log = "UNBLOCK_USER: пользователи магазина ".$id_object." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['user_login']."', '".$date_time."', 'domain_user', ' - ', '".$update_log."')";
			mysql_query($log, $dbcnx);		
		}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if (isset($_POST['opennow'])) {
			mysql_query("UPDATE `object` SET `open`='1' WHERE `id`='".$id_object."'", $dbcnx);
		////   LOG   ///////////////////////////////	
			$update_log = "OPEN_SHOP: магазин ".$id_object." ";
			$log = "INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'object', '".$id_object."', '".$update_log."')";
			mysql_query($log,$dbcnx);
		}
		if (isset($_GET['cls'])) {
			mysql_query("UPDATE `object` SET `open`='0' WHERE `id`='".$id_object."'", $dbcnx);	
			$update_log = "CLOSE_SHOP: ".$id_object." ";
			mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$userdata['id']."','".$date_time."','ws','".$id_object."','".$update_log."')",$dbcnx);	
		}
		if (isset($_POST['subAddProduct'])) {		
			$id = $_POST['type'];
			$selectOpeningIdDeport = selectOpeningIdDeport ($id, $dbcnx);
			mysql_query("INSERT INTO `opening`(`products`, `id_deport`, `id_parents`) VALUES ('".$_POST['newproduct']."','".$selectOpeningIdDeport[0]['id_deport']."','".$_POST['type']."')",$dbcnx);
		////   LOG   ///////////////////////////////	
			$update_log = "ADD_PRODUCT: новый тип ".$_POST['newproduct']." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['user_login']."', '".$date_time."', 'opening', '".$_GET['id']."', '".$update_log."')";
			mysql_query($log,$dbcnx);
		}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
			$sh = selectObject ($dbcnx, $id_object);
			$id_building = $sh['id_building']; 
				
			$type = $sh['type']; 
			$name = $sh['name']; 
			$date_open = $sh['date_open'];
			$open = $sh['open'];
			$address = $sh['address'];
			$area = $sh['area'];
		if ($open == '2') { 
			echo "<div class='card_body' id='card_body' style='background: -webkit-linear-gradient(top, #fad4ff, #cae6f3);'>"; 
		} else { echo "<div class='card_body' id='card_body'>"; }		
/////////////////////////////////////////////////////////////////////////////////////////				
///////////////////////////////////// ЛОГИ  - НАЧАЛО ////////////////////////////////////	
/////////////////////////////////////////////////////////////////////////////////////////				
		if ((isset($id_object)) and (isset($_GET['du']))) { // ЛОГИ ДОМЕННЫХ ПОЛЬЗОВАТЕЛЕЙ МАГАЗИНОВ
			echo "<div class='link'><a href='card?id=".$id_object."' title='Назад к объекту'><img width='20px' src='img/back_arrow.png'></a></div>";
			echo "<div class='card'>";
			echo "<center>ЛОГИ ПОЛЬЗОВАТЕЛЯ <b>".$_GET['du']."</b><br><br><br>";
			$selectSessionUser = selectSessionUser ($dbcnx, $_GET['du']);
			foreach ($selectSessionUser as $a) {
				$maxdate = date('H:i d.m.Y', strtotime($a['max(date)']));
				echo "<p>● Терминал <b>".$a['ip_term']."</b> - последний вход <b>".$maxdate."</b><p>";
			}			
			echo "</center></div></div><br><br>";		
		} elseif ((isset($id_object)) and (isset($_GET['log']))) { // ЛОГИ ИЗМИНЕНИЙ В БД ПО МАГАЗИНУ
			echo "<div class='link'><a href='card?id=".$id_object."' title='Магазины'><img width='20px' src='img/back_arrow.png'></a></div>";
			echo "<div class='card'>";
			echo "<center><b>ЛОГИ ИЗМИНЕНИЙ В БД ПО МАГАЗИНУ</b><br><br><br>";
			
			$shop_log_select = shop_log_select ($dbcnx, $id_object);
				
			echo "<table class='tablesorter' style='margin: auto;width: 630px;' cellspacing='1'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th style='width: 5%;text-align:center'>№</th>";
			echo "<th style='width: 20%;text-align:center'>USER</th>";
			echo "<th style='width: 15%;text-align:center'>ДАТА</th>";
			echo "<th style='width: 50%;text-align:center'>ИЗМЕНЕНИЕ</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody><tr>";
			$i = 1;
			foreach ($shop_log_select as $log) {
				echo "<tr>";
				echo "<td style='font-size: 12px;' title='".$log['id']."'>".$i++."</td>";
				echo "<td style='font-size: 12px;'>".$log['user_name']."</td>";
				echo "<td style='font-size: 12px;'>".date('H:i d.m.y', strtotime($log['date']))."</td>";
				echo "<td style='font-size: 12px;'>".$log['inquiry']."</td>";
				echo "</tr>";
			}	
			echo "</tr></tbody>";
			echo "</table>";
			echo "</center></div></div>";		
		} elseif ((isset($id_object)) and (isset($_GET['ch']))) {	// ОТЧЕТЫ О ПРОВЕРКАХ БД
			$selectCheckBD = selectCheckBD ($dbcnx, $_GET['ch']);
				list($year, $month, $day) = sscanf($selectCheckBD[0]['date'], "%2s %2s %2s");
			echo "<div class='link'><a href='card?id=".$id_object."' title='Назад к объекту'><img width='20px' src='img/back_arrow.png'></a></div>";
			echo "<div class='checkBD'>";
			echo "<center><b>ОТЧЕТ О ПРОВЕРКЕ БД от ".$day.".".$month.".".$year."</b></center>";
			echo "<br>";

				echo "<pre>".$selectCheckBD[0]['log']."</pre>";
			echo "</div></div><br><br>";
			echo "</div>";
		}
/////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// ЛОГИ - КОНЕЦ /////////////////////////////////////////		
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////// СТРАНИЦА ДЛЯ РАБОТЫ С ЕГАИС - НАЧАЛО ////////////////////////////	
/////////////////////////////////////////////////////////////////////////////////////////				
		elseif ((isset($id_object)) and (isset($_GET['eg']))) {
			echo "<div class='link'><a href='card?id=".$id_object."' title='Магазин'><img width='20px' src='img/back_arrow.png'></a></div>";
			echo "<div class='card'>";
			
			$selectAtolInObject = selectAtolInObject ($dbcnx, $id_object);
			$ipAtolInObject = trim($selectAtolInObject['ip']);
			
			echo "<center><b>РАБОТА С ЕГАИС <a href='http://".$ipAtolInObject.":8080' target='_blank'><img src='img/egais_logo.png' style='width: 17px;'></a></b></center><br><br><br>";
			
			$today = date('Ymd');

			if (!file_exists("egais/".$id_object)) { mkdir("egais/".$id_object, 0777); }
			if (!file_exists("egais/".$id_object."/opt")) { mkdir("egais/".$id_object."/opt", 0777); }
			if (!file_exists("egais/".$id_object."/opt/out")) { mkdir("egais/".$id_object."/opt/out", 0777); }
			if (!file_exists("egais/".$id_object."/opt/in")) { mkdir("egais/".$id_object."/opt/in", 0777); }
			
				# вывод всех записей в папке /opt/out
				exec("curl –X GET http://".$ipAtolInObject.":8080/opt/out", $output);
				
				# из строк выбираем replyid	
					for ($s=2; $s < count($output)-2; $s++) {
						preg_match('/replyid="(.*?)"/siu', $output[$s], $array);
						$replyid_array[] = $array[1];
					}
					
				echo "<center><b>Документов в папке Входящие [".count($replyid_array)."]</b></center>";	
				
				# оставляем только уникальные значения	
				$replyid_array = array_unique($replyid_array);
				
				echo "<br><br>";
					
				$o=0;
				foreach ($replyid_array as $replyid) {
					
					echo ++$o.". <b>replyid: ".$replyid."</b> <br>";
					
					exec("curl –X GET http://".$ipAtolInObject.":8080/opt/out?replyId=".$replyid, $file);
						//echo count($file)." <br>";
					if (count($file) == 5) {
						preg_match('/>(.*?)</siu', $file[2], $link);
						preg_match('/out\/(.*?)\//siu', $file[2], $type);
						preg_match('/out\/'.$type[1].'\/(.*?)</siu', $file[2], $id);
						preg_match('/fileId="(.*?)"/siu', $file[2], $fileId);
						preg_match('/timestamp="(.*?)"/siu', $file[2], $timestamp);
						
						echo "<b> - fileId:</b> ".$fileId[1]."";
							if ((!file_exists("egais/".$id_object."/opt/out/".$fileId[1].".xml")) or (filesize("egais/".$id_object."/opt/out/".$fileId[1].".xml")) < 1) {
								$url = file_get_contents($link[1]);
								file_put_contents("egais/".$id_object."/opt/out/".$fileId[1].".xml", $url);
								echo " <b style='color: #ff0000;'> НОВЫЙ !!</b>";
							}		
						echo "<br>";
							
						mysql_query("INSERT INTO `e_out_doc`(`id_object`, `fileID`, `replyID`, `type`, `ID`, `date`) VALUES ('".$id_object."', '".$fileId[1]."', '".$replyid."', '".$type[1]."', '".$id[1]."', '".$timestamp[1]."')", $dbcnx);
				
					} else {
						$count = count($file)-2;
						for ($y=2; $y < $count; $y++) {
							preg_match('/>(.*?)</siu', $file[$y], $link);
							preg_match('/out\/(.*?)\//siu', $file[$y], $type);
							preg_match('/out\/'.$type[1].'\/(.*?)</siu', $file[$y], $id);
							preg_match('/fileId="(.*?)"/siu', $file[$y], $fileId);
							preg_match('/timestamp="(.*?)"/siu', $file[$y], $timestamp);
							
							echo "<b> - fileId:</b> ".$fileId[1]."";
								if ((!file_exists("egais/".$id_object."/opt/out/".$fileId[1].".xml")) or (filesize("egais/".$id_object."/opt/out/".$fileId[1].".xml")) < 1) {
									$url = file_get_contents($link[1]);
									file_put_contents("egais/".$id_object."/opt/out/".$fileId[1].".xml", $url);
									echo " <b style='color: #ff0000;'> НОВЫЙ !!</b>";
								}
							echo "<br>";
								
							mysql_query("INSERT INTO `e_out_doc`(`id_object`, `fileID`, `replyID`, `type`, `ID`, `date`) VALUES ('".$id_object."', '".$fileId[1]."', '".$replyid."', '".$type[1]."', '".$id[1]."', '".$timestamp[1]."')", $dbcnx);
						}
					}
					echo "<br><br>";		
					$file = null;	
				}	
				
				/*
				$o=0;
				for ($i=2; $i < count($output)-2; $i++) {
					
					preg_match('/>(.*?)</siu', $output[$i], $link);
					preg_match('/out\/(.*?)\//siu', $output[$i], $type);
					preg_match('/out\/'.$type[1].'\/(.*?)</siu', $output[$i], $id);
					preg_match('/replyid="(.*?)"/siu', $output[$i], $replyid);
						exec("curl –X GET http://".$ipAtolInObject.":8080/opt/out?replyId=".$replyid[1], $file);
						
						$url = file_get_contents($link[1]);
					if (count($file) == 5) {	
						preg_match('/fileId="(.*?)"/siu', $file[2], $fileId);
						preg_match('/timestamp="(.*?)"/siu', $file[2], $timestamp);
							file_put_contents("egais/".$id_object."/opt/out/".$fileId[1].".xml", $url);
						echo "<br><br>";
						echo ++$o.". <a href='".$link[1]."'>".$type[1]."/".$id[1]."</a> | replyid: ".$replyid[1]." <br>";	
						echo "fileId: ".$fileId[1]."<br><br><br>";
						mysql_query("INSERT INTO `e_out_doc`(`fileID`, `replyID`, `type`, `ID`, `date`) VALUES ('".$fileId[1]."', '".$replyid[1]."', '".$type[1]."', '".$id[1]."', '".$timestamp[1]."')", $dbcnx);
				
					} else {
						$count = count($file)-2;
						for ($y=2; $y < $count; $y++) {
							preg_match('/fileId="(.*?)"/siu', $file[$y], $fileId);
							preg_match('/timestamp="(.*?)"/siu', $file[$y], $timestamp);
								file_put_contents("egais/".$id_object."/opt/out/".$fileId[1].".xml", $url);
							echo "<br><br>";
							echo ++$o.". <a href='".$link[1]."'>".$type[1]."/".$id[1]."</a> | replyid: ".$replyid[1]." <br>";	
							echo "fileId: ".$fileId[1]."<br><br><br>";
							mysql_query("INSERT INTO `e_out_doc`(`fileID`, `replyID`, `type`, `ID`, `date`) VALUES ('".$fileId[1]."', '".$replyid[1]."', '".$type[1]."', '".$id[1]."', '".$timestamp[1]."')", $dbcnx);
						}	
					}	
					$file = null;
					
				}	
				
				/*
				$i = count($xml->url);
				for ($a = 0; $a < $i; $a++){
					$url=$xml->url[$a];
					$url3 = preg_replace("/(.*)(out\/)/","",$url);
					echo "<a href = 'http://".$ipAtolInObject.":8080/opt/out/".$url3."'>".$url3."</a>";
					echo "<br>";
				}
				*/
	
			echo "</div></div>";	
		}
/////////////////////////////////////////////////////////////////////////////////////////				
/////////////////////// СТРАНИЦА ДЛЯ РАБОТЫ С ЕГАИС - КОНЕЦ /////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////// СТАТИСТИКА ОТКРЫТИЙ МАГАЗИНОВ - НАЧАЛО //////////////////////////	
/////////////////////////////////////////////////////////////////////////////////////////	
		elseif (isset($_GET['open'])) {
			echo "<div class='link' style='width: 780px;'><a href='card' title='Магазины'><img width='20px' src='img/back_arrow.png'></a></div>";
			echo '<div class="card">';		
			echo "<center><b><a href='/stats?o'>СТАТИСТИКА ПО ДАТАМ ОТКРЫТИЙ МАГАЗИНОВ</a></b><br><br><br>";
			$year = date ("Y");
			while ($year > 2005) {
				$openAllShopDate = openAllShopDate ($dbcnx, $year);
				$i=1;
				
				echo "<br>".str_repeat("-", 70)." ".$year." ".str_repeat("-", 70)."<br><br>";
				echo "<table style='color: #666;'>";

				foreach ($openAllShopDate as $oasd) {
					echo "<tr>";
					echo "<td style='width: 26px;'>".$i.". </td><td style='width: 350px;'><a href='card?id=".$oasd['id']."'> ".$oasd['name']." </td><td style='width: 45px;'>".date ("d.m", strtotime($oasd['date_open']))." </td>";  
					
					$whoOpener = whoOpener ($dbcnx, $oasd['id']);
					if (isset($whoOpener[0]['user_name'])) {
						echo "<td style='width: 420px;'>";
						foreach ($whoOpener as $wo) {
							$secondname = explode(" ", $wo['user_name']);
							echo $secondname[0]." ";
						}
					} else {
						echo "<td style='width: 420px;color: #f44336'>";
						echo "данных нет";
					}
					echo "</td>";
					echo "</tr>";
					$i++;
				}
				echo "</table>";
				echo "<br>";
				$year--;
			}
			echo "</center></div></div>";	
		}
/////////////////////////////////////////////////////////////////////////////////////////		
/////////////////////// КАРТОЧКА С ИНФОРМАЦИЕЙ О МАГАЗИНЕ ПЕЛИКАН ///////////////////////		
/////////////////////////////////////////////////////////////////////////////////////////	
		elseif (isset($id_object)) {	
			preg_match( '/Тольятти/sui' , $address , $tlt ); //+
			if (isset($tlt[0])) {
				mysql_query("UPDATE `object` SET `gorod`='TLT' WHERE `id`='".$id_object."' ",$dbcnx);
			}
			preg_match( '/Самара/sui' , $address , $smr ); //+
			if (isset($smr[0])) {
				mysql_query("UPDATE `object` SET `gorod`='SMR' WHERE `id`='".$id_object."' ",$dbcnx);
			}
			preg_match( '/Жигулевск/sui' , $address , $zhg ); //+
			if (isset($zhg[0])) {
				mysql_query("UPDATE `object` SET `gorod`='ZHG' WHERE `id`='".$id_object."' ",$dbcnx);
			}
			preg_match( '/Сызрань/sui' , $address , $szn ); //+
			if (isset($szn[0])) {
				mysql_query("UPDATE `object` SET `gorod`='SZN' WHERE `id`='".$id_object."' ",$dbcnx);
			}
			preg_match( '/Кинель/sui' , $address , $knl ); //+
			if (isset($knl[0])) {
				mysql_query("UPDATE `object` SET `gorod`='KNL' WHERE `id`='".$id_object."' ",$dbcnx);
			}
///////////////////////////////////////////////////////////////////////////////////////////////////////////			
/////////////////////// ОБЪЕКТ ОТКРЫТ ////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
			if (in_array($userdata['access'], array(3, 4, 5, 6, 7, 8, 9, 10))) {
				
				echo "<div class='link'>";
				if ($type == 'МИНДАЛЬ') {				
					echo "<a href='ip_mindal' title='Магазины'>";
				} elseif (($type == 'ОФИС') or ($type == 'СКЛАД')) {				
					echo "<a href='ip_office' title='Магазины'>";		
				} else {
					echo "<a href='ip_pelican' title='ОФис'>";
				}
				echo "<img width='20px' src='img/back_arrow.png'></a>";
				
				if ($userdata['id_group'] == "1") {
					echo "<a href='/card?id=".$id_object."&log'><img src='img/log.png' width='20px' title='log статистика изменений по объекту'></a>";
				}
				echo "</div>";
				echo "<div class='card'><center><b>";
				
				if (($type == 'МИНДАЛЬ') or (($type == 'ПЕЛИКАН'))) {				
					echo "МАГАЗИН ".$name; 
				} elseif (($type == 'ОФИС') or ($type == 'СКЛАД')) {				
					echo $name;		
				}
					if ($area == 'SMR') echo " | САМАРА";	
					elseif ($area == 'KNL') echo " | КИНЕЛЬ";
					elseif ($area == 'ZHG') echo " | ЖИГУЛЕВСК";
					//elseif ($area == 'SZR') echo " | СЫЗРАНЬ";
					elseif ($area == 'SMR_OBL') echo " | САМ.ОБЛ.";
					elseif ($area == 'TLT') echo " | ТОЛЬЯТТИ";
					if ($open == '2') { echo " | НА ОТКРЫТИИ"; }
					if ($open == '0') { echo " | ЗАКРЫТ"; }
				echo "</b></center><br>";
				
				if ($open == 2) { // ЕСЛИ ОБЪЕКТ НА ОТКРЫТИИ
					if (in_array($userdata['access'], array( 9, 10))) {	
						echo "<center>";
						echo "<form name='opennow' method='POST' action='card?id=".$id_object."' style='DISPLAY: inline'>";
						echo "<input type='submit' name='opennow' value='Магазин открылся'>";
						echo "</form>";
						echo "</center>";
					}
				}
				
				$selectInternetInObject = selectInternetInObject($dbcnx, $id_object);			
				echo "<div class='gmaps'>";
				$dop_form = dop_form ($dbcnx, $id_object);
				if ($dop_form[0]['cafe'] == 1) {
					echo "<img width='25px' src='img/pcafe.png' title='ПелиКафе'><br>"; 
				}
				if ($dop_form[0]['bakery'] == 1) {
					echo "<img width='25px' src='img/хлеб.png' title='Пекарня'><br>"; 
				}
				if ($dop_form[0]['production'] == 1) {
					echo "<img width='25px' src='img/производство.png' title='Производство'><br>"; 
				}
				echo "<a href=/map?id=".$id_object." target='_blank' ><img class='block__gmaps' width='32px' src='img/gmlogo.png' title='найти в google maps'></a><br>";
				if (isset($selectInternetInObject[0]['name'])) {		
					echo "<img class='block__gmaps' width='32px' height='32px' src='img/".$selectInternetInObject[0]['name'].".png' title='".$selectInternetInObject[0]['name']." | ".$selectInternetInObject[0]['phone']."'>";
				}
				echo "</div>";
				
				
				echo "Адрес объекта: <b>".$address."</b><br>";
				echo "Удаленность от гл.офиса: ";
					if(!empty($sh['distance'])) { echo "<b>".$sh['distance']." км </b>"; } 
					else { echo "<b title='значение выставляется в ручную'>не определена </b>"; };
				echo "<br>";
				if ($type == 'ПЕЛИКАН') {
					echo "Электронный адрес: <b>";
					$emailInObject = emailInObject ($dbcnx, $id_object); 
					foreach ($emailInObject as $eio) {
						echo "<a href='mailto:".$eio['email']."'>".$eio['email']."</a> ";
					}
					echo "</b><br>";

					$phoneInObject = phoneInObject ($dbcnx, $id_object);
					echo "Телефонный номер: <b>"; 
					foreach ($phoneInObject as $pio) {
						echo " ".$pio['number'];		
					}
					echo "</b><br>";
					
					$selectSouthConfInObject = selectSouthConfInObject ($dbcnx, $id_object);
					
					if (in_array($userdata['access'], array(5, 6, 7, 8, 9, 10))) {	
						echo "COMM магазина: <b>".$selectSouthConfInObject['COMM']."</b><br>";
						echo "Группа чеков: <b>".$selectSouthConfInObject['group_chk']."</b><br>";
					}
				}
				if (in_array($userdata['access'], array(5, 6, 7, 8, 9, 10))) {	
					$mask = selectInternetMaskInObject ($dbcnx, $id_object);				
					if (empty($mask)) { $mask = '0.0.0'; }
					echo "Маска подсети: <b>".$mask."</b> <br>";
				}
				if ($type == 'ПЕЛИКАН') {
					echo "Время работы: <b>".date("G:i", strtotime($selectSouthConfInObject['omIn']))."</b> - <b>".date("G:i", strtotime($selectSouthConfInObject['omOut']))."</b><br>";
				}
					echo "Тех.поддержка: ";
						if($sh['ttp'] == 1) { echo "<b>Тольятти</b>"; } 
						else { echo "<b>Самара</b>"; };
					echo "<br>";
			}
///////////////////////////////////////////////////////////////////		
//////////////////////////// АДМИНИСТРАТОР МАГАЗИНА
			if ($type == 'ПЕЛИКАН') {
				if (in_array($userdata['access'], array(3, 4, 5, 6, 7, 8, 9, 10))) {	
					echo "<br><br><center><b>СОТРУДНИКИ МАГАЗИНА</b></center><br>";
					
					$selectEmployees = selectEmployees ($dbcnx, $id_object);
					echo "<details class='details'>";
						echo "<summary>Развернуть список сотрудников</summary>";
					foreach ($selectEmployees as $es) {
						echo "&nbsp;&nbsp;&nbsp; ".$es['name'].": <b>".$es['fio']."</b><br>";
					}
					echo '</details><br>';	
					//$selectCod1C = selectCod1C ($dbcnx, $id_object);
					//echo "Директор: <b>".$selectCod1C['fio']."</b><br>";
					//echo "Номер: <b>".$selectCod1C['phone']."</b><br>";
				}
			}
//////////////////////////// ЕГАИС	
			if (in_array($userdata['access'], array(4, 5, 6, 7, 8, 9, 10))) {				
				echo "<br><center><b>ЕГАИС</b></center><br>";			
				$selectEgais = selectEgais ($dbcnx, $id_object);
				
				echo "Номер RSA: <b>".$selectEgais['rsa']."</b> <a href='https://check1.fsrar.ru/' target='_blank'><img width='10px' style='padding-top:3px' src='img/egais_logo.png' title='Проверка TTN'></a><br>"; 
				echo "Номер КПП: <b>".$selectEgais['kpp']."</b><br>";
		
				$selectAtolInObject = selectAtolInObject ($dbcnx, $id_object);
				$ipAtolInObject = trim($selectAtolInObject['ip']);
				
				echo "IP адрес ключа: ";
				if (!empty($selectAtolInObject)) {
					exec('timeout.sh -t 1 telnet '.$ipAtolInObject.' 8080', $output);
					if (isset($output[2])) {
						preg_match('/(Escape\scharacter)/', $output[2], $new_output);
					}
					
					if (in_array($userdata['access'], array(4))) {	
						echo "<b><a href='http://".$ipAtolInObject."/docs/' target='_blank'>".$ipAtolInObject."</a></b> ";
					} else { echo "<b>".$ipAtolInObject."</b> "; }
					
					if (isset($new_output[1])){
						echo "<a href='http://".$ipAtolInObject.":8080/' target='_blank'><img width='10px' src='img/online.png' title='EГАИС ONLINE' style='vertical-align: middle;'></a>"; 
					} else {
						echo "<a href='http://".$ipAtolInObject.":8080/' target='_blank'><img width='10px' src='img/offline.png' title='EГАИС OFFLINE' style='vertical-align: middle;'></a>"; 
					}
					
					if (!empty($selectEgais['bild_number'])) {
						echo " [ ".$selectEgais['bild_number']." ] | ";
					} else { 
						echo " [ 0000 ] | ";
					}
					
					echo "<form method='POST' action='service/atol_reboot?id=".$id_object."' style='DISPLAY: inline'>";
					echo "<input type='image' name='confirmAtol' class='image' width='9px' value='".$id_object."' src='img/reboot.png' title='Перезагрузка АТОЛ' onclick=\"return confirmAtol();\"/>";
					echo "</form>";
					//echo " | <a href='card?id=".$id_object."&eg'> ЕГАИС </a>"; 
					echo "<br>";
					
					echo "Идентификатор АТОЛ: <b>".$selectAtolInObject['identifier']."</b><br>";
					echo "Тип ключа: <b>".$selectAtolInObject['token']."</b><br>";
				}
				if ($userdata['id_group'] == "1") {
					echo "<br><div class='edit'><a href='editcard?id=".$id_object."&e=1' title='Пеликан'> edit </a></div>";
				}
			}
////////////////////////////////////////////////////////////////////
/////////////////////// МАГАЗИН В СТАДИИ ОТКРЫТИЯ (РЕМОНТА)	- НАЧАЛО
			/*
			if ($open == 2) {			
				echo "</div>";
				echo "<hr noshade size='4'><br>";
				echo "<div class='card'>";
				
				echo "<center><b>"; 
				if (isset($dateOpen)) {
					echo "ОТКРЫТИЕ МАГАЗИНА ЗАПЛАНИРОВАНО НА "; 
				}
				else {
					echo "ДАТА ОТКРЫТИЯ НЕ ИЗВЕСТНА"; 
				}
				echo "</b></center><br>";	

				echo "<form name='' method='post' id='example_group2'>";
			
				$allDeport = allDeport ($dbcnx); 
				
				echo "<body onload=\"slider('slider',0)\"><div id='intro'><p><div id='slider'>";
				$t=1;
				foreach ($allDeport as $allDep) {					
					echo "<b>● ".$allDep['deportament']."</b><br>";
					$id_deport = $allDep['id'];

					$opening = opening ($id_deport, $dbcnx);
			
					foreach ($opening as $open) {
						$openId = $open['id'];
						$openProducts = $open['products'];
						$openIdDeport = $open['id_deport'];
						
						$id_parents = $openId;
						$countProductsChild = countProductsChild ($id_parents, $dbcnx);
						$ChildColumn = $countProductsChild[0]["count(*)"]; // Количество дочерних пунктов в родительском столбце
						echo "<div class='header' id='".$t."-header'>";
						$gotov = '0';
						if ($gotov == $ChildColumn) {
							echo "<b style='color: green;'> ★ </b> ";
						}
						else {
							echo "<b style='color: red;'> ☆ </b> ";
						}
						echo $openProducts." [ <b>0 из ".$ChildColumn." </b> ]</div>"; 
						$openingSelectParents = openingSelectParents ($id_parents, $dbcnx);
						
						echo "<div class='content' id='".$t."-content'><div class='text'>";
							foreach ($openingSelectParents as $childProducts) {
								
								$OIdchild = $childProducts['id'];
								$OCProducts = $childProducts['products'];
								echo " &emsp;&emsp; <input type='checkbox' name='checkbox[]' value=".$OIdchild."> ".$OCProducts." ".$OIdchild."<br>";
							}
						echo "</div></div>";
						$t++;
					}
					echo "<br>";		
				}
				echo "</div></p></div></body>";
				echo "</form>";
				echo "<form name='' method='post' id='example_group2'>";
				echo "<input type='text' name='newproduct' placeholder='новый пункт' size='18'>";
				
				$openingParents = openingParents ($dbcnx);
				//var_dump($openingParents);
				echo "<select size='1' name='type'>";
						echo "<option value='0'>это родительский тип</option>";
					foreach ($openingParents as $OP) {
						echo "<option value='".$OP['id']."'>".$OP['products']."</option>";
					}
				echo "</select>";
				echo "<input type='submit' name='subAddProduct' value='Добавить' size='2'>";		
				echo "</form>";

				if ($userdata['access'] > 8){
					echo "<br><center>";
					echo "<p><input type='button' onclick=javascript:window.location='card?id=".$id_object."&open' value='Магазин открылся'/></p>";
					echo "</center>";
				}
				echo "</div>";
				echo "<br><hr noshade size='4'>";
				echo "<div class='card'>";
			}
			*/
//////////////////////////// МАГАЗИН В СТАДИИ ОТКРЫТИЯ (РЕМОНТА) - КОНЕЦ 
///////////////////////////////////////////////////////////////////		
//////////////////////////// ФОТО С ВИДЕОРЕГИСТРАТОРОВ
			if ((in_array($userdata['access'], array(3, 5, 6, 7, 8, 9, 10))) or ($userdata['username'] == 'sb')) {
				echo "<br><hr>";
				
				$videoCamSelectInObject = videoCamSelectInObject ($dbcnx, $id_object);
				?>
				<script>
				var mask='<?php echo $mask?>';
				var id_object='<?php echo $id_object?>';

				// СКРЫТИЕ БЛОКА ВИДЕОКАМЕР
				function showHide(element_id) {
					var obj = document.getElementById(element_id); 
					if (obj.style.display != "block") { 
						obj.style.display = "block"; //Показываем элемент
						
						   var opts = {
							lines: 13, // Число линий для рисования
							length: 7, // Длина каждой линии
							width: 2, // Толщина линии
							scale: 1.5,
							opacity: 0.05,
							radius: 5, // Радиус внутреннего круга
							corners: 1, // Скругление углов (0..1)
							rotate: 0, // Смещение вращения
							direction: 1, // 1: по часовой стрелке, -1: против часовой стрелки
							color: '#9e9e9e', // #rgb или #rrggbb или массив цветов
							speed: 0.8, // Кругов в секунду
							trail: 30, // Послесвечение
							shadow: false, // Тень(true - да; false - нет)
							hwaccel: true, // Аппаратное ускорение
							className: 'spinner', // CSS класс
							zIndex: 9999, // z-index (по-умолчанию 2000000000)
							top: '-27px', // Положение сверху относительно родителя
							left: '20%', // Положение слева относительно родителя
							position: 'relative' // Element positioninggjplyj
							
						   };
						   var target = document.getElementById('block_id');
						   var spinner = new Spinner(opts).spin(target);
								
						$.ajax({			
							type: "POST",			
							url: "video.php",
							data: {
								'id_object': id_object,
								'mask': mask 
							},		
							success: function(html){  
								$("#block_id").html(html);  
							}  
						}); 
					}
					else {
						obj.style.display = "none"; //Скрываем элемент
					}	
				}
				
				function refreshCam(id, id_object) {
					var opts = {
						lines: 13, // Число линий для рисования
						length: 9, // Длина каждой линии
						width: 2, // Толщина линии
						scale: 1.5,
						opacity: 0.05,
						radius: 5, // Радиус внутреннего круга
						corners: 1, // Скругление углов (0..1)
						rotate: 0, // Смещение вращения
						direction: 1, // 1: по часовой стрелке, -1: против часовой стрелки
						color: '#fff', // #rgb или #rrggbb или массив цветов
						speed: 0.8, // Кругов в секунду
						trail: 30, // Послесвечение
						shadow: false, // Тень(true - да; false - нет)
						hwaccel: true, // Аппаратное ускорение
						className: 'spinner', // CSS класс
						zIndex: 9999, // z-index (по-умолчанию 2000000000)
						top: '50px', // Положение сверху относительно родителя
						left: '2.5%', // Положение слева относительно родителя
						position: 'fixed' // Element positioninggjplyj
					};
				   var target = document.getElementById('cam');
				   var spinner = new Spinner(opts).spin(target);
					
					$.ajax({			
						type: "POST",			
						url: "refreshCam.php",
						data: {
							'id_reg': id
						}	 
					}).done(function(){
						setTimeout(function(){	
							selectCam(id_object);
						}, 100);	
					});
				}
				</script>
				<?php
					echo "<td id='cam'></td>";
				if (isset($videoCamSelectInObject[0])) {
					echo "<br><center><b>▼▼▼ ВИДЕОКАМЕРЫ</b> <a href=\"javascript:void(0)\" onclick=\"showHide('block_id')\">[Показать \ Скрыть]</a> ▼▼▼</center><br>";
					echo "<div id='block_id'></div>";	
				} else {
					echo "<br><center><b>ВИДЕОКАМЕРЫ</b></center><br>";
				}	
				
				//echo "<br><center><b>ВИДЕОКАМЕРЫ</b></center>";
			}
//////////////////////////// ПОЛЬЗОВАТЕЛИ ДОМЕНА МАГАЗИНА
			if (in_array($userdata['access'], array(5, 6, 7, 8, 9, 10))) {	
				echo "<hr><br><center><b>ДОМЕННЫЕ ПОЛЬЗОВАТЕЛИ НА ТЕРМИНАЛАХ"; 
				echo " [ ";
				echo "<form name='domain' method='POST' action='card?id=".$id_object."' style='DISPLAY: inline'>";
					echo "<input type='image' name='submitAllUnblock' class='image' width='12px' value='".$id_object."' src='img/open.png' title='Открыть пользователям магазина вход в SOUTH' onclick=\"return confirmAllUnblock();\"/>";
				echo " | ";
					echo "<input type='image' name='submitAllBlock' class='image' width='12px' value='".$id_object."' src='img/close.png' title='Закрыть пользователям магазина вход в SOUTH' onclick=\"return confirmAllBlock();\"/>";
				echo " | ";
					echo "<input type='image' name='submitOutAllUser' class='image' width='10px' value='".$id_object."' src='img/delete.png' title='выгнать пользователей магазина из программы SOUTH' onclick=\"return confirmOutAllUser();\"/>";
				echo "</form>";
				echo " ]";
				echo "</b></center><br>";

				$selectDomainUserInObject = selectDomainUserInObject ($dbcnx, $id_object);
				if (count($selectDomainUserInObject) > 10) {
						echo "<details class='details'>";
						echo "<summary>Развернуть список сотрудников</summary>";	
				}
						echo "<table>";			
						foreach ($selectDomainUserInObject as $sdu) {
							echo "<tr><td style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left; width: 460px;'>";
							$domain_username = $sdu['username'];
							$run = $sdu['run'];
							$sessionUserStat = sessionUserStat ($dbcnx, $domain_username);
							$ipterm = $sessionUserStat[0]['ip']; $name_comp = $sessionUserStat[0]['name_comp']; $idterm = $sessionUserStat[0]['idterm'];
							
							$date = date('H:i:s d.m.Y', strtotime($sessionUserStat[0]['datetime']));
							// echo $sessionUserStat[0]['run']." ";
							if (isset($sessionUserStat[0]['id'])) {
								if ($sessionUserStat[0]['run'] > '0') {
									echo "<img width='10px' src='img/online.png' title='Вход в ".$date."'>";
								} else {
									echo "<img width='10px' src='img/offline.png' title='OFFLINE'>";
								}
							} else {
								echo "<img width='10px' src='img/offline.png' title='OFFLINE'>";
							}
							
							echo " <b>";
							if (!empty($domain_username)) { echo "<a href='card?id=".$id_object."&du=".$domain_username."' title='статистика подключений пользователя к терминалам'>".$domain_username."</a>"; } 
							else { echo $sdu['fio']; }
							
							echo "</b> на тер. <b><a href='terminals?inf=".$idterm."' title='страница терминала'><b>".$ipterm."</b></a> ";
							echo "</td><td>";
							
							/////// ОТКРЫТ ИЛИ ЗАКРЫТ ДОСТУП ПОЛЬЗОВАТЕЛЯ 
							echo "<form name='domain' method='POST' action='card?id=".$id_object."' style='DISPLAY: inline'>";
							echo "<input name='domain_user' type='hidden' size='25' value='".$domain_username."'>";
							if ($run == '1') {
								echo "<input type='image' name='submit_block1' class='image' width='12px' value='".$domain_username."' src='img/open.png' title='закрыть пользователю вход в SOUTH' onclick=\"return confirmBlock1();\"/>";
							}
							else {
								echo "<input type='image' name='submit_block0' class='image' width='12px' value='".$domain_username."' src='img/close.png' title='открыть пользователю вход в SOUTH' onclick=\"return confirmBlock0();\"/>";
							}
							echo "</form>";
							

							if ((isset($sessionUserStat[0]['id'])) and ($sessionUserStat[0]['run'] == '1')) {
								echo "<form name='domain' method='POST' action='card?id=".$id_object."' style='DISPLAY: inline'>
								<input name='domain_user' type='hidden' size='25' value='".$domain_username."'><input type='image' name='submit_du' class='image' width='10px' value='".$domain_username."' src='img/delete.png' title='выгнать пользователя из программы SOUTH' onclick=\"return confirmDelete();\"/></form>";
							}

							echo "<br></td></tr>";
						}
						echo "</table>";
				if (count($selectDomainUserInObject) > 10) {
						echo "</details>";
				}
				// Редактирование данных о доменных пользователях
				if ($userdata['id_group'] == "1") {
					echo "<br><div class='edit'><a href='editcard?id=".$id_object."&e=9' title='добавление доменных пользователей'> edit </a></div>";
				}
//////////////////////////// ЗАЯВКИ	
				$last_ticket = last_ticket($dbcnx04, $emailInObject[0]['email']);					
				if (count($last_ticket) > 0) { 
					echo "<br><br><center><b>ПОСЛЕДНИЕ ЗАЯВКИ</b></center><br>";			
					foreach ($last_ticket as $ticket) {
					//	if (isset($ticket['firstname'])) $firstname = $ticket['firstname'];
					//	if (isset($ticket['lastname'])) $lastname = $ticket['lastname'];
						$ticketid = $ticket['ticketID']; $subject = $ticket['subject']; $status = $ticket['status'];	
						echo " ";
						if ($status == 'closed') echo "<img width='10px' src='img/offline.png' title='заявка закрыта'>"; // +  
						else echo "<img width='10px' src='img/online.png' title='заявка открыта'>"; // × 
						echo " [<a href='http://support.neo63.ru/scp/tickets.php?id=".$ticketid."'>".$ticketid."</a>] <b>Тема:</b> '".$subject."' ";
					//	if (($status == 'closed') and (isset($lastname)) and (isset($firstname))) {
					//		echo "| <b>закрыл:</b> ".$lastname." ".$firstname.""; 
					//	}
						echo "<br>";
					}
				}
				echo "<br>";
//////////////////////////// ИНТЕРНЕТ
				echo "<hr><br><center><b>ИНТЕРНЕТ ДАННЫЕ</b></center><br>";			
				$allSelectInternetInObject = allSelectInternetInObject($dbcnx, $id_object);
				foreach ($allSelectInternetInObject as $int) {	 	
					echo "Внешний IP: ".$int['ext_ip']."<br>";
					echo "Номер подключения: ".$int['agreement']."<br>";
					echo "Номер договора: ".$int['contract']."<br>";
					echo "Провайдер: ";
					if ($int['id_isp'] != '0') {
						$selectProviders = selectProviders ($dbcnx, $int['id_isp']);
						echo "<b>".$selectProviders['name']."</b>&nbsp;<img width='14px' src='img/".$selectProviders['name'].".png' title='".$selectProviders['name']." | ".$selectProviders['phone']."'>";
					}	
					echo "<br>";
					echo "Тип подключения: ".$int['type']."<br>";
					
					$selectRouterModel = selectRouterModel ($dbcnx240, $mask);
					echo "Тип Роутера: ".$selectRouterModel['model']."<br>";
					
					if ((isset($int['note'])) and ($int['note'] != '0')) echo "Заметка: ".$int['note']."<br>";
					echo "<br>";
				}
				// Редактирование данных о рабочий станциях
				if ($userdata['id_group'] == "1") {
					echo "<br><div class='edit'><a href='editcard?id=".$id_object."&e=2' title='Пеликан'> edit </a></div>";
				}
//////////////////////////// РАБОЧИЕ СТАНЦИИ
				echo "<hr><br><center><b>РАБОЧИЕ СТАНЦИИ</b></center><br>";
				$typeWS=0; $s=1; $selectWsInObject = selectWsInObject ($dbcnx, $id_object, $typeWS);
				foreach ($selectWsInObject as $ws) {		
					$ip_ws = $ws['ip'];
					$os_ws = $ws['os'];
					echo "ПК [".$s."]: <b>".$ip_ws."</b>";

					if ($os_ws == 'Windows 7 Professional') {
						echo "&nbsp <img height='15px' src=img/win7.png title='Windows 7 Professional'> "; }
					elseif ($os_ws == 'Microsoft Windows XP') {
						echo "&nbsp <img height='15px' src=img/winxp.png title='Microsoft Windows XP'> "; }
					elseif ($os_ws == 'win8') {
						echo "&nbsp <img height='15px' src=img/win8.png title='Win8'> "; }
					elseif ($os_ws == 'Windows 10 Pro') {
						echo "&nbsp <img height='15px' src=img/win10.png title='Win10'> "; }
					elseif ($os_ws == 'LINUX') {
						echo "&nbsp <img height='15px' src=img/lin.png title='Linux'> "; }
					elseif ($os_ws == 'Apple') {
						echo "&nbsp <img height='15px' src=img/mac.png title='Apple'> "; }							
					elseif ($os_ws == 'ATOL') {
						echo "&nbsp <img height='15px' src=img/atol.png title='ATOL'> "; }
					
					if (!empty($ws['title'])) {
						echo "<b>[ ".$ws['title']." ]</b>"; 
					}	
						
					echo "<br>"; $s++;
				}
				echo "<br>";
				if($userdata['id_group'] == "1") {
					echo "<br><div class='edit'><a href='editcard?id=".$id_object."&e=3' title='Редактирование'> edit </a></div>";
				}
//////////////////////////// DHCP
				if ($type != 'МИНДАЛЬ') {
					if (isset($mask) and ($mask != '')) {
						$s=1; $selectDHCPleases = selectDHCPleases ($dbcnx240, $mask);	
						if (count($selectDHCPleases) > 0) { 
							echo "<br><center><b>DHCP</b></center><br>";
							echo "<table style='margin: auto;width: 530px;color: #666;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-weight: bold;font-size: 14px;line-height: 130%; text-indent: 0.10em;'>";	
							if (count($selectDHCPleases) > 0) foreach ($selectDHCPleases as $a) {
								echo "<tr style='background-color: rgba(158, 158, 158, 0.34);'><td align=center>".$s++."</td><td align=center>".$a['ip']."</td><td align=center>".$a['mac']."</td><td align=center title='".$a['name']."'>".$a['type']."</td></tr>";
							}
							echo "</table>";
						}
					}
				}
//////////////////////////// КАССЫ	
				if (($type == 'ПЕЛИКАН') or ($type == 'МИНДАЛЬ')) {
					echo "<hr><br><center><b>КАССЫ</b></center><br>";
					$typeWS=1; $s=1; $selectWsInObject = selectWsInObject ($dbcnx, $id_object, $typeWS);
					foreach ($selectWsInObject as $ws) {
						$selectPinpadInObject = selectPinpadInObject ($dbcnx, $ws['id']);						
						echo "Кacca [".$s."]: <b>".$ws['ip']."</b> ";	
						//if ($userdata['username'] == "vea") {
							
							if ($selectPinpadInObject[0]['pinpad_id'] > '1'){
								if (!empty($selectPinpadInObject[0]['pinpad_model'])) {
									echo $selectPinpadInObject[0]['pinpad_model']." | ";
								}
								if (!empty($selectPinpadInObject[0]['pinpad_os_version'])) {
									echo $selectPinpadInObject[0]['pinpad_os_version']." | ";
								}
								if (!empty($selectPinpadInObject[0]['pinpad_id'])) {
									echo $selectPinpadInObject[0]['pinpad_id']." | ";
								}								
							}
						//}
						//if ($puppet_install == '1') { echo "&nbsp <img height='15px' src=img/puppet_small.png title='Puppet Installed'>"; }
						if ($ws['udev'] == '1') {
							echo " <img width='10px' src='img/udev_on.png' title='UDEV ON' style='vertical-align: middle;'>"; 
						} else {
							echo " <img width='10px' src='img/udev_off.png' title='UDEV OFF' style='vertical-align: middle;'>"; 
						}
						/*
						$scannersInWs = scannersInWs ($dbcnx, $id_ws);
						foreach ($scannersInWs as $scan) {
							if ($scan['model'] == '7580g') { echo "<img width='15px' title='Honeywell 7580g' src='img/7580g.png'>"; }
							elseif ($scan['model'] == '1450g') { echo "<img width='15px' title='Honeywell 1450g' src='img/1450g.png'>"; }
						}
						*/
						$vesInWs = vesInWs ($dbcnx, $ws['ip']);
						if (!empty($vesInWs)) {
							if ($vesInWs == 'Shtrih Slim') { echo " <b title='Штрих Slim' style='font-size: 16px;'>Ⓢ</b>"; }
							elseif ($vesInWs == 'Digi 708') { echo " <b title='DIGI 708' style='font-size: 16px;'>Ⓓ</b>"; }
						}
						
						echo "<br>"; $s++;
								
					}
					// Редактирование данных о кассах
					if ($userdata['id_group'] == "1") {	
						echo "<br><div class='edit'><a href='editcard?id=".$id_object."&e=4' title='Редактирование'> edit </a></div>";
					}
				}
//////////////////////////// ТСД
				echo "<hr><br><center><b>ТСД</b></center><br>";
				$s=1;
				$tsdInObject = tsdInObject ($dbcnx240, $mask);
				if (count($tsdInObject) > 0) {
					foreach ($tsdInObject as $tsd) {	
						echo "ТСД [".$s."]: <b>".$tsd['type']." [ <a href='tsd?mac=".$tsd['mac']."'>".$tsd['mac']."</a> ]</b><br>"; $s++;
					}
				}
				echo "<br>";
				// Редактирование данных о ТСД	
				if ($userdata['id_group'] == "1") {
					//echo "<br><div class='edit'><a href='editcard?id=".$id_object."&e=11' title='Редактирование'> edit </a></div>";
				}	
//////////////////////////// ВЕСЫ	
				if ($type == 'ПЕЛИКАН') {
					echo "<hr><br><center><b>ВЕСЫ</b></center><br>";
					$s=1; $vesInObject = vesInObject ($dbcnx, $id_object);
					foreach ($vesInObject as $ves) {	
						echo "Весы [".$s++."]: <b>".$ves['ip']."</b> <input type='checkbox' onchange=\"checkProizVes(".$ves['ip'].")\";><br>";
					}
					// Редактирование данных о весах	
					if ($userdata['id_group'] == "1") {	
						echo "<br><div class='edit'><a href='editcard?id=".$id_object."&e=5' title='Редактирование'> edit </a></div>";
					}
				}
//////////////////////////// ПРИНТЕРЫ
				echo "<hr><br><div id='print_reload'><center><b>ПРИНТЕРЫ</b> <i class='fas fa-redo'></i></center></div><br>";
				
				$printersInObject = printersInObject($dbcnx, $id_object); $s=1;	 	
				foreach ($printersInObject as $printer) {		
					echo "Принтер [".$s."]: <b>".$printer['print_name']."</b> на <b>".$printer['ip']."</b><br>"; $s++;
				}
				if ($userdata['id_group'] == "1") {	
					echo "<br><div class='edit'><a href='delcard?id=".$id_object."&e=7' title='Редактирование'> edit </a></div>";
				}
//////////////////////////// ШТРИХ ЭТИКЕТКИ
				if ($type == 'ПЕЛИКАН') {
					echo "<hr><br><center><b>ШТРИХ ЭТИКЕТКИ</b></center><br>";
					$selectShtrichAll = selectShtrichAll ($dbcnx, $id_object);
					
					if (count($selectShtrichAll) > 0) {
						foreach ($selectShtrichAll as $s) {
							echo "<b>".$s['username']."</b> подключен <b>".$s['share_strh']."</b> на: ".$s['ip']."<br>";
						}
					} else { echo "нет данных по штрих этикеткам <br><br>"; }
					// Редактирование данных о штрих этикетках
					if ($userdata['id_group'] == "1") {	
						echo "<br><div class='edit'><a href='editcard?id=".$id_object."&e=7' title='Редактирование'> edit </a></div>";
					}
				}				
//////////////////////////// ВИДЕОНАБЛЮДЕНИЕ
				echo "<hr><br><center><b>ВИДЕОНАБЛЮДЕНИЕ</b></center><br>";
				$videoInObject = videoInObject ($dbcnx, $id_object);
				foreach ($videoInObject as $vid) {		
					echo "IP адрес: <b>".$vid['ip']."</b><br>";
					echo "Модель: <b>".$vid['model']."</b><br>";
					echo "Кол-во каналов: <b>".$vid['channel']."</b><br>";
					echo "Объем HDD: <b>";
					if (($vid['hdd'] == 250) or ($vid['hdd'] == 500) or ($vid['hdd'] == 750)) {
						echo $vid['hdd']." Гб";
					} elseif ($vid['hdd'] == 1000){
						echo "1 Тб";
					} else {
						echo "2 Тб";
					}
					echo "</b><br>";
					echo "Логин: <b>".$vid['login']."</b><br>";
					echo "Пароль: <b>".$vid['pass']."</b><br>";
					
					echo "<br>";
				}
				// Редактирование данных о видеонаблюдении
				if ($userdata['id_group'] == "1") {	
					echo "<br><div class='edit'><a href='editcard?id=".$id_object."&e=8' title='Редактирование'> edit </a></div>";
				}
//////////////////////////// СКАНЫ
				$scanObjectCount = scanObjectCount ($dbcnx, $id_object);
				echo "<hr><br><center><b>ДОКУМЕНТЫ МАГАЗИНА [ ".$scanObjectCount." ]</b></center><br>";
				$scanAllselectObject = scanAllselectObject ($dbcnx, $id_object);
				$v = 0;
				echo '<div class="slider">';
					echo '<div class="slider__wrapper">';
						foreach ($scanAllselectObject as $saso)	{
							$scan_hash = scan_hash ($dbcnx, $saso['filename']);
							$ScanHashDataStr = str_replace(',', ' ', $scan_hash[0]['hash']);
							$randval = rand();
							echo '<div class="slider__item">';	
								echo '<a href="/scan/resize/'.$saso['filename'].'.jpg?n='.$randval.'" class="highslide" onclick="return hs.expand(this)" title="'.$ScanHashDataStr.'">';
									if (stripos($ScanHashDataStr, 'Акт') !== false) {
										$style = 'max-width: 100px; max-height: 72px; border: 1px solid red;'; $v++;
									} else {
										$style = 'max-width: 100px; max-height: 72px';	
									}
									echo '<img src="/scan/preview/'.$saso['filename'].'.jpg?n='.$randval.'" style="'.$style.'">';
								echo '</a>';
							echo '</div>';	
						}
					echo '</div>';
					echo '<a class="slider__control slider__control_left" href="#" role="button"></a>';
					echo '<a class="slider__control slider__control_right slider__control_show" href="#" role="button"></a>';
				 echo '</div>';

				if ($v == 0) {
					echo '<p style="color: red;font-weight: bold;">Акта приема передачи нет</p>';
				}
				echo "<br>";				
				
//////////////////////////// ОТЧЕТЫ О ПРОВЕРКАХ БД
				if ($type == 'ПЕЛИКАН') {
					echo "<hr><br><center><b>ОТЧЕТЫ О ПРОВЕРКАХ БД</b></center><br>";
					$checkBD = checkBD ($dbcnx, $id_object); $i = 1;
					if (count($checkBD) > 0) {
						foreach ($checkBD as $a) {
							list($year, $month, $day) = sscanf($a['date'], "%2s %2s %2s");
							echo "[".$i++."]: <b><a href='card?id=".$id_object."&ch=".$a['id']."'>".$day.".".$month.".".$year." ";
							$pos = strpos($a['log'], 'НЕ ПРОВЕДЕНА');
							if ($pos == true) {
								echo " НЕ ПРОВЕДЕНА !!";
							} else {
								$pos1 = strpos($a['log'], '!');
								if ($pos1 == false) {
									echo " [ без ошибок ]";
								} else {
									echo " [ ошибки есть ]";
								}
							}	
							echo "</a></b><br>";
						}	
					} else { echo "данные о проверках в магазине нет"; }
					echo "<br><br>";	
				}				
//////////////////////////// КТО ОТКРЫВАЛ
				if ($type == 'ПЕЛИКАН') {
					echo "<hr><br><center><b>КТО ОТКРЫВАЛ</b></center><br>";
					$whoOpener = whoOpener ($dbcnx, $id_object); $i = 1;
					if (count($whoOpener) > 0) {
						foreach ($whoOpener as $a) {
							echo "[".$i++."]: ".$a['fio']."<br>";
						}	
					} else { echo "к магазину ответственные не закреплены <br><br>"; }		
					if ($userdata['id_group'] == "1") {
						echo "<br><div class='edit'><a href='editcard?id=".$id_object."&e=13' title='добавление ответственных'> edit </a></div>";
					}	
				}
//////////////////////////// КОНЕЦ СТРАНИЦЫ			
				echo "<tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";			
			}			
			if (in_array($userdata['access'], array(9, 10))) {	
				if ($open == '1') {
					echo "<center><p><input type='button' onclick=javascript:window.location='card?id=".$id_object."&cls' value='Закрыть магазин'/></p></center><br>";
				}
			}
			echo "</div></div>";				
			echo "<br><br>";
		} else {
			echo '<br><p><center>Такой магазин не найден</center></p><br>';	
		}	
	}
}
include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");
 ?>
 
 <script>
$(document).ready(function(){
	//var text = document.getElementById('checkBD').innerHTML;
	var text = $("#checkBD").text();
	console.log(text);
});

'use strict';
var multiItemSlider = (function () {
  return function (selector, config) {
	var
	  _mainElement = document.querySelector(selector), // основный элемент блока
	  _sliderWrapper = _mainElement.querySelector('.slider__wrapper'), // обертка для .slider-item
	  _sliderItems = _mainElement.querySelectorAll('.slider__item'), // элементы (.slider-item)
	  _sliderControls = _mainElement.querySelectorAll('.slider__control'), // элементы управления
	  _sliderControlLeft = _mainElement.querySelector('.slider__control_left'), // кнопка "LEFT"
	  _sliderControlRight = _mainElement.querySelector('.slider__control_right'), // кнопка "RIGHT"
	  _wrapperWidth = parseFloat(getComputedStyle(_sliderWrapper).width), // ширина обёртки
	  _itemWidth = parseFloat(getComputedStyle(_sliderItems[0]).width), // ширина одного элемента    
	  _positionLeftItem = 0, // позиция левого активного элемента
	  _transform = 0, // значение транфсофрмации .slider_wrapper
	  _step = _itemWidth / _wrapperWidth * 100, // величина шага (для трансформации)
	  _items = []; // массив элементов
	// наполнение массива _items
	_sliderItems.forEach(function (item, index) {
	  _items.push({ item: item, position: index, transform: 0 });
	});

	var position = {
	  getMin: 0,
	  getMax: _items.length - 1,
	}

	var _transformItem = function (direction) {
	  if (direction === 'right') {
		if ((_positionLeftItem + _wrapperWidth / _itemWidth - 1) >= position.getMax) {
		  return;
		}
		if (!_sliderControlLeft.classList.contains('slider__control_show')) {
		  _sliderControlLeft.classList.add('slider__control_show');
		}
		if (_sliderControlRight.classList.contains('slider__control_show') && (_positionLeftItem + _wrapperWidth / _itemWidth) >= position.getMax) {
		  _sliderControlRight.classList.remove('slider__control_show');
		}
		_positionLeftItem++;
		_transform -= _step;
	  }
	  if (direction === 'left') {
		if (_positionLeftItem <= position.getMin) {
		  return;
		}
		if (!_sliderControlRight.classList.contains('slider__control_show')) {
		  _sliderControlRight.classList.add('slider__control_show');
		}
		if (_sliderControlLeft.classList.contains('slider__control_show') && _positionLeftItem - 1 <= position.getMin) {
		  _sliderControlLeft.classList.remove('slider__control_show');
		}
		_positionLeftItem--;
		_transform += _step;
	  }
	  _sliderWrapper.style.transform = 'translateX(' + _transform + '%)';
	}

	// обработчик события click для кнопок "назад" и "вперед"
	var _controlClick = function (e) {
	  var direction = this.classList.contains('slider__control_right') ? 'right' : 'left';
	  e.preventDefault();
	  _transformItem(direction);
	};

	var _setUpListeners = function () {
	  // добавление к кнопкам "назад" и "вперед" обрботчика _controlClick для событя click
	  _sliderControls.forEach(function (item) {
		item.addEventListener('click', _controlClick);
	  });
	}

	// инициализация
	_setUpListeners();

	return {
	  right: function () { // метод right
		_transformItem('right');
	  },
	  left: function () { // метод left
		_transformItem('left');
	  }
	}

  }
}());

var slider = multiItemSlider('.slider')

</script>
