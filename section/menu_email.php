<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";

if (isset($_POST['exit_cookies'])){
	setcookie("id", '');
	setcookie("hash", '');
	//setcookie("id", "", time() - 3600*24*30*12, "/");
	// setcookie("hash", "", time() - 3600*24*30*12, "/");
	header("Refresh:1"); 
}
	
echo "<form name='' method='post' id='example_group2' >";
echo "<table>";
echo "<tr>";
echo "<td><a href='/'><img src='/img/logo.png' class='logo'></a></td>";
echo "</tr>";
echo "<tr>";
echo "<nav role='navigation'>";
echo "  <ul>";
echo "    <li><a href='/'>Главная</a></li>";
echo "    <li><a href='address.php'>Магазины</a>";
echo "      <ul>";
echo "        <li><a href='address.php?group=TLT'>Тольятти</a></li>";
echo "        <li><a href='address.php?group=SMR'>Самара</a></li>";
echo "        <li><a href='address.php?group=KNL'>Кинель</a></li>";
echo "        <li><a href='address.php?group=SZN'>Сызрань</a></li>";
echo "        <li><a href='address.php?group=ZHG'>Жигулевск</a></li>";
echo "        <li><a href='address.php?group=SMR_OBL'>Самарская обл.</a></li>";
echo "      </ul>";
echo "    </li>";
echo "    <li><a href='partneram.php'>Партнерам</a></li>";

echo "</ul>";
echo "</nav>  ";
echo "</tr>";
echo "</table>";
echo "</form>";

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) { 
	$query = mysql_query("SELECT CONCAT(SUBSTRING_INDEX(`fio`, ' ', 1), ' ', SUBSTRING((SUBSTRING_INDEX(`fio`, ' ', -2)),1,1), '.') AS `fio`, `id_position`, `access`, `id`, INET_NTOA(ip) AS `ip` FROM domain_user WHERE id = '".intval($_COOKIE['id'])."'",$dbcnx);
	$userdata = mysql_fetch_assoc($query);
	if($userdata['id'] == $_COOKIE['id']) {
		if  ($userdata['id_position'] !== 0) {
			echo "	<div class='login_online'><form method='post' name='exit_cookies'>";
			if ($userdata['id_position'] == "1") {
				echo "	<p><a href='personal'>".$userdata['fio']." </a><input type='submit' name='exit_cookies' value='X' id='exit_cookies'><br>";
			} else {
				echo "	<p style='color:#408193;'><a href='check'>".$userdata['fio']." </a><input type='submit' name='exit_cookies' value='X' id='exit_cookies'><br>";
			} 					
			echo "	</form></div>";
		}
		if ($userdata['id_position'] == "1") {			
			/*$query = "SELECT count(online) FROM `rsa_online_log` WHERE date=(SELECT date FROM `rsa_online_log` GROUP BY date DESC limit 1) ";
			$result = mysql_query($query,$dbcnx);
			$row = mysql_fetch_assoc($result);
			$all = $row['count(online)'];	
			
			
			$rsa_date = rsa_date ($dbcnx);
			$arr_date = $rsa_date[0]['date'];
			$rsa_date_online = rsa_date_online ($dbcnx, $arr_date);	//161		
			$rsa_date_offline = $all-$rsa_date_online;
		*/
			// 200*день+день*месяц+33-час
			$day_today = date("d");
			$month_today = date("m");
			$hours_now = date("H");
			if ($userdata['access'] > 6){
				$adm_pass = ((200 * $day_today)+($day_today * $month_today) + (33 - $hours_now));
				echo "<div class='left_menu0'>";
				echo "<center style='padding-top:9px;color:white'><div id='pass'></div></b></center>";
			//	echo "<center style='padding-top:1px;color:white'><b> ".$rsa_date_offline." </b></center>";
				echo "</div>";
			}
			echo "	<div class='left_menu'>";
			echo "	<center><a href='/ip_pelican'><img width='27px' style='padding-top:11px' src='/img/ip.png' title='IP Пеликан'></a></center>";
			echo "</div>";
			echo "<div class='left_menu2'>";
			echo "	<center><a href='http://support.neo63.ru/scp/'><img width='27px' style='padding-top:11px' src='/img/scp.png' title='ТехПоддержка'></a></center>";
			echo "</div>";
			echo "<div class='left_menu3'>";
			echo "	<center><a href='/jabber'><img width='27px' style='padding-top:11px' src='/img/jabber.png' title='Jabber'></a></center>";
			echo "</div>";
			echo "<div class='left_menu4'>";
			echo "	<center><a href='http://forum.neo63.ru/'><img width='27px' style='padding-top:11px' src='/img/forum.png' title='Форум Пеликан'></a></center>";
			echo "</div>";
			echo "<div class='left_menu5'>";
			echo "	<center><a href='/terminals'><img width='27px' style='padding-top:11px' src='/img/terminal.png' title='Терминалы'></a></center>";
			echo "</div>";
			echo "<div class='left_menu6'>";
			echo "	<center><a href='rsa_all_stat'><img width='40px' style='padding-top:3px' src='/img/egais_logo.png' title='Статистика ЕГАИС'></a></center>";
			//echo "	<center><a href='http://neo63.ru/stats_p.php'><img width='40px' style='padding-top:3px' src='img/stat.png' title='Статистика'></a></center>";
			echo "</div>";
			echo "<div class='left_menu7'>";
			echo "	<center><a href='/zapravka'><img width='40px' style='padding-top:3px' src='/img/zapravka.png' title='Заправка картриджей'></a></center>";
			echo "</div>";
		}	
	}	
} else {
	echo "<div class='login_online'><a href=entrance><img src='img/login.png' width='30px'></a></div>";
}	
?>