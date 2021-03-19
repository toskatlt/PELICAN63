<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

echo "<script src='/js/spin/spin.js'></script><br>";
echo "<script src='/js/spin/hideshow.j'></script><br>"; 

require_once("function/function_staff.php");
$date_time = date("y-m-d H:i:s");
?>
<style>
#slider {
	 width: 508px;
	 color: #66666;
	 font-family: Georgia;
	 font-size: 20px;
 }
.header {
	 width: 488px;
	 border: 1px solid #cccccc;
	 padding: 8px;
	 margin-top: 5px;
	 cursor: pointer;
	 text-align: left;
 }
.header:hover {
	color: #666666;
 }
.content {
	
 }
.text {
	 width: 474px;
	 border: 1px solid #cccccc;
	 border-top: none;
	 padding: 15px;
	 text-align: left;
	 background: #eeeeee;
	 font-size: 14px;
 }
</style>

<?php
if (isset($_GET['id'])) $id_object = $_GET['id'];
if (isset($_GET['email'])) {
	$pos = strpos($_GET['email'], 'mindal');
	if ($pos == true) { ?> <script> location.replace("/ip_mindal.php"); </script> <?php }
	
	if(strpos($_GET['email'], 'neo.rus') == true) {
		$_GET['email'] = str_replace('neo.rus', 'neo63.ru', $_GET['email']);
	} 
	$objectFromEmail = objectFromEmail($dbcnx, $_GET['email']);
	$id_object = $objectFromEmail['id'];	
	
}


if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	$uname = $userdata['id'];
	if ($userdata['access'] > 3) {
		// ЕСЛИ ПРИНТЕР ШТРИХ ЭТИКЕТОК НЕ БОЛЕЕ 1-ГО В МАГАЗИНЕ, ОН СТАНОВИТЬСЯ ПО УМОЛЧАНИЮ 
		$selectDomainUserInObjectNotTSD = selectDomainUserInObjectNotTSD ($dbcnx, $id_object);
		$printersShtrihInObject = printersShtrihInObject ($dbcnx, $id_object);
		if (count($printersShtrihInObject) == 1) {
			foreach ($selectDomainUserInObjectNotTSD as $a) {
				mysql_query("INSERT INTO `shtrich`(`id_object`, `id_user`, `ip`, `share_strh`) VALUES ('".$id_object."','".$a['id']."','".$printersShtrihInObject[0]['ip']."','".$printersShtrihInObject[0]['print_name']."') ON DUPLICATE KEY UPDATE `ip`='".$printersShtrihInObject[0]['ip']."', `share_strh`='".$printersShtrihInObject[0]['print_name']."'", $dbcnx);
			}
		}
		if(isset($_POST['submit_du'])) {
			$u = userListSelect ($dbcnx, $_POST['domain_user']);
			if (isset($u['name_user'])){
				mysql_query("INSERT INTO `kill_list`(`id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run`) VALUES ('".$u['id']."','".$u['ip']."','".$u['datetime']."','".$u['name_comp']."','".$u['name_user']."','".$u['domain_name']."','".$u['password']."','1')", $dbcnx);
		////   LOG   ///////////////////////////////
				$update_log = "KILL_USER: пользователь ".$_POST['domain_user']." ";
				mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'kill_user', '".$id_object."', '".$update_log."')", $dbcnx);
			}
		}
		if(isset($_POST['submit_block1'])) {
			$du = $_POST['domain_user'];
			$update = "UPDATE `domain_users` SET `run`='0' WHERE `username`='".$du."'";
			mysql_query($update,$dbcnx);
		////   LOG   ///////////////////////////////	
			$update_log = "BLOCK_USER: пользователь ".$du." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['user_login']."', '".$date_time."', 'kill_user', ' - ', '".$update_log."')";
			mysql_query($log,$dbcnx);			
		}
		if(isset($_POST['submit_block0'])) {
			$du = $_POST['domain_user'];
			$update = "UPDATE `domain_users` SET `run`='1' WHERE `username`='".$du."'";
			mysql_query($update,$dbcnx);
		////   LOG   ///////////////////////////////	
			$update_log = "UNBLOCK_USER: пользователь ".$du." ";
			$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$userdata['user_login']."', '".$date_time."', 'domain_users', ' - ', '".$update_log."')";
			mysql_query($log,$dbcnx);		
		}
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
			mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$userdata['username']."','".$date_time."','ws','".$id_object."','".$update_log."')",$dbcnx);	
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
/////////////////////////////////////////////////////////////////////////////////////////				
/////////////////////// ЛОГИ ДОМЕННЫХ ПОЛЬЗОВАТЕЛЕЙ МАГАЗИНОВ - НАЧАЛО //////////////////	
/////////////////////////////////////////////////////////////////////////////////////////	
		echo "<br><br><br>";
			$sh = selectObject ($dbcnx, $id_object);
			$id_building = $sh['id_building']; 
			$type = $sh['type']; 
			$name = $sh['name']; 
			$date_open = $sh['date_open'];
			$open = $sh['open'];
			$address = $sh['address'];
			$area = $sh['area'];
		if ($open == '2') { echo "<div id='card_body' style='width: 670px;background: -webkit-linear-gradient(top, #fad4ff, #cae6f3);'>"; }
		else { echo "<div id='card_body' style='width: 670px;'>"; }
		
		if ((isset($id_object)) and (isset($_GET['du']))) {
			echo "<div class='link'><a href='card?id=".$id_object."' title='Назад к объекту'><img width='20px' src='img/back_arrow.png'></a></div>";
			echo "<div class='card'>";
			echo "<center>ЛОГИ ПОЛЬЗОВАТЕЛЯ <b>".$_GET['du']."</b><br><br><br>";
			$selectSessionUser = selectSessionUser ($dbcnx, $_GET['du']);
			foreach ($selectSessionUser as $a) {
				$maxdate = date('H:i d.m.Y', strtotime($a['max(date)']));
				echo "<p>● Терминал <b>".$a['ip_term']."</b> - последний вход <b>".$maxdate."</b><p>";
			}			
			echo "</center></div></div><br><br>";		
		} elseif ((isset($id_object)) and (isset($_GET['log']))) {
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
		}
