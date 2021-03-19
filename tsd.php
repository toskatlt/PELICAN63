<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

$date = date("Y-m-d");

function tsdAllLog ($dbcnx240, $mac) {
	$query = mysql_query("SELECT * FROM `tsd_log` WHERE `mac` = '".$mac."' ORDER BY `datetime` ASC", $dbcnx240);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function objectFindByMask ($dbcnx, $mask) {
	$mask = substr($mask, 0, -2);
	$query = mysql_query("SELECT object.name FROM internet, object WHERE object.id = internet.id_object and internet.mask = '".$mask."'", $dbcnx);	
	$n = mysql_num_rows($query);	
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) { 
	$userdata = authorization ($dbcnx, $_COOKIE['id']);	
	if($userdata['id_group'] == "1") {
		echo "<div class='card_body' id='card_body'>";
		echo "<div class='link'><a href='ip_pelican.php' title='Магазины'><img width='20px' src='img/back_arrow.png'></a></div>";
		echo "<div class='card'>";
			
		echo "<br><center><b>ЛОГИ ТСД с MAC-адресом [ ".$_GET['mac']." ]</b></center><br><br>";
		echo "<table><tr>";
		
			$tsdAllLog = tsdAllLog ($dbcnx240, $_GET['mac']);
			
			foreach ($tsdAllLog as $tsd) {			
				$objectFindByMask = objectFindByMask ($dbcnx, $tsd['lanip']);
				echo "<tr><th style='font-weight:normal;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'>";
				if (!$objectFindByMask[0]['name']) {
					echo $tsd['lanip'];
				} else {	
					echo $objectFindByMask[0]['name'];
				}
				echo " - </th><th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'> ".$tsd['datetime'];
				echo "</th></tr>";
			}
			
		echo "</tr></table>";
		echo "<br><br>";
		echo "</div></div>";
	}
}	