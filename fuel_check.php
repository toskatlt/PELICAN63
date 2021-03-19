<?php
include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."section/menu.php"); /* MENU */

require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_avto.php");

?>
<script>
	function look (type) {
		param = document.getElementById(type);
		if(param.style.display == "none") param.style.display = "block";
		else param.style.display = "none"
	}
</script>
<?php

if (isset($_GET['del'])) {
	delFuelingCheck ($dbcnx, $_GET['del']);
}	

$speed = 62;

function route ($dbcnx, $vBake, $speed, $time_v_min, $fuel_consumption, $id_user, $routet_in, $datetime, $timeLimit, $mileage) {
	
	$unixtime = strtotime($datetime);
	
	while ($vBake > 7) {
		$maxPossibleKilometersTraveled = round(($vBake)/$fuel_consumption*100, 2); // Максимально пройденное растояние на бензине в баке
			
		$random_routet = $routet_in + 3;
			
		$routet = $maxPossibleKilometersTraveled/$random_routet;
		$routet = round($routet, 2);
		//echo "Дробление расстояния на ".$random_routet." | ".$routet." км<br>";
			//echo "selectTop5Distance (dbcnx, $routet, $unixtime) <br>";
		$selectTop5Distance = selectTop5Distance ($dbcnx, $routet, $unixtime);
		$random = rand(0, 4);
		$distance = $selectTop5Distance[$random]['distance'];

			if ($distance > 35) { $speed = 75; }
	////////////////////////////////////////////////////////////////////////////
		$time1 = $time_v_min + 12;
		$hour1 = floor($time1/60);  // остаток часов
		$minutes1 = $time1%60; // остаток минут
		
		$minutes1 = minutes ($minutes1);			
					
		$time_v_puti = $distance*(60/$speed);
		$time_v_puti = round($time_v_puti, 2);

		$time2 = $time1 + $time_v_puti;
		$hour2 = floor($time2/60);  // остаток часов
		$minutes2 = $time2%60; // остаток минут
		
		$minutes2 = minutes ($minutes2);
		
		$timeInShop = 5;
		
		$time3 = $time2 + $timeInShop;
		$hour3 = floor($time3/60);  // остаток часов
		$minutes3 = $time3%60; // остаток минут
		
		$minutes3 = minutes ($minutes3);
		
		$time_v_puti = $distance*(60/$speed);
		$time_v_puti = round($time_v_puti, 2);
		$time4 = round($time3 + $time_v_puti, 2);
		
		$hour4 = floor($time4/60);  // остаток часов
		$minutes4 = $time4%60; // остаток минут
		
		$minutes4 = minutes ($minutes4);
		
	//////////////////////////////////////////////////////////////////////////// ВЫВОД

		$time_v_min = $time4; 
		
		if (isset($timeLimit)) {
			$tL = $timeLimit;
		} else {
			$tL = 1420;
		}
		
		//echo "tL - ".$tL." <br>";
		if ($time4 < $tL) { // ЕСЛИ ДЕНЬ НЕ ЗАКОНЧИЛСЯ
			
		//echo "Космонавтов 30 - ".$selectTop5Distance[$random]['name']." | ".$distance." | ".$hour1.":".$minutes1." - >";
		//echo " ".$hour2.":".$minutes2." <br>";
		
		mysql_query("INSERT INTO `fueling_check_routet`(`id_user`, `from`, `to`, `distance`, `date`, `time_from`, `time_to`, `mileage`, `vbake`) VALUES ('".$id_user."', 'Космонавтов 30', '".$selectTop5Distance[$random]['name']."', '".$distance."', '".$datetime."', '".$hour1.":".$minutes1."', '".$hour2.":".$minutes2."', '".$mileage."', '".$vBake."') ", $dbcnx);	
		
		$mileage = $mileage+($distance*2);	
		
		//echo $selectTop5Distance[$random]['name']." - Космонавтов 30 | ".$distance." | ".$hour3.":".$minutes3." - >";
		//echo " ".$hour4.":".$minutes4." <br>";
		
		//echo "<br>";
		$vBake = $vBake-($distance*2)/(100/$fuel_consumption);
		$vBake = round($vBake, 2);
		
		mysql_query("INSERT INTO `fueling_check_routet`(`id_user`, `from`, `to`, `distance`, `date`, `time_from`, `time_to`, `mileage`, `vbake`) VALUES ('".$id_user."', '".$selectTop5Distance[$random]['name']."', 'Космонавтов 30', '".$distance."', '".$datetime."', '".$hour3.":".$minutes3."', '".$hour4.":".$minutes4."', '".$mileage."', '".$vBake."') ", $dbcnx);			
		mysql_query("UPDATE `avto` SET `tank`='".$vBake."', `mileage`='".$mileage."' WHERE `id_domain_user`='".$id_user."' ", $dbcnx);			
		//echo $mileage." - пробег автомобиля <br>";
		//echo "Осталось ".$vBake." литров бензина на ".$ostatok." км <br><br>";		
		} else {
			mysql_query("UPDATE `avto` SET `tank`='".$vBake."' WHERE `id_domain_user`='".$id_user."' ", $dbcnx);
			break;
		}
	}
}

function minutes ($minutes) {	
	if ($minutes < 10) {
		$minutes = "0".$minutes;
	}
	return $minutes;
}

// ФУНКЦИЯ - заправка бензина по чеку (подключение к бд, литров в баке, скорость передвижения, время (в минутах) заправка, номер АЗС, расход топлива)
function route_fueling ($dbcnx, $tank, $speed, $time_v_min, $idFueling, $liters, $fuel_consumption, $id_user, $mileage, $id_check, $datetime) {
	// Название и удаленность заправки
	$selectFueling = selectFueling ($dbcnx, $idFueling);
		
		
	//$distance = round(3 + mt_rand() / mt_getrandmax() * (7 - 3), 1); // дистанция до заправки
	if ($selectFueling[0]['distance'] < 30) {
		$distance = $selectFueling[0]['distance'];
	} else {
		$distance = 5.2;
	}  
	  
		//echo $distance." - distance <br>";
	$time1 = $time_v_min - 7;
	$hour = floor($time1/60);  // остаток часов
	$minutes = $time1%60; // остаток минут
	
	$minutes = minutes ($minutes);
	$time_from = $hour.":".$minutes;	
	
	//echo "Космонавтов 30 - ".$selectFueling[0]['name']." | ".$distance." | ".$hour.":".$minutes." - >";
	
	$time2 = $time_v_min - 2;
	$hour = floor($time2/60);  // остаток часов
	$minutes = $time2%60; // остаток минут
	
	$minutes = minutes ($minutes);
	$time_to = $hour.":".$minutes;
	
	//echo " ".$hour.":".$minutes." <br>";
	
	mysql_query("INSERT INTO `fueling_check_routet`(`id_user`, `from`, `to`, `distance`, `date`, `time_from`, `time_to`, `mileage`, `vbake`) VALUES ('".$id_user."', 'Космонавтов 30', '".$selectFueling[0]['name']."', '".$distance."', '".$datetime."', '".$time_from."', '".$time_to."', '".$mileage."', '".$tank."') ", $dbcnx);	

	////////////////////////////////
		$hour = floor($time_v_min/60);  // остаток часов
		$minutes = $time_v_min%60; // остаток минут
		
		$minutes = minutes ($minutes);
		
		//echo "<b style='color: red;'>Заправка на АЗС в ".$hour.":".$minutes." | ".$liters." л.</b><br>";
	//////////////////////////////
	
	$time3 = $time_v_min + 3;
	$hour = floor($time3/60);  // остаток часов
	$minutes = $time3%60; // остаток минут
	
	$minutes = minutes ($minutes);
	$time_from = $hour.":".$minutes;
	
	//echo $selectFueling[0]['name']." - Космонавтов 30 | ".$distance." | ".$hour.":".$minutes." - >";
	
	$time4 = $time_v_min + 7;
	$hour = floor($time4/60);  // остаток часов
	$minutes = $time4%60; // остаток минут
	
	$minutes = minutes ($minutes);
	$time_to = $hour.":".$minutes;
	
	//echo " ".$hour.":".$minutes." <br>";
	
	$exit[0] = $time4;
	
	$time_v_puti = $distance*2*(60/$speed); // минут потрачено на путь туда\обратно
	$time_v_puti = round($time_v_puti, 2);
	//echo $time_v_puti." - минут в пути <br>";
	$fuelConsumptionPerWay = $time_v_puti*($fuel_consumption/60); // израсходованно топливо за пройденный путь
	$fuelConsumptionPerWay = round($fuelConsumptionPerWay, 2);
	//echo $fuelConsumptionPerWay." - израсходованно топливо за пройденный путь <br>";
	$exit[1] = ($tank + $liters)-$fuelConsumptionPerWay;
	
		mysql_query("INSERT INTO `fueling_check_routet`(`id_user`, `from`, `to`, `distance`, `date`, `time_from`, `time_to`, `mileage`, `vbake`) VALUES ('".$id_user."', '".$selectFueling[0]['name']."', 'Космонавтов 30', '".$distance."', '".$datetime."', '".$time_from."', '".$time_to."', '".$mileage."', '".$exit[1]."') ", $dbcnx);
	
		$mileage = $mileage+($distance*2);
	
		mysql_query("UPDATE `avto` SET `tank`='".$exit[1]."', `mileage`='".$mileage."' WHERE `id_domain_user`='".$id_user."' ", $dbcnx);
		mysql_query("UPDATE `fueling_check` SET `point`='1' WHERE `id`='".$id_check."' ", $dbcnx);		
		//echo "UPDATE `avto` SET `tank`='".$exit[1]."' WHERE `id_domain_user`='".$id_user."' ";
		
		//echo $mileage." - пробег автомобиля <br>";
	return $exit;
} 

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);	
	if (isset($userdata)) {		
	
		if (!file_exists("waybill/".$userdata['id'])) { mkdir("waybill/".$userdata['id'], 0777); }
		
		$date_time = date("d-m-y H:i:s");
		if (!empty($_POST['fueling'])) {
			for ($i=0; $i<count($_POST['fueling']); $i++) {
				if ((!empty($_POST['fueling'][$i])) and (is_numeric($_POST['liters'][$i])) and (!empty($_POST['time'][$i]))) {
					$pieces = explode("T", $_POST['time'][$i]);
					$pieces_date = explode("-", $pieces[0]);
					$year = $pieces_date[0]; // год
					$month = $pieces_date[1]; // месяц
					$day = $pieces_date[2]; // день
					if (($day != '00') and ($month != '00') and ($year != '0000')) {		
						$pieces_time = explode(":", $pieces[1]);
						$hour = $pieces_time[0]; // часы
						$minutes = $pieces_time[1]; // минуты					
						$time = $year."-".$month."-".$day." ".$hour.":".$minutes.":00";
						
						mysql_query("INSERT INTO `fueling_check`(`datetime`, `fueling`, `liters`, `id_user`) VALUES ('".$time."','".$_POST['fueling'][$i]."','".$_POST['liters'][$i]."','".$userdata['id']."')", $dbcnx);
					}
				}
			}
		}

		echo "<div class='container check_flex'>";
		
		if (isset($_POST['check'])) {
			echo "<br>ДАННЫЕ ЧЕКОВ ЗАПРАВОК<br>";
			echo "!! Все поля обязательны к заполнению !!<br><br>";
			//echo $_POST['check']." - чеков<br>";
			echo "<form name='check' method='POST' action='fuel_check.php'>";
			
			for ($i=1;$i<=$_POST['check'];$i++){		
				echo "<br>";
				echo "-= Чек ".$i." =-<br>";
				echo "Станция: <select size='1' name='fueling[]'>";
				echo "<option value='' selected></option>";
				$allFueling = allFueling ($dbcnx);
				foreach ($allFueling as $af) {
					echo "<option value='".$af['id']."'>".$af['name']."</option>";
				}
				echo "</select><br>";
				echo "Дата: <input name='time[]' type='datetime-local'/><br>";
				echo "Литры: <input name='liters[]' type='text' size='14'><br>";
				echo "<br>";	
			}
			echo "</select><input type='submit' class='generate' name='submit' value='Добавить в БД'>";
			echo "</form>";	
			echo "<br><br><br><br>";
		} else {
			if (isset($_POST['generate'])) {		
				/////////////////////////////////////////////////////////////////////////////////////	
				//! echo "<br><br>";
				$allFuelingCheckUser = allFuelingCheckUser ($dbcnx, $userdata['id']); // ВСЕГО ЧЕКОВ
				$allFuelingCheckUserNotUse = allFuelingCheckUserNotUse ($dbcnx, $userdata['id']); // ВСЕ НЕ РАСЧИТАННЫЕ ЧЕКИ
				
				$pieces = explode(" ", $allFuelingCheckUserNotUse[0]['datetime']);				
					$pieces_date = explode(".", $pieces[0]);

					$year = $pieces_date[2]; // год
					$month = $pieces_date[1]; // месяц
				
				$number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
											
				//! echo "!! Чеков ".$allFuelingCheckUser."<br>";
				//! echo "!! Дней - ".$number." в месяце ".$month." ".$year." года <br>";

				///////////////////////////////////// НАЧАЛО РАБОЧЕГО ДНЯ И ВЫЕЗДА В 7 УТРА!!!!!!!!! /////////////////////////////////////	
					$beginningOfTheDay = 8; // начало дня в часах
					$timeInMinutesBeginningOfTheDay = $beginningOfTheDay*60; // начало рабочего дня в минутах
				
				//! echo "<br><hr><hr>";
				/////////////////////////////////////////////////////////////////////////////////////	
				$d = 1;
				while ($d < $number+1) { // Перебор дней месяца чеков
					if ($d < 10) { // если число меньше 10, добавляем в начало 0
						$d = "0".$d;
					}
				
					// ДАННЫЕ АВТО ПОЛЬЗОВАТЕЛЯ
					$selectAvto = selectAvto ($dbcnx, $userdata['id']);			
					$fuel_consumption = $selectAvto['fuel_consumption']; // расход топлива
					$mileage = $selectAvto['mileage']; // пробег автомобиля
					
					//! echo $mileage." - пробег автомобиля <br>";
					
					$fuel_limit = $selectAvto['fuel_limit']; // размер бака
					$tank = $selectAvto['tank']; // текущие состояние бака в литрах	
					
					$maxTank = $fuel_limit+20; // размер бака автомобиля + 20 л.канистра
					
						$winter_month = ["11","12","1", "2", "3", "4"]; // зимние месяца
						if( in_array($month, $winter_month) ) {
							$fuel_consumption = $fuel_consumption*1.1; // в зимний период расход топлива увеличивается на 10%
						} else {
							$fuel_consumption = $fuel_consumption*1; // летний период
						}
					//echo "!! В баке - ".$tank." <br>";
					$datetime = $year."-".$month."-".$d; // дата в формате 2000-00-00
					if (!file_exists("waybill/".$userdata['id']."/".$year)) { mkdir("waybill/".$userdata['id']."/".$year, 0777); }
					if (!file_exists("waybill/".$userdata['id']."/".$year."/".$month)) { mkdir("waybill/".$userdata['id']."/".$year."/".$month, 0777); }
					//! echo "<p style='color:black;'>".$d.".".$month.".".$year."</p>";
					
						$userFuelingCheckInDay = userFuelingCheckInDay ($dbcnx, $userdata['id'], $datetime);
					///////////  ЕСЛИ В БАКЕ БОЛЬШЕ 8 ЛИТРОВ  /////////////////////////////////////////////////////////////////////////////////					
					if ($tank > 8) { // ЕСЛИ НА НАЧАЛО ДНЯ В БАКЕ БОЛЬШЕ 8 ЛИТРОВ 	
						//echo "На начало рабочего дня в баке ".$tank." литров, из которых ".($tank-5)." являются лишними <br>";
						
					///////////  ЕСЛИ В БАКЕ БОЛЬШЕ 8 ЛИТРОВ -> ЕСЛИ БЫЛА МИНИМУМ 1 ЗАПРАВКА  //////////////////////////////////////////////////

						if (isset($userFuelingCheckInDay[0]['id'])) { // ЕСЛИ В ТЕЧЕНИИ ДНЯ БЫЛА ХОТЯ БЫ 1 ЗАПРАВКА
							
							$piecesFuelingCheck = explode(" ", $userFuelingCheckInDay[0]['datetime']);							
							$piecesDateFuelingCheck = explode(":", $piecesFuelingCheck[1]);

							$hourFuelingCheck = $piecesDateFuelingCheck[0]; // час
							$minutesFuelingCheck = $piecesDateFuelingCheck[1]; // минуты
					
							$timeInMinutesFuelingCheck = ($hourFuelingCheck*60)+$minutesFuelingCheck; // Минута заправки с начала дня
							
							$idFueling = $userFuelingCheckInDay[0]['fueling'];
							$liters = $userFuelingCheckInDay[0]['liters'];
							$id_check = $userFuelingCheckInDay[0]['id'];
							
					///////////  ЕСЛИ В БАКЕ БОЛЬШЕ 8 ЛИТРОВ -> ЕСЛИ БЫЛА МИНИМУМ 1 ЗАПРАВКА -> ЗАПРАВКА ПРОИЗОШЛА РАНЬШЕ НАЧАЛА ДНЯ  ////////////			
			
							if ($timeInMinutesBeginningOfTheDay > $timeInMinutesFuelingCheck) { // ЕСЛИ ЗАПРАВКА ПРОИЗОШЛА РАНЬШЕ НАЧАЛА ДНЯ
								//! echo "До заправки не успеваем израсходовать излишки в баке <br>";
								
								// заправка по чеку на АЗС (подключение к БД, литров в баке, скорость в км/ч, время заправки в мин, количество заправленного топлива, расход топлива)
								// ЗАПРАВКА
								
					///////////  ЕСЛИ В БАКЕ БОЛЬШЕ 8 ЛИТРОВ -> ЕСЛИ БЫЛА МИНИМУМ 1 ЗАПРАВКА -> НАЧАЛО ДНЯ РАНЬШЕ ЗАПРАВКИ  ///////////////////////	
						
							} else { // ЕСЛИ НАЧАЛО ДНЯ РАНЬШЕ ЗАПРАВКИ		 
								$timeInMinutesBeginningOfTheDay_FuelingCheck = ($timeInMinutesFuelingCheck-$timeInMinutesBeginningOfTheDay)-15;
								//echo $timeInMinutesBeginningOfTheDay_FuelingCheck." - свободные минуты с начала рабочего дня до заправки <br>";
							
								$time_v_min = $timeInMinutesBeginningOfTheDay;
								
								$timeInMinutesAllDay = (1440-$timeInMinutesBeginningOfTheDay)-50;
								//echo "Свободных минут до конца дня: ".$timeInMinutesAllDay." <br>";
							
								$time_v_min = $timeInMinutesBeginningOfTheDay;
							
								$possibleKilometersTraveled = round(($timeInMinutesBeginningOfTheDay/60)*$speed, 2); // возможный пройденный километраж до конца текущего дня
								$maxPossibleKilometersTraveled = round(($tank)/$fuel_consumption*100, 2); // возможный пройденный километраж на текущем баке
								if ($possibleKilometersTraveled > $maxPossibleKilometersTraveled) {
									//echo "Можно потратить весь бензин за текущий день <br>";
									$routet_in = 1;
									//$ostatok = $maxPossibleKilometersTraveled; // МАКСИМАЛЬНО ВОЗМОЖНЫЙ ПУТЬ В КИЛОМЕТРАХ НА БЕНЗИНЕ В БАКЕ - 5 литров ЗАПАСА НА СЛЕД ДЕНЬ
								}
								else {
									//echo "Потребуется несколько дней для опустошения бака <br>";
									$routet_in = round($maxPossibleKilometersTraveled/$possibleKilometersTraveled, 0);
									$routet_in = $routet_in * 3; 
									//$ostatok = $possibleKilometersTraveled; // МАКСИМАЛЬНО ВОЗМОЖНЫЙ ПУТЬ В КИЛОМЕТРАХ НА БЕНЗИНЕ В БАКЕ - 5 литров ЗАПАСА НА СЛЕД ДЕНЬ
								}
								
								route ($dbcnx , $tank , $speed , $time_v_min, $fuel_consumption, $userdata['id'], $routet_in, $datetime, $timeLimit, $mileage);
								
								$selectAvto = selectAvto ($dbcnx, $userdata['id']);			
								$tank = $selectAvto['tank']; // текущие состояние бака в литрах
								$mileage = $selectAvto['mileage'];		
								
								$exit = route_fueling ($dbcnx, $tank, $speed, $timeInMinutesFuelingCheck, $idFueling, $liters, $fuel_consumption, $userdata['id'], $mileage, $id_check, $datetime);
							
								//echo ((1440-$exit[0])-25)." - минут до конца дня <br>";
								$timeInMinutesEndOfTheDay = ((1440-$exit[0])-25);
								//echo $exit[1]." - бензин в баке <br><br>";
								//! echo "<br>";
								$time_v_min = $exit[0]-10;
								$tank = $exit[1];
								
								$selectAvto = selectAvto ($dbcnx, $userdata['id']);			
								$mileage = $selectAvto['mileage'];	
								
								if ($timeInMinutesEndOfTheDay > 30) {
									
									$possibleKilometersTraveled = round(($timeInMinutesEndOfTheDay/60)*$speed, 2); // возможный пройденный километраж до конца текущего дня
									$maxPossibleKilometersTraveled = round(($tank)/$fuel_consumption*100, 2); // возможный пройденный километраж на текущем баке
									if ($possibleKilometersTraveled > $maxPossibleKilometersTraveled) {
										//echo "Можно потратить весь бензин за текущий день <br>";
										$routet_in = 1;
									}
									else {
										//echo "Потребуется несколько дней для опустошения бака <br>";
										$routet_in = round($maxPossibleKilometersTraveled/$possibleKilometersTraveled, 0);
										//echo $maxPossibleKilometersTraveled." - максимально пройденный путь <br>"; 
										//echo $possibleKilometersTraveled." - возможный пройденный путь <br>";
										$routet_in = $routet_in * 3; 
									}
									
									route ($dbcnx , $tank , $speed , $time_v_min, $fuel_consumption, $userdata['id'], $routet_in, $datetime, $timeLimit, $mileage);
								}	
							}
							
						} 
				///////////  1-2  ///////////////////////////////////////////////////////////////////////////////					
						else {
							//echo "<b style='color:red;'> в этот день заправок не было </b> <br><br>"; // дата в формате 00.00.2000
							$timeInMinutesAllDay = (1440-$timeInMinutesBeginningOfTheDay)-50;
							//echo "Свободных минут до конца дня: ".$timeInMinutesAllDay." <br>";
							
							$time_v_min = $timeInMinutesBeginningOfTheDay;
							
							$possibleKilometersTraveled = round(($timeInMinutesBeginningOfTheDay/60)*$speed, 2); // возможный пройденный километраж до конца текущего дня
							$maxPossibleKilometersTraveled = round(($tank)/$fuel_consumption*100, 2); // возможный пройденный километраж на текущем баке
								if ($possibleKilometersTraveled > $maxPossibleKilometersTraveled) {
									//echo "Можно потратить весь бензин за текущий день <br>";
									$routet_in = 1;
									//$ostatok = $maxPossibleKilometersTraveled; // МАКСИМАЛЬНО ВОЗМОЖНЫЙ ПУТЬ В КИЛОМЕТРАХ НА БЕНЗИНЕ В БАКЕ - 5 литров ЗАПАСА НА СЛЕД ДЕНЬ
								}
								else {
									//echo "Потребуется несколько дней для опустошения бака <br>";
									$routet_in = round($maxPossibleKilometersTraveled/$possibleKilometersTraveled, 0);
									$routet_in = $routet_in * 3; 
									//$ostatok = $possibleKilometersTraveled; // МАКСИМАЛЬНО ВОЗМОЖНЫЙ ПУТЬ В КИЛОМЕТРАХ НА БЕНЗИНЕ В БАКЕ - 5 литров ЗАПАСА НА СЛЕД ДЕНЬ
								}
							
										//(БД | БАК | скорость | ВРЕМЯ НАЧАЛА ПОЕЗДКИ | расход топлива | ID водитель | делитель)
									route ($dbcnx , $tank , $speed , $time_v_min, $fuel_consumption, $userdata['id'], $routet_in, $datetime, $timeLimit, $mileage);
						}
								
				///////////  ЕСЛИ В БАКЕ МЕНЬШЕ 8 ЛИТРОВ  /////////////////////////////////////////////////////////////////////////////////
						
					} else {
					
				///////////  ЕСЛИ В БАКЕ МЕНЬШЕ 8 ЛИТРОВ -> ЕСЛИ БЫЛА МИНИМУМ 1 ЗАПРАВКА  //////////////////////////////////////////////////					
						if (isset($userFuelingCheckInDay[0]['id'])) { // ЕСЛИ В ТЕЧЕНИИ ДНЯ БЫЛА ХОТЯ БЫ 1 ЗАПРАВКА
							$piecesFuelingCheck = explode(" ", $userFuelingCheckInDay[0]['datetime']);							
							$piecesDateFuelingCheck = explode(":", $piecesFuelingCheck[1]);

							$hourFuelingCheck = $piecesDateFuelingCheck[0]; // час
							$minutesFuelingCheck = $piecesDateFuelingCheck[1]; // минуты
					
							$timeInMinutesFuelingCheck = ($hourFuelingCheck*60)+$minutesFuelingCheck; // Минута заправки с начала дня
								
							$timeInMinutesBeginningOfTheDay_FuelingCheck = ($timeInMinutesFuelingCheck-$timeInMinutesBeginningOfTheDay)-15;
							//echo $timeInMinutesBeginningOfTheDay_FuelingCheck." - свободные минуты с начала рабочего дня до заправки <br>";
							
							$idFueling = $userFuelingCheckInDay[0]['fueling'];
							$liters = $userFuelingCheckInDay[0]['liters'];
							$id_check = $userFuelingCheckInDay[0]['id'];
							// заправка по чеку на АЗС (БД, литров в баке, скорость в км/ч, время заправки в мин, ID заправки, количество заправленного топлива, расход топлива, ID водитель)
							$exit = route_fueling ($dbcnx, $tank, $speed, $timeInMinutesFuelingCheck, $idFueling, $liters, $fuel_consumption, $userdata['id'], $mileage, $id_check, $datetime);
							
						//	echo ((1440-$exit[0])-25)." - минут до конца дня <br>";
							$timeInMinutesEndOfTheDay = ((1440-$exit[0])-25);
							//echo $exit[1]." - бензин в баке <br><br>";
							//! echo "<br>";
							$time_v_min = $exit[0]-10;
							$tank = $exit[1];
							
							if (isset($userFuelingCheckInDay[1]['id'])) { // ЕСЛИ В ТЕЧЕНИИ ДНЯ БЫЛО 2 ЗАПРАВКИ
							
								$piecesFuelingCheck = explode(" ", $userFuelingCheckInDay[1]['datetime']);							
								$piecesDateFuelingCheck = explode(":", $piecesFuelingCheck[1]);
								
								$hourFuelingCheck = $piecesDateFuelingCheck[0]; // час
								$minutesFuelingCheck = $piecesDateFuelingCheck[1]; // минуты
								
								$timeInMinutesFuelingCheck = ($hourFuelingCheck*60)+$minutesFuelingCheck; // Минута заправки 2 чека с начала дня
								
								$timeInMinutesBeginningOfTheDay_FuelingCheck = ($timeInMinutesFuelingCheck-$exit[0])-15;
								//echo $timeInMinutesBeginningOfTheDay_FuelingCheck." - свободные минуты с момента окончания первой заправки до заправки второй <br>";
								
								$timeLimit = $timeInMinutesFuelingCheck-10;
							
								if ($timeInMinutesEndOfTheDay > 30) {								
									$possibleKilometersTraveled = round(($timeInMinutesEndOfTheDay/60)*$speed, 2); // возможный пройденный километраж до конца текущего дня
									$maxPossibleKilometersTraveled = round(($tank)/$fuel_consumption*100, 2); // возможный пройденный километраж на текущем баке
									if ($possibleKilometersTraveled > $maxPossibleKilometersTraveled) {
										//echo "Можно потратить весь бензин за текущий день <br>";
										$routet_in = 1;
									}
									else {
										//echo "Потребуется несколько дней для опустошения бака <br>";
										$routet_in = round($maxPossibleKilometersTraveled/$possibleKilometersTraveled, 0);
										//echo $maxPossibleKilometersTraveled." - максимально пройденный путь <br>"; 
										//echo $possibleKilometersTraveled." - возможный пройденный путь <br>";
										$routet_in = $routet_in * 10; 
									}
									
									$selectAvto = selectAvto ($dbcnx, $userdata['id']);			
									$tank = $selectAvto['tank']; // текущие состояние бака в литрах
									$mileage = $selectAvto['mileage'];
									

									route ($dbcnx , $tank , $speed , $time_v_min, $fuel_consumption, $userdata['id'], $routet_in, $datetime, $timeLimit, $mileage);
								}
								
								$idFueling = $userFuelingCheckInDay[1]['fueling'];
								$liters = $userFuelingCheckInDay[1]['liters'];
								
								$selectAvto = selectAvto ($dbcnx, $userdata['id']);			
								$tank = $selectAvto['tank']; // текущие состояние бака в литрах
								$mileage = $selectAvto['mileage'];							
								
								$id_check = $userFuelingCheckInDay[1]['id'];
								
								$exit = route_fueling ($dbcnx, $tank, $speed, $timeInMinutesFuelingCheck, $idFueling, $liters, $fuel_consumption, $userdata['id'], $mileage, $id_check, $datetime);
								
								//echo ((1440-$exit[0])-25)." - минут до конца дня <br>";
								$timeInMinutesEndOfTheDay = ((1440-$exit[0])-25);
								// echo $exit[1]." - бензин в баке <br><br>";
								//! echo "<br>";						
								$time_v_min = $exit[0]-10;
								$tank = $exit[1];
								
								if ($timeInMinutesEndOfTheDay > 30) {								
									$possibleKilometersTraveled = round(($timeInMinutesEndOfTheDay/60)*$speed, 2); // возможный пройденный километраж до конца текущего дня
									$maxPossibleKilometersTraveled = round(($tank)/$fuel_consumption*100, 2); // возможный пройденный километраж на текущем баке
									if ($possibleKilometersTraveled > $maxPossibleKilometersTraveled) {
										//echo "Можно потратить весь бензин за текущий день <br>";
										$routet_in = 1;
									}
									else {
										//echo "Потребуется несколько дней для опустошения бака <br>";
										$routet_in = round($maxPossibleKilometersTraveled/$possibleKilometersTraveled, 0);
										//echo $maxPossibleKilometersTraveled." - максимально пройденный путь <br>"; 
										//echo $possibleKilometersTraveled." - возможный пройденный путь <br>";
										$routet_in = $routet_in * 9; 
									}
									
									$timeLimit = 1420;		
									$selectAvto = selectAvto ($dbcnx, $userdata['id']);			
									$tank = $selectAvto['tank']; // текущие состояние бака в литрах
									$mileage = $selectAvto['mileage'];
									
									route ($dbcnx , $tank , $speed , $time_v_min, $fuel_consumption, $userdata['id'], $routet_in, $datetime, $timeLimit, $mileage);
								}		
							} else { // ЕСЛИ В ТЕЧЕНИИ ДНЯ БЫЛА ТОЛЬКО 1 ЗАПРАВКА
								
								$selectAvto = selectAvto ($dbcnx, $userdata['id']);	
								$tank = $selectAvto['tank']; // текущие состояние бака в литрах
								$mileage = $selectAvto['mileage'];	
								$timeLimit = 1420;
								//echo $timeInMinutesEndOfTheDay." - timeInMinutesEndOfTheDay <br>";
								if ($timeInMinutesEndOfTheDay > 30) {
									$possibleKilometersTraveled = round(($timeInMinutesEndOfTheDay/60)*$speed, 2); // возможный пройденный километраж до конца текущего дня
									$maxPossibleKilometersTraveled = round(($tank)/$fuel_consumption*100, 2); // возможный пройденный километраж на текущем баке
									if ($possibleKilometersTraveled > $maxPossibleKilometersTraveled) {
										//echo "Можно потратить весь бензин за текущий день <br>";
										$routet_in = 1;
									}
									else {
										//echo "Потребуется несколько дней для опустошения бака <br>";
										$routet_in = round($maxPossibleKilometersTraveled/$possibleKilometersTraveled, 0);
										//echo $maxPossibleKilometersTraveled." - максимально пройденный путь <br>"; 
										//echo $possibleKilometersTraveled." - возможный пройденный путь <br>";
										$routet_in = $routet_in * 3; 
									}
									
									route ($dbcnx , $tank , $speed , $time_v_min, $fuel_consumption, $userdata['id'], $routet_in, $datetime, $timeLimit, $mileage);
									//echo "route (dbcnx , $tank , $speed , $time_v_min, $fuel_consumption, ".$userdata['id'].", $routet_in, $datetime, $timeLimit, $mileage) <br>";
								}
							}
				///////////  ЕСЛИ В БАКЕ МЕНЬШЕ 8 ЛИТРОВ -> ЗАПРАВОК НЕ БЫЛО  ///////////////////////////////////////////////////////////////////////////////						
						} else {
							// СЛЕДУЮЩИЙ ДЕНЬ
						}
					}
					$d++;
					//! echo "<br><hr><br>";
				}
				
				$selectDateCheckRoutet = selectDateCheckRoutet ($dbcnx);
				foreach ($selectDateCheckRoutet as $sdcr) {
					
					//! echo "<hr><br>";
					$selectAvto = selectAvto ($dbcnx, $userdata['id']);			
					$selectDateCheckRoutetFromDate = selectDateCheckRoutetFromDate ($dbcnx, $sdcr['date']);	
					
					$day = date("d", strtotime($sdcr['date']));
					$month = date("m", strtotime($sdcr['date']));
					$year = date("Y", strtotime($sdcr['date']));
					if (!file_exists("waybill/".$userdata['id']."/".$year)) { mkdir("waybill/".$userdata['id']."/".$year, 0777); }
					if (!file_exists("waybill/".$userdata['id']."/".$year."/".$month)) { mkdir("waybill/".$userdata['id']."/".$year."/".$month, 0777); }
					
					$mileage_start = $selectDateCheckRoutetFromDate[0]['mileage'];
					$finish = count($selectDateCheckRoutetFromDate)-1;
					$mileage_finish = $selectDateCheckRoutetFromDate[$finish]['mileage'];
					
					$vbake_start = $selectDateCheckRoutetFromDate[0]['vbake'];
					$vbake_finish = $selectDateCheckRoutetFromDate[$finish]['vbake'];				
					
					$timeStart = $selectDateCheckRoutetFromDate[0]['time_from'];
					$timeFinish = $selectDateCheckRoutetFromDate[$finish]['time_to'];
					
						$piecesTimeStart = explode(":", $timeStart);
						$hourTimeStart = $piecesTimeStart[0]; // час
						$minutesTimeStart = $piecesTimeStart[1]; // минуты			
						$timeInMinutesTimeStart = ($hourTimeStart*60)+$minutesTimeStart; 
						
						$piecesTimeFinish = explode(":", $timeFinish);
						$hourTimeFinish = $piecesTimeFinish[0]; // час
						$minutesTimeFinish = $piecesTimeFinish[1]; // минуты		
						$timeInMinutesTimeFinish = ($hourTimeFinish*60)+$minutesTimeFinish; 
					
					$timeInRoad = $timeInMinutesTimeFinish - $timeInMinutesTimeStart;
					
						$hourInRoad = floor($timeInRoad/60);  // остаток часов
						$minutesInRoad = $timeInRoad%60; // остаток минут
						
						$minutesInRoad = minutes ($minutesInRoad);
						
						$timeOverInRoad = $hourInRoad.":".$minutesInRoad;
						
					$monthes = array(
							1 => 'Января', 2 => 'Февраля', 3 => 'Марта', 4 => 'Апреля',
							5 => 'Мая', 6 => 'Июня', 7 => 'Июля', 8 => 'Августа',
							9 => 'Сентября', 10 => 'Октября', 11 => 'Ноября', 12 => 'Декабря'
						);
				
					//! echo date("d ", strtotime($sdcr['date']))." ".$monthes[(date("n", strtotime($sdcr['date'])))]." ".date(" Y", strtotime($sdcr['date']))."<br>";
					
					//! echo "ФИО: ".$selectAvto['fioAll']." <br>";
					//! echo "Инициалы: ".$selectAvto['fio']." <br>";
					//! echo "Марка автомобиля: ".$selectAvto['model']." <br>";
					//! echo "Гос номер: ".$selectAvto['number']." <br>";
					//! echo "Табельный номер: ".$selectAvto['tabel_number']." <br>";
					//! echo "Марка бензина: АИ-".$selectAvto['fuel_mark']." <br>";
					//! echo "Удостоверение: ".$selectAvto['d_license']." <br>";
					//! echo "<br>";
					//! echo $mileage_start." - километраж на начало дня <br>";
					//! echo $mileage_finish." - километраж на конец дня <br>";
					//! echo "<br>";
					$selectSumInDayLiters = selectSumInDayLiters ($dbcnx, $sdcr['date'], $userdata['id']);
					if (!isset($selectSumInDayLiters)) { $selectSumInDayLiters = 0; }
					//! echo "Выдано литров ".$selectSumInDayLiters." <br>";
					//! echo "<br>";
					$mileageOverInRoad = $mileage_finish - $mileage_start;
					$mileageOverInRoad = round($mileageOverInRoad, 2);
					//! echo $mileageOverInRoad." - пройдено км <br>";
					//! echo $timeStart." - время выезда <br>";
					//! echo $timeFinish." - время возвращения <br>";
					//! echo $timeOverInRoad." - время в наряде <br>";
					
					//! echo "<br><br><br><br>";
					//// GD ////////////////////////////////////			
					$im = ImageCreateTrueColor (2000, 1414);
					$im1 = @imagecreatefromjpeg('/var/www/html/www/waybill/xxx.jpg'); // фон

					imagealphablending($im, true);
					imagealphablending($im, 1);
					imagealphablending($im1, 1);
					
					$black = imagecolorallocate($im, 0, 0, 0);
					// $fontb = '/var/www/html/www/waybill/6426.ttf'; // Arial Black 
					// $font = '/var/www/html/www/waybill/6426.ttf'; // Arial
					$font = '/var/www/html/www/waybill/7454.ttf'; // Times New Roman
					
					imagecopy($im, $im1, 0, 0, 0, 0, 2000, 1414);
					$dateList = date("d", strtotime($sdcr['date']))." ".$monthes[(date("n", strtotime($sdcr['date'])))]." ".date("Y", strtotime($sdcr['date']));
					
					imagettftext($im, 14, 0, 322, 180, $black, $font, $dateList); // ДАТА ЛИСТА
					imagettftext($im, 15, 0, 322, 326, $black, $font, $selectAvto['model']); // МАРКА АВТО
					imagettftext($im, 14, 0, 448, 352, $black, $font, $selectAvto['number']); // НОМЕР АВТО
					imagettftext($im, 14, 0, 230, 378, $black, $font, $selectAvto['fioAll']); // ФИО ВОДИТЕЛЯ ПОЛНОСТЬЮ
					imagettftext($im, 14, 0, 300, 432, $black, $font, $selectAvto['d_license']); // УДОСТОВЕРЕНИЕ
					imagettftext($im, 14, 0, 806, 375, $black, $font, $selectAvto['tabel_number']); // ТАБЕЛЬНЫЙ НОМЕР
					imagettftext($im, 14, 0, 806, 597, $black, $font, $mileage_start); // ПОКАЗАНИЯ СПИДОМЕТРА
					
					$center = round(850); //центр изображения
						$selectSumInDayLiters = round($selectSumInDayLiters, 2);
						$length0 = imagettfbbox(14, 0, $font, $selectSumInDayLiters); // определение длины строки
						$position0 = $center-round(($length0[2]-$length0[0])/2);
					imagettftext($im, 14, 0, $position0, 1075, $black, $font, $selectSumInDayLiters); // ВЫДАНО ЛИТРОВ
						$length1 = imagettfbbox(14, 0, $font, $vbake_start); // определение длины строки
						$position1 = $center-round(($length1[2]-$length1[0])/2);
					imagettftext($im, 14, 0, $position1, 1100, $black, $font, $vbake_start); // ОСТАТОК ПРИ ВЫЕЗДЕ
						$length2 = imagettfbbox(14, 0, $font, $vbake_finish); // определение длины строки
						$position2 = $center-round(($length2[2]-$length2[0])/2);
					imagettftext($im, 14, 0, $position2, 1125, $black, $font, $vbake_finish); // ОСТАТОК ПРИ ВОЗВРАЩЕНИИ
					
					$fuel_consumption = $selectAvto['fuel_consumption'];
					$winter_month = ["11","12","1", "2", "3", "4"]; // зимние месяца
						if (in_array($month, $winter_month)) { $fuel_consumption = $fuel_consumption*1.1; // в зимний период расход топлива увеличивается на 10%
						} else { $fuel_consumption = $fuel_consumption*1; }
					$consumptionIsNormal = round(($mileageOverInRoad*$fuel_consumption)/100, 2);
					
						$length3 = imagettfbbox(14, 0, $font, $consumptionIsNormal); // определение длины строки
						$position3 = $center-round(($length3[2]-$length3[0])/2);
					imagettftext($im, 14, 0, $position3, 1150, $black, $font, $consumptionIsNormal); // РАСХОД ПО НОРМЕ
					$actualConsumption = round($consumptionIsNormal*0.99, 2);
						$length4 = imagettfbbox(14, 0, $font, $actualConsumption); // определение длины строки
						$position4 = $center-round(($length4[2]-$length4[0])/2);
					imagettftext($im, 14, 0, $position4, 1175, $black, $font, $actualConsumption); // ФАКТИЧЕСКИЙ РАСХОД
					$saving = round($consumptionIsNormal-$actualConsumption, 2);
						$length5 = imagettfbbox(14, 0, $font, $saving); // определение длины строки
						$position5 = $center-round(($length5[2]-$length5[0])/2);
					imagettftext($im, 14, 0, $position5, 1200, $black, $font, $saving); // ЭКОНОМИЯ
						$length6 = imagettfbbox(14, 0, $font, '0'); // определение длины строки
						$position6 = $center-round(($length6[2]-$length6[0])/2);
					imagettftext($im, 14, 0, $position6, 1225, $black, $font, '0'); // ПЕРЕРАСХОД
					
					imagettftext($im, 14, 0, 795, 1315, $black, $font, $mileage_finish); // ПОКАЗАНИЯ СПИДОМЕТРА НА КОНЕЦ ДНЯ
					imagettftext($im, 14, 0, 445, 880, $black, $font, $timeStart); // ВРЕМЯ ВЫЕЗДА
					imagettftext($im, 13, 0, 750, 688, $black, $font, $selectAvto['fio']); // ФИО ВОДИТЕЛЯ
					imagettftext($im, 13, 0, 750, 790, $black, $font, $selectAvto['fio']); // ФИО ВОДИТЕЛЯ
					
					$fuelMark = "АИ-".$selectAvto['fuel_mark'];
					imagettftext($im, 14, 0, 680, 874, $black, $font, $fuelMark); // МАРКА ТОПЛИВА
					imagettftext($im, 14, 0, 445, 1043, $black, $font, $timeFinish); // ВРЕМЯ ВОЗВРАЩЕНИЯ
					
					$i=1;
					$s=182;
					foreach ($selectDateCheckRoutetFromDate as $sdcrfd) {
						$time_from = explode(":", $sdcrfd['time_from']);
						$time_to = explode(":", $sdcrfd['time_to']);	
						//! echo " ".$sdcrfd['from']." | ".$sdcrfd['to']." | ".$time_from[0]." | ".$time_from[1]." | ".$time_to[0]." | ".$time_to[1]." | ".$sdcrfd['distance']."км  <br>";
							$length7 = imagettfbbox(13, 0, $font, $i); // определение длины строки
							$position7 = 990-round(($length7[2]-$length7[0])/2);
						imagettftext($im, 13, 0, $position7, $s, $black, $font, $i++);
							$from = mb_strimwidth($sdcrfd['from'], 0, 18);
						imagettftext($im, 13, 0, 1060, $s, $black, $font, $from);
							$to = mb_strimwidth($sdcrfd['to'], 0, 18);
						imagettftext($im, 13, 0, 1260, $s, $black, $font, $to);
						imagettftext($im, 13, 0, 1455, $s, $black, $font, $time_from[0]);
						imagettftext($im, 13, 0, 1510, $s, $black, $font, $time_from[1]);
						imagettftext($im, 13, 0, 1560, $s, $black, $font, $time_to[0]);
						imagettftext($im, 13, 0, 1615, $s, $black, $font, $time_to[1]);
						imagettftext($im, 13, 0, 1670, $s, $black, $font, $sdcrfd['distance']);
						$s=$s+25.5;
						mysql_query("UPDATE `fueling_check_routet` SET `check`= '1' WHERE id = '".$sdcrfd['id']."'", $dbcnx);
					}				
					
					imagettftext($im, 13, 0, 365, 1349, $black, $font, $selectAvto['fio']); // ФИО ВОДИТЕЛЯ
					imagettftext($im, 13, 0, 734, 1349, $black, $font, $selectAvto['fio']); // ФИО ВОДИТЕЛЯ
					
					imagettftext($im, 14, 0, 1152, 978, $black, $font, $timeOverInRoad); // ВРЕМЯ В НАРЯДЕ
					imagettftext($im, 14, 0, 1148, 1042, $black, $font, $mileageOverInRoad); // ПРОЙДЕНО КМ
					imagettftext($im, 13, 0, 1508, 1150, $black, $font, $selectAvto['fio']); // ФИО ВОДИТЕЛЯ
					
					imagepng($im, '/var/www/html/www/waybill/'.$userdata['id'].'/'.$year.'/'.$month.'/'.$day.'.png');
					imagedestroy($im);
					
					mysql_query("INSERT INTO `finished_checks` (`id_domain_user`, `year`, `month`, `day`) VALUES ('".$userdata['id']."', '".$year."', '".$month."', '".$day."')", $dbcnx);
				}
			}
			
			$selectAvto = selectAvto ($dbcnx, $userdata['id']);	
			if ($selectAvto['idx'] != 0) {
				
				echo "<div class='fuel_check_block'>";
				
				echo "!! ДАННЫЕ АВТОМОБИЛЯ И ВОДИТЕЛЯ !! <img src='img/exc.png' style='width:13px;' title='все поля должны быть обязательно заполненны'><br><br>";
				echo "ID пользователя: <b style='color:#fff'>".$userdata['id']."</b></br>";
				echo "Водитель: <b style='color:#fff'>".$selectAvto['fio']."</b></br>";
				echo "Модель: <b style='color:#fff'>".$selectAvto['model']."</b></br>";
				echo "Гос.номер: <b style='color:#fff'>".$selectAvto['number']."</b></br>";
				echo "Номер водит.удост: <b style='color:#fff'>".$selectAvto['d_license']."</b></br>";
				echo "Размер бака авто: <b style='color:#fff'>".$selectAvto['fuel_limit']."</b></br>";
				echo "Модель бензина: <b style='color:#fff'>".$selectAvto['fuel_mark']."</b></br>";
				echo "Расход топлива: <b style='color:#fff'>".$selectAvto['fuel_consumption']."</b></br>";
				echo "Табельный номер: <b style='color:#fff'>".$selectAvto['tabel_number']."</b></br>";
				echo "Пробег авто: <b style='color:#fff'>".$selectAvto['mileage']."</b></br>";
				
				echo "<br>";		
				echo "<a href='http://www.neo63.ru/editpers.php?e=3' title='Данные авто'> >> СМЕНИТЬ ДАННЫЕ АВТО << </a><br>";
				
				//echo "<br>У ВАС ОПЛАЧЕН РАСЧЕТ 500 ЛИТРОВ<br>";

				echo "<br><hr><br>";
				echo "<form name='check' method='POST' action='fuel_check.php'>";
					echo "Количество чеков за месяц ";
					echo "<input type='number' class='generate' name='check' min='1' max='10' value='1'>";
				
					/*
					echo "<select size='1' name='check'>";
						echo "<option value='' selected></option>";
						for ($i=1;$i<8;$i++){
							echo "<option value='".$i."'>".$i."</option> ";
						}
					echo " </select>";
					*/
					echo "<input type='submit' class='generate' name='submit' value='Добавить'>";
				echo "</form>";
				
				$allFuelingCheckUser = allFuelingCheckUser ($dbcnx, $userdata['id']);
				echo "<br><p style='color: red;'>В БАЗЕ НЕ РАСЧИТАННЫХ ЧЕКОВ: ".$allFuelingCheckUser."</p><br>";
				if ($allFuelingCheckUser > 0) {
					$allFuelingCheckUserNotUse = allFuelingCheckUserNotUse ($dbcnx, $userdata['id']);
					$r=1;
					foreach ($allFuelingCheckUserNotUse as $notUse) {
						echo $r++.". от ".$notUse['datetime']." заправлено ".$notUse['liters']." литров [ <a href=fuel_check.php?del=".$notUse['id']." title='удалить запись'> X </a> ]";
						
						echo "<br>";
					}
					echo "<br><br>";
					echo "<form name='check' method='POST' action='fuel_check.php'>";
					echo "</select><input type='submit' class='generate' name='generate' value='Сформировать документы'>";
					echo "</form>";
				}
				echo "<hr><br>";
				echo "<b>В БАЗЕ РАСЧИТАННЫЕ ЧЕКИ </b><br>";
				echo "<br>";
				echo "<div id='check'>";
				$allFuelingCheckYear = allFuelingCheckYear ($dbcnx, $userdata['id']);
				foreach ($allFuelingCheckYear as $year) {	
					echo "<b onClick=\"javascript:look('div".$year['year']."');\" >&#9660; ЧЕКИ ЗА ".$year['year']."г. [".$year['count']." чеков] &#9660;</b>";
					echo "<br><br>";
					echo "<div id='div".$year['year']."' style='display:none'>";
					$allFuelingCheckMonth = allFuelingCheckMonth ($dbcnx, $userdata['id'], $year['year']);
					foreach ($allFuelingCheckMonth as $date) {
						echo "<b>..:: Месяц ".$date['datetime']." ::..</b><br>";
							$pieces = explode(".", $date['datetime']);
						$files = scandir("/var/www/html/www/waybill/".$userdata['id']."/".$pieces[1]."/".$pieces[0]."");
						$r = 1;
						for ($i=2; $i < count($files); $i++) {
							$check = explode('.', $files[$i]);
							
							echo "<div>".$r++.". <a onclick=\" newWindow = window.open('/waybill/".$userdata['id']."/".$pieces[1]."/".$pieces[0]."/".$files[$i]."', '_blank','height=843,width=1125'); newWindow.focus(); newWindow.print();\">чек от ".$check[0]."</a>";
							$dateCheck = $pieces[1].'-'.$pieces[0].'-'.$check[0]; 
							$selectSumInDayLiters = selectSumInDayLiters ($dbcnx, $dateCheck, $userdata['id']);
							if ($selectSumInDayLiters > 0) { $selectSumInDayLiters = round($selectSumInDayLiters, 2); echo " с заправкой в ".$selectSumInDayLiters." литров"; } else { echo " без заправки, допол-ный"; }
							echo "</div>";
						}																																									 
						echo "<br><br>";
					} 
					echo "</div>";
				}
				echo "</div>";
				echo "</div>";
			}
		}
		echo "</div>";
	}
}	


/* FOOTER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");