/////////////////////////////////////////////////////////////////////////////////////////				
/////////////////////// ЛОГИ ДОМЕННЫХ ПОЛЬЗОВАТЕЛЕЙ МАГАЗИНОВ - КОНЕЦ ///////////////////		
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
				
			echo "<br><br>";	
			echo "</div></div>";	
		}
/////////////////////////////////////////////////////////////////////////////////////////				
/////////////////////// СТРАНИЦА ДЛЯ РАБОТЫ С ЕГАИС - КОНЕЦ /////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////// СТАТИСТИКА ОТКРЫТИЙ МАГАЗИНОВ - НАЧАЛО //////////////////////////	
/////////////////////////////////////////////////////////////////////////////////////////	
		elseif (isset($_GET['open'])) {
			echo "<div class='link' style='width: 780px;'><a href='card' title='Магазины'><img width='20px' src='img/back_arrow.png'></a></div>";
			echo "<div class='card'>";
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
/////////////////////// МАГАЗИН ОТКРЫТ ////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
			if ($userdata['access'] > 3) {
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
				
				if ($open == 2) { // ЕСЛИ МАГАЗИН НА ОТКРЫТИИ
					if ($userdata['access'] > 8){
							echo "<center>";
							echo "<form name='opennow' method='POST' action='card?id=".$id_object."' style='DISPLAY: inline'>";
							echo "<input type='submit' name='opennow' value='Магазин открылся'>";
							echo "</form>";
							echo "</center>";
					}
				}
				
				$selectInternetInObject = selectInternetInObject($dbcnx, $id_object);			
				echo "<div class='gmaps'>";
				$pelicafe = pelicafe ($dbcnx, $id_object);
				if ($pelicafe['count(*)'] == 1) {
					echo "<img width='25px' src='img/pcafe.png' title='ПелиКафе'><br>"; 
				}
				echo "<a href=/map?id=".$id_object." target='_blank' ><img class='block__gmaps' width='32px' src='img/gmlogo.png' title='найти в google maps'></a><br>";
				if (isset($selectInternetInObject[0]['name'])) {		
					echo "<img class='block__gmaps' width='32px' height='32px' src='img/".$selectInternetInObject[0]['name'].".png' title='".$selectInternetInObject[0]['name']." | ".$selectInternetInObject[0]['phone']."'>";
				}
				echo "</div>";
				
				if ($type == 'ПЕЛИКАН') {
					echo "Адрес магазина: <b>".$address."</b><br>";
					echo "Электронный адрес: <b>";
					$emailInObject = emailInObject ($dbcnx, $id_object); 
					foreach ($emailInObject as $eio) {
						echo "<a href='mailto:'>".$eio['email']."</a>";
					}
					echo "</b><br>";
					$phoneInObject = phoneInObject ($dbcnx, $id_object);
					echo "Телефонный номер: <b>"; 
					foreach ($phoneInObject as $pio) {
						echo " ".$pio['number'];		
					}
					echo "</b><br>";
					
					echo "Удаленность: ";
						if(!empty($sh['distance'])) { echo "<b>".$sh['distance']." км </b>"; } 
						else { echo "<b title='значение выставляется в ручную'>не определена </b>"; };
					echo "<br>";
					
					$selectSouthConfInObject = selectSouthConfInObject ($dbcnx, $id_object);
					
					if ($userdata['access'] > 4) {	
					echo "COMM магазина: <b>".$selectSouthConfInObject['COMM']."</b><br>";
					echo "Группа чеков: <b>".$selectSouthConfInObject['group_chk']."</b><br>";

					}
				}	
				if ($userdata['access'] > 4) {	
					$mask = selectInternetMaskInObject ($dbcnx, $id_object);
					echo "Маска подсети: <b>".$mask."</b> <br>";
				}
			}
///////////////////////////////////////////////////////////////////		
//////////////////////////// АДМИНИСТРАТОР МАГАЗИНА
			if ($type == 'ПЕЛИКАН') {
				if ($userdata['access'] > 3) {
					echo "<br><br><center><b>АДМИНИСТРАТОР МАГАЗИНА</b></center><br>";
					$selectCod1C = selectCod1C ($dbcnx, $id_object);
					$selectCod1C = selectCod1C ($dbcnx, $id_object);
					
					echo "Директор: <b>".$selectCod1C['fio']."</b><br>";
					echo "Номер: <b>".$selectCod1C['phone']."</b><br>";
				}
			}
//////////////////////////// ЕГАИС	
			if ($userdata['access'] > 3) {
				echo "<br><br><center><b>ЕГАИС</b></center><br>";			
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
					
					if ($userdata['access'] == 4) {
						echo "<b><a href='http://".$ipAtolInObject."/docs/' target='_blank'>".$ipAtolInObject."</a></b> ";
					} else { echo "<b>".$ipAtolInObject."</b> "; }
					
					if (isset($new_output[1])){
						echo "<a href='http://".$ipAtolInObject.":8080/' target='_blank'><img width='10px' src='img/online.png' title='EГАИС ONLINE' style='vertical-align: middle;'></a>"; 
					} else {
						echo "<a href='http://".$ipAtolInObject.":8080/' target='_blank'><img width='10px' src='img/offline.png' title='EГАИС OFFLINE' style='vertical-align: middle;'></a>"; 
					}
					echo " [ ".$selectEgais['bild_number']." ] | ";
					echo "<form method='POST' action='service/atol_reboot?id=".$id_object."' style='DISPLAY: inline'>";
					echo "<input type='image' name='confirmAtol' class='image' width='9px' value='".$id_object."' src='img/reboot.png' title='перезагрузка АТОЛ' onclick=\"return confirmAtol();\"/>";
					echo "</form>";
					
					echo " | <a href='card?id=".$id_object."&eg'> ЕГАИС </a>"; 
					echo "<br>";		
				}
				if ($userdata['id_group'] == "1") {
					echo "<div class='edit'><a href='editcard?id=".$id_object."&e=1' title='Пеликан'> edit </a></div>";
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
			if ($userdata['access'] > "4") {
				echo "<hr>";
				if ($userdata['id_group'] == "VID") {	}
				
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
				
				if (isset($videoCamSelectInObject[0])) {
					echo "<td id='cam'></td>";
					echo "<br><center><b>▼▼▼ ВИДЕОКАМЕРЫ</b> <a href=\"javascript:void(0)\" onclick=\"showHide('block_id')\">[Показать \ Скрыть]</a> ▼▼▼</center><br>";
					echo "<div id='block_id'></div>";	
				} 
				
				//echo "<br><center><b>ВИДЕОКАМЕРЫ</b></center>";
			
//////////////////////////// ПОЛЬЗОВАТЕЛИ ДОМЕНА МАГАЗИНА
				echo "<hr><br><center><b>ДОМЕННЫЕ ПОЛЬЗОВАТЕЛИ НА ТЕРМИНАЛАХ</b></center><br>";
				
				$selectDomainUserInObject = selectDomainUserInObject ($dbcnx, $id_object);
				echo "<table>";
				foreach ($selectDomainUserInObject as $sdu) {
					echo "<tr><td style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left; width: 460px;'>";
					$domain_username = $sdu['username'];
					$run = $sdu['run'];
					
					$sessionUserStat = sessionUserStat ($dbcnx, $domain_username);
					$ipterm = $sessionUserStat[0]['ip']; $name_comp = $sessionUserStat[0]['name_comp']; $idterm = $sessionUserStat[0]['idterm'];	 
					$date = date('H:i:s d.m.Y', strtotime($sessionUserStat[0]['datetime']));
					if (isset($sessionUserStat[0]['id'])) {
						echo "<img width='10px' src='img/online.png' title='Вход в ".$date."'>";
					} else {
						echo "<img width='10px' src='img/offline.png' title='OFFLINE'>";
					}
					
					
					echo " <b>";
					if (!empty($domain_username)) { echo "<a href='card?id=".$id_object."&du=".$domain_username."' title='статистика подключений пользователя к терминалам'>".$domain_username."</a>"; } 
					else { echo $sdu['fio']; }
					
					echo "</b> на тер. <b><a href='terminals?inf=".$idterm."' title='страница терминала'><b>".$ipterm."</b></a> ";
					
					
					echo "</td><td>";
					
					//if ($userdata['access'] > 7){
						echo "<form name='domain' method='POST' action='card?id=".$id_object."' style='DISPLAY: inline'>
						<input name='domain_user' type='hidden' size='25' value='".$domain_username."'>";
						if ($run == '1') {
							echo "<input type='image' name='submit_block1' class='image' width='12px' value='".$domain_username."' src='img/open.png' title='закрыть пользователю вход в SOUTH' onclick=\"return confirmBlock1();\"/>";
						}
						else {
							echo "<input type='image' name='submit_block0' class='image' width='12px' value='".$domain_username."' src='img/close.png' title='открыть пользователю вход в SOUTH' onclick=\"return confirmBlock0();\"/>";
						}
						echo "</form>";
						if (isset($sessionUserStat[0]['id'])) {
							echo "<form name='domain' method='POST' action='card?id=".$id_object."' style='DISPLAY: inline'>
							<input name='domain_user' type='hidden' size='25' value='".$domain_username."'><input type='image' name='submit_du' class='image' width='10px' value='".$domain_username."' src='img/delete.png' title='выгнать пользователя из программы SOUTH' onclick=\"return confirmDelete();\"/></form>";
						}
					//}
					echo "<br></td></tr>";
				}
				echo "</table>";
				// Редактирование данных о доменных пользователях
				if ($userdata['id_group'] == "1") {
					echo "<div class='edit'><a href='editcard?id=".$id_object."&e=9' title='добавление доменных пользователей'> edit </a></div>";
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
					echo "Тип Роутера: ".$int['router']."<br>";
					
					if ((isset($int['note'])) and ($int['note'] != '0')) echo "Заметка: ".$int['note']."<br>";
					echo "<br>";
				}
				// Редактирование данных о рабочий станциях
				if ($userdata['id_group'] == "1") {
					echo "<div class='edit'><a href='editcard?id=".$id_object."&e=2' title='Пеликан'> edit </a></div>";
				}
//////////////////////////// РАБОЧИЕ СТАНЦИИ
				echo "<hr><br><center><b>РАБОЧИЕ СТАНЦИИ</b></center><br>";
				$typeWS=0; $s=1; $selectWsInObject = selectWsInObject ($dbcnx, $id_object, $typeWS);
				foreach ($selectWsInObject as $ws) {		
					$ip_ws = $ws['ip'];
					$os_ws = $ws['os'];
					echo "ПК [".$s."]: <b>".$ip_ws."</b>";

					if ($os_ws == 'Windows 7 Professional') {
						echo "&nbsp <img height='15px' src=img/win7.png title='Windows 7 Professional'>"; }
					elseif ($os_ws == 'Microsoft Windows XP') {
						echo "&nbsp <img height='15px' src=img/winxp.png title='Microsoft Windows XP'>"; }
					elseif ($os_ws == 'win8') {
						echo "&nbsp <img height='15px' src=img/win8.png title='Win8'>"; }
					elseif ($os_ws == 'win10') {
						echo "&nbsp <img height='15px' src=img/win10.png title='Win10'>"; }
					elseif ($os_ws == 'ATOL') {
						echo "&nbsp <img height='15px' src=img/atol.png title='ATOL'>"; }
					echo "<br>"; $s++;
				}
				echo "<br>";
				if($userdata['id_group'] == "1") {
					echo "<div class='edit'><a href='editcard?id=".$id_object."&e=3' title='Редактирование'> edit </a></div>";
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
						$id_ws = $ws['id'];				
						$ip_ws = $ws['ip'];
						$os_ws = $ws['os'];
						$puppet_install = $ws['puppet'];
						$udev = $ws['udev'];
						
						$selectPinpadInObject = selectPinpadInObject ($dbcnx, $id_ws);
						if (isset($selectPinpadInObject[0]['pinpad_model'])) $pinpad_model = $selectPinpadInObject[0]['pinpad_model'];
						if (isset($selectPinpadInObject[0]['pinpad_os_version'])) $pinpad_os_version = $selectPinpadInObject[0]['pinpad_os_version'];
						if (isset($selectPinpadInObject[0]['pinpad_id'])) $pinpad_id = $selectPinpadInObject[0]['pinpad_id'];
						
						echo "Кacca [".$s."]: <b>".$ip_ws."</b>";	
						if ($userdata['username'] == "vea") {
							if ($pinpad_id > '1'){
								echo " | ".$pinpad_model." | ".$pinpad_os_version." | ".$pinpad_id;
							}		
						}
						//if ($puppet_install == '1') { echo "&nbsp <img height='15px' src=img/puppet_small.png title='Puppet Installed'>"; }
						if ($udev == '1') {
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
						$vesInWs = vesInWs ($dbcnx, $ip_ws);
						if (!empty($vesInWs)) {
							if ($vesInWs == 'Shtrih Slim') { echo " <b title='Штрих Slim' style='font-size: 16px;'>Ⓢ</b>"; }
							elseif ($vesInWs == 'Digi 708') { echo " <b title='DIGI 708' style='font-size: 16px;'>Ⓓ</b>"; }
						}
						
						echo "<br>"; $s++;	
					}
					// Редактирование данных о кассах
					if ($userdata['id_group'] == "1") {	
						echo "<div class='edit'><a href='editcard?id=".$id_object."&e=4' title='Редактирование'> edit </a></div>";
					}
				}
//////////////////////////// ТСД
				echo "<hr><br><center><b>ТСД</b></center><br>";
				$s=1; $tsdInObject = tsdInObject ($dbcnx, $id_object);
				if (count($tsdInObject) > 0) {
					foreach ($tsdInObject as $tsd) {	
						echo "ТСД [".$s."]: <b>IMEI:</b> ".$tsd['imei']." <b>| SERIAL:</b> ".$tsd['serial']."<br>"; $s++;
					}
				}
				// Редактирование данных о ТСД	
				if ($userdata['id_group'] == "1") {
					echo "<div class='edit'><a href='editcard?id=".$id_object."&e=11' title='Редактирование'> edit </a></div>";
				}	
//////////////////////////// ВЕСЫ	
				if ($type == 'ПЕЛИКАН') {
					echo "<hr><br><center><b>ВЕСЫ</b></center><br>";
					$s=1; $vesInObject = vesInObject ($dbcnx, $id_object);
					foreach ($vesInObject as $ves) {	
						echo "Весы [".$s."]: <b>".$ves['ip']."</b><br>"; $s++;
					}
					// Редактирование данных о весах	
					if ($userdata['id_group'] == "1") {	
						echo "<div class='edit'><a href='editcard?id=".$id_object."&e=5' title='Редактирование'> edit </a></div>";
					}
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
						echo "<div class='edit'><a href='editcard?id=".$id_object."&e=7' title='Редактирование'> edit </a></div>";
					}
				}
//////////////////////////// ПРИНТЕРЫ
				echo "<hr><br><center><b>ПРИНТЕРЫ</b></center><br>";
				$printersInObject = printersInObject($dbcnx, $id_object); $s=1;	 	
				foreach ($printersInObject as $printer) {		
					echo "Принтер [".$s."]: <b>".$printer['print_name']."</b> на <b>".$printer['ip']."</b><br>"; $s++;
				}
				if ($userdata['id_group'] == "1") {	
					echo "<div class='edit'><a href='delcard?id=".$id_object."&e=7' title='Редактирование'> edit </a></div>";
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
					if ($vid['model'] != 'GV') {
						echo "Тип: <b>".$vid['type']."</b><br>";
					}
					echo "Логин: <b>".$vid['login']."</b><br>";
					echo "Пароль: <b>".$vid['pass']."</b><br>";
					
					echo "<br>";
				}
				// Редактирование данных о видеонаблюдении
				if ($userdata['id_group'] == "1") {	
					echo "<div class='edit'><a href='editcard?id=".$id_object."&e=8' title='Редактирование'> edit </a></div>";
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
						echo "<div class='edit'><a href='editcard?id=".$id_object."&e=13' title='добавление ответственных'> edit </a></div>";
					}	
				}
//////////////////////////// КОНЕЦ СТРАНИЦЫ			
				echo "<tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";			
			}			
			if ($userdata['access'] > 8) {
				if ($open == '1') {
					echo "<center><p><input type='button' onclick=javascript:window.location='card?id=".$id_object."&cls' value='Закрыть магазин'/></p></center><br>";
				}
			}
			echo "</div></div>";				
			echo "<br><br>";
		}
	}
}
include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");
 ?>