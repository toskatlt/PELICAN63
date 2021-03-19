<?php
include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."section/menu.php"); /* MENU */

require_once("function/function_staff.php");

if(isset($_GET['t'])) { $time = $_GET['t']; } else { $time = '30'; }

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['id_group'] == "1") {
		echo "<br><br><br><br>";
		echo "<div id='card_body'>";
		echo "<div class='link'><a href='personal.php' title='Магазины'><img width='20px' src='img/back_arrow.png'></a></div>";
		echo "<div class='card'>";
/////////////////////////// КОЛИЧЕСТВО ОТКРЫТЫХ МАГАЗИНОВ АДМИНИСТРАТОРАМИ				
		if (isset($_GET['o'])) {
			echo "<br><center><b>КОЛИЧЕСТВО ОТКРЫТЫХ МАГАЗИНОВ АДМИНИСТРАТОРАМИ	 </b></center><br><br>";
			echo "<table><tr>";
			$allCountOpenShopUser = allCountOpenShopUser ($dbcnx);
			foreach ($allCountOpenShopUser as $acosu) {
				echo "<tr><th style='font-weight:normal;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>";
				echo $acosu['fio'];
				echo " - </th><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'> ".$acosu['count'];
				echo "</th></tr>";
			}
			echo "</tr></table>";
			echo "<br><br>";
		}
/////////////////////////// СУММА ЗАКРЫТЫХ ЗАЯВОК 			
		else {
			echo "<br><center><b>СУММА ЗАКРЫТЫХ ЗАЯВОК ";
			if ($time == '9999'){
				echo "ВСЕГО";
			}
			else {
				echo "ЗА ".$time." ДНЕЙ";
			}
			echo "</b></center><br>";
			echo "<table><tr>";
			$count_staff = count_staff ($dbcnx04, $time);
			$a = AllAdminOnline ($dbcnx);
			//var_dump($count_staff);
			for ($i=0;$i<count($count_staff);$i++){
				echo "<tr><th style='font-weight:normal;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>";
				echo $count_staff[$i]['firstname']." ".$count_staff[$i]['lastname'];
				echo " </th><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'> - ".$count_staff[$i]['COUNT(ost_ticket.staff_id)'];
				echo "<br>";

				echo "</th></tr>";
			}
			echo "</tr></table>";
			echo "<br><br><a href='?t=9999'>ВСЕГО</a>  | <a href='?t=7'>7</a> | <a href='?t=14'>14</a> | <a href='?t=30'>30</a> | <a href='?t=90'>90</a> | <a href='?t=180'>180</a> | <a href='?t=360'>360</a> | <a href='?t=720'>720</a> </div><br>";
		}
		echo "</div></div>";
	}	
}

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ 		