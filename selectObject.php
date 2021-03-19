<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_object.php");

echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">';

require_once("./function/function_scan.php");

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['access'] > 2) {

		$search = '';
		$userdata = authorization ($dbcnx, $_COOKIE['id']);

		if (isset($_POST['change'])) {
			$change = $_POST['change'];
		}

		if (isset($_GET['change'])) {
			$change = $_GET['change'];
		}

		echo "<div class='table2'>";
			echo "<table id='myTable' class='tablesorter'>";
				echo "<thead>";
					echo "<tr>";
						echo "<th style='width: 2%;' class='header'>№</th>";
						echo "<th style='width: 20%;' class='header'>Пеликан</th>";
						$selectHandScan = selectHandScan ($dbcnx);
						$selectAllPelicanCountObject = selectAllPelicanCountObject ($dbcnx);

						if ($userdata['access'] > '4') {
							echo "<th style='width: 2%;' class='header' title='".$selectHandScan." из ".$selectAllPelicanCountObject."'>Scn</th>";
							echo "<th style='width: 2%;' class='header'>VID</th>";
						}

						echo "<th style='width: 15%;' class='header'>Телефон</th>";

						if ($userdata['access'] > '4') {
							echo "<th style='width: 12%;' class='header'>IP адрес</th>";
							echo "<th style='width: 15%;' class='header'>Интернет</th>";
							echo "<th style='width: 10%;' class='header'>IP WAN</th>";
							// КОЛИЧЕСТВО ОБЪЕКТОВ 'FR' в таблице dhcp_leases
							$selectALLDHCPleasesCountFR = selectALLDHCPleasesCountFR ($dbcnx240);
							// КОЛИЧЕСТВО КАСС В ПАПКЕ WS
							$countAllWsLinuxInObject = countAllWsLinuxInObject ($dbcnx);
							$t = $countAllWsLinuxInObject - $selectALLDHCPleasesCountFR;
							echo "<th style='width: 3%;' class='header' title='Всего касс ".$countAllWsLinuxInObject." [".$t."]'>Кассы</th>";
						}
					echo "</tr>";
				echo "</thead>";

				/////////////////////////////////////////////////////////////////////////////			
				/////////////////////////////////////////////////////////////////////////////		

				echo "<tbody>";	
					$i = 1;	
					if (isset($_POST['search'])) {
						if ($change == 'cls') {
							$countFindsObject = findObjectByNameInCloses ($dbcnx, $_POST['search']);
							if(count($countFindsObject) > 0) {	
								$allObject = findObjectByNameInCloses ($dbcnx, $_POST['search']);
							} else {
								$switcher = switcher ($_POST['search']);
								$countFindsObject = findObjectByNameInCloses ($dbcnx, $switcher);
								if(count($countFindsObject) > 0) {	
									$allObject = findObjectByNameInCloses ($dbcnx, $switcher);
								} else echo '<center>По вашему запросу ничего не найдено</center>';
							}
						} else {
							$countFindsObject = findObjectByName ($dbcnx, $_POST['search']);
							if(count($countFindsObject) > 0) {	
								$allObject = findObjectByName ($dbcnx, $_POST['search']);
							} else {
								$switcher = switcher ($_POST['search']);
								$countFindsObject = findObjectByName ($dbcnx, $switcher);
								if(count($countFindsObject) > 0) {	
									$allObject = findObjectByName ($dbcnx, $switcher);
								} else echo '<center>По вашему запросу ничего не найдено</center>';
							}
						}
					} elseif (isset($change)) {
						if ($change != 'all') {
							if ($change == 'cls') {
								$allObject = allObjectClose ($dbcnx);
							} elseif ($change == 'nob') {
								$allObject = allObjectNoBuilding ($dbcnx);
							} elseif ($change == 'PLC') {
                                $allObject = allObjectPelicafe ($dbcnx);
                            } elseif ($change == 'PEK') {
                                $allObject = allObjectBakery ($dbcnx);
                            } elseif ($change == 'PRO') {
                                $allObject = allObjectProduction ($dbcnx);
                            } else {
								$allObject = allObjectWithArea ($dbcnx, $change);
							}		
						} else {
							$allObject = allObject($dbcnx);
						}
					}

					foreach ($allObject as $aO) {	
						$id_object = $aO['id'];
						$object_name = $aO['name']; 
						$building_address = $aO['address'];
						$area = $aO['area'];				

						echo "<tr><td align=left>".$i."</td>"; $i++;
						if ($aO['open'] == 2) {
							echo "<td align=left style='background-color: #fad4ff;'>";
						} else { echo "<td align=left>"; }
						$scanObjectAktCount = scanObjectAktCount ($dbcnx, $id_object);
						
						echo "<a href='card?id=".$id_object."' title='".$building_address."'>".$object_name;
						if ($scanObjectAktCount > 0) { 
							echo ' <i class="fas fa-copy"></i>'; 
						}					
						echo "</a> ";



						// для Зориной и Стрекаловой
						if ($userdata['access'] == '4') {
							$selectAtolInObject = selectAtolInObject ($dbcnx, $id_object); 
							$ip_atol = trim($selectAtolInObject['ip']);
							echo "<b><a href='http://".$ip_atol."/docs/' target='_blank'><img width='10px' style='padding-top:3px' src='img/atol.png'></a></b> ";
						}

						echo "</td>";
						if ($userdata['access'] > '4') {	

					////////////////////////  SCANNER ////////////////////////
							echo "<td>";
							if ($change == 'cls') {
								$query = mysql_query("SELECT count(*) as count FROM `work_atol_in_close_object` WHERE `id_object` = '".$id_object."'", $dbcnx);
								$row = mysql_fetch_assoc($query);
								
								if ($row['count'] == 1) {
									echo "<div hidden='true'>1</div><img width='10px' src='img/online.png' title='EГАИС ONLINE' style='vertical-align: middle;'>"; 
								} else {
									echo "<div hidden='true'>2</div><img width='10px' src='img/offline.png' title='EГАИС OFFLINE' style='vertical-align: middle;'>"; 
								}
							} else {
								$honeywellInObject = honeywellInObject ($dbcnx, $id_object);
								if ($honeywellInObject > 0) { echo "<div hidden='true'>1</div><img src='img/1450g.png' title='Honeywell 1450g' style='width: 19px;'>"; }
								else { echo "<div hidden='true'>0</div>"; }	
							}	
							
							echo "</td>";
					////////////////////////  VIDEO TYPE  ////////////////////////						
							$videCamSelectInObject = videCamSelectInObject ($dbcnx, $id_object);
							echo "<td> ";
							if (isset($videCamSelectInObject[0])) {
								if ($videCamSelectInObject[0]['model'] == 'RVIHD' OR $videCamSelectInObject[0]['model'] == 'RVIHDR') {
									echo "<div hidden='true'>2</div><img src='img/сamera.png' style='width: 16px;'><img src='img/hd.png' style='width: 16px;'>";
								} elseif ($videCamSelectInObject[0]['model'] == 'RVI') {
									echo "<div hidden='true'>1</div><img src='img/сamera.png' style='width: 16px;'>";
								} else {
									echo "<div hidden='true'>0</div>";
								}							
							}	
							echo "</td> ";
						}
					////////////////////////  TELEPHONE  ////////////////////////
						$phones = null; $phoneInObject = phoneInObject ($dbcnx, $id_object);
						if (isset($phoneInObject[0]['number'])) {
							foreach ($phoneInObject as $phone) {
								$phone['number'] = str_replace(" ","",$phone['number']);
								if (strlen($phone['number']) == '6') { $phones[] = preg_replace("/(\d{2})(\d{2})(\d{2})/","\\1-\\2-\\3",$phone['number']); }
								elseif (strlen($phone['number']) == '10') { $phones[] = preg_replace("/(\d{2})(\d{2})(\d{2}),(\d{3})/","\\1-\\2-\\3 д.\\4",$phone['number']); }
								elseif (strlen($phone['number']) == '12') { $phones[] = preg_replace("/\((\d{3})\)(\d{7})/","8(\\1)\\2",$phone['number']); }
							}
							$implode = implode(", ", $phones);
							echo "<td align=left>".$implode."</td>";
						} else {
							echo "<td align=left>нет данных</td>";
						}
					////////////////////////  IP адрес  ////////////////////////
						if ($userdata['access'] > '4') {
							$selectInternetMaskInObject = selectInternetMaskInObject ($dbcnx, $id_object);		
							if (isset($selectInternetMaskInObject)) {
								$piecesIPRoute = piecesIPRoute ($selectInternetMaskInObject);		
								echo "<td align=left><div hidden='true'>".$piecesIPRoute."</div> ".$selectInternetMaskInObject.".4</td> ";
							} else {
								echo "<td align=left>не задан</td>";			
							}
					////////////////////////  ИНТЕРНЕТ  ////////////////////////
							$int = selectInternetInObjectISP ($dbcnx, $id_object);
							echo "<td align=left>";
							echo "<div hidden='true'>".$int['id_isp']."*</div>";
							
							echo "<div class='isp'>";
									if (isset($int['name'])) {
										echo "<div class='img_isp'><img width='13' src='img/".$int['name'].".png' title='".$int['name']." | ".$int['phone']."'></div>";
									}
								echo "<div class='num_isp'>".$int['agreement']."</div>";	
							echo "</div>";
							
							echo "</td>";
					//////////////////////// IP WAN	 ////////////////////////			
							echo "<td align=left>".$int['ext_ip']."</td>";										
					//////////////////////// КОЛИЧЕСТВО КАСС В МАГАЗИНЕ	 ////////////////////////
							$countWsLinuxInObject = countWsLinuxInObject($dbcnx, $id_object);
							$mask = selectInternetMaskInObject ($dbcnx, $id_object);
							$selectDHCPleasesCountFR = selectDHCPleasesCountFR ($dbcnx240, $mask);
							$n = $countWsLinuxInObject - $selectDHCPleasesCountFR;
							if ($n == 0) {
								echo "<td align=left>".$countWsLinuxInObject."</td> ";	
							} else {
								echo "<td align=left style='color: red;'>".$countWsLinuxInObject." ";
								while ($n > 0) { echo "!"; $n--; }
								echo "</td>";
							}	
						}
					}					
					echo "</tr>";
				echo "</tbody>";	
			echo "</table>";
		echo "</div>";
	}
}	
?>
<script>
$(document).ready(function() { 
    $("#myTable").tablesorter({sortList: [[0,0],[2,0]]}); 
} );
</script>
