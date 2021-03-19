<?php
if (isset($_POST['exit_cookies'])){
	setcookie("id", '');
	setcookie("hash", '');
	header("Refresh:1"); 
}
?>
<div class='menu'>
    <li><a href='/'>Главная</a></li>
	<li><a href='/promo/pepsi'>Акции</a></li>
    <li><a href='address'>Адреса магазинов</a></li>
	<li><a href='partneram'>Партнерам</a></li>
	
	
<?php	
if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) { 
$userdata = authorization_lite ($dbcnx, $_COOKIE['id']);
if($userdata['id'] == $_COOKIE['id']) {
		if  ($userdata['access'] > "3") { 
			echo "    <li><a href='check.php'>Админ панель</a>";
			echo "      <ul>";
			echo "        <li><a href='ip_pelican'>IP PELICAN</a></li>";
			echo "        <li><a href='ip_office'>IP OFFICE</a></li>";
			if  ($userdata['access'] > "4") {
				echo "        <li><a href='ip_mindal'>IP MINDAL</a></li>";
			//  echo "        <li><a href='telephony'>Телефония</a></li>";
				echo "    	  <li><a href='domain_user'>Пользователи</a></li>";				
				echo "    	  <li><a href='rsa'>RSA</a></li>";
				echo "        <li><a href='jabber'>Jabber</a></li>";
				echo "        <li><a href='http://192.168.0.2:9090/' target='_blank'>OpenFire</a></li>";
				echo "        <li><a href='http://forum.neo63.ru/' target='_blank'>Форум</a></li>";
			//	echo "        <li><a href='/email/' target='_blank'>Почта Неотрейд</a></li>";
				echo "        <li><a href='email.php'>Почтовые адреса</a></li>";
				echo "        <li><a href='duty_admin' target='_blank'>Дежурство</a></li>";
				echo "        <li><a href='terminals'>Терминалы</a></li>";
				echo "        <li><a href='/mpdf/index?id=".$userdata['id']."'>Накладная</a></li>";
				echo "        <li><a href='checkbd'>Проверки БД</a></li>";					
			//	echo "        <li><a href='pos_error'>Ошибки POS</a></li>";
			}
			if ($userdata['access'] > "6"){
				echo "        <li><a href='scan.php'>Сканы</a></li>";	
			}	
			echo "      </ul>";
			echo "    </li>";
		}
		echo "    <li><a href='neocam.php'>NEO CAM</a></li>";
		echo "    <li><a href='shares.php'>Акции</a></li>";
		echo "    <li><a href='radio.php'>Радио</a></li>";
	}
}
?>
	</div>
<?php
if (!empty($userdata['id'])) { 

	echo "<div class='login_online'><form method='post' name='exit_cookies'>";
	echo "<p><a href='personal'>".$userdata['fio']." </a><input type='submit' name='exit_cookies' value='X' id='exit_cookies'><br>";
	echo "</form></div>";

	$query = mysql_query("SELECT CONCAT(SUBSTRING_INDEX(`domain_user`.`fio`, ' ', 1), ' ', SUBSTRING((SUBSTRING_INDEX(`domain_user`.`fio`, ' ', -2)),1,1), '.') AS `fio`, `domain_user`.`id_position`, `domain_user`.`access`, `domain_user`.`id`, `group`.id as id_group FROM domain_user, position, `group` WHERE domain_user.id = '".intval($_COOKIE['id'])."' and domain_user.id_position=position.id and position.id_group=group.id",$dbcnx);
	$domain_user = mysql_fetch_assoc($query);
	if($domain_user['id'] == $_COOKIE['id']) {
		if($domain_user['id'] == $_COOKIE['id']) {
			if ($domain_user['id_group'] == "1") {	
				$day_today = date("d");
				$month_today = date("m");
				$hours_now = date("H");
				if ($domain_user['access'] > 6) {
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
			/*	echo "<div class='left_menu8'>";
				echo "	<center><a href='http://neo63.ru/rsa_all_stat.php'><img width='40px' style='padding-top:3px' src='img/egais_logo.png' title='Статистика ЕГАИС'></a></center>";
				echo "</div>"; */
			}	
		}
	}	
} else { echo "<div class='login_online'><a href=entrance><img src='img/login.png' width='30px'></a></div>"; }	

echo "<body class='gradient'>";
?>

<script>
function pass() {
	var date = new Date();
	var month = date.getMonth();
	var month = ++month;
	var day = date.getDate();
	var hour = date.getHours();
	//var hour = date.getSeconds();
	var pass = ((400 * day) + (day * month) + (33 - hour));
	document.getElementById('pass').innerHTML = pass;
	window.setTimeout(arguments.callee, 1000);
}
window.onload = pass;
</script>