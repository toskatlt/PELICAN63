<?php
//include $_SERVER["DOCUMENT_ROOT"]."/config.php";

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) { 
$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['id'] == $_COOKIE['id']) {
		if  ($userdata['id_position'] == "1") {
			$day_today = date("d");
			$month_today = date("m");
			$hours_now = date("H");
			echo $userdata['access'].'<br>';
			if ($userdata['access'] > 11){
				$adm_pass = ((400 * $day_today)+($day_today * $month_today) + (33 - $hours_now));
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
			echo "</div>";
			echo "<div class='left_menu7'>";
			echo "	<center><a href='/zapravka'><img width='40px' style='padding-top:3px' src='/img/zapravka.png' title='Заправка картриджей'></a></center>";
			echo "</div>";
		}	
	}
}	
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