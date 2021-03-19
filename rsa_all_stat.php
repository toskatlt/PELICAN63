<?php
include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."section/menu.php"); /* HEADER */

$date_time = date("y-m-d H:i:s");
					
echo "<div style='margin: 10px;width: 100%;height: 100%;overflow: auto;'><center>";
echo "<div class='card_body' id='card_body'>";	
echo "<b style='color:#fff;'>RSA [ СТАТИСТИКА РАБОТОСПОСОБНОСТИ КЛЮЧА ЕГАИС ]</b><br>";
echo "<b style='color: red;font-size: 30;'> OFFLINE КЛЮЧЕЙ: ".$offline[0]." </b>";

$query_offline_now = query_offline_now($dbcnx);

echo "<div style='margin-top: 10px;margin-bottom: 25px;'>";

foreach ($query_offline_now as $qon) {
		$selectAtolInObject = selectAtolInObject ($dbcnx, $qon['id']);
		echo "<b><a href='https://neo63.ru/card.php?id=".$qon['id']."' target='_blank' style='color: #666666;' title='".$qon['address']."'>".$qon['name']." [ ".$qon['ip']." ] <b style='color: red;'>".$selectAtolInObject['token']."</b></a></b>";
		$pieces = explode(".", $qon['ip']);
		if ($pieces[0] == 10 && $pieces[1] == 0) { echo ' офис'; }
		echo '<br>';
}

echo "</div>";
echo "</div>";

echo "<div style='margin-top: 10px;margin-bottom: 75px;'><div class='table2'>";
echo "<table class='tablesorter' style='margin: auto;' cellspacing='1'>";
echo "<thead>";
echo "<tr>";
echo "<th style='width: 3%;'>№</th>";
echo "<th style='width: 40%;'>Дата</th>";
echo "<th style='width: 25%;'>ЕГАИС ОФФЛАЙН</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";			
echo "<tr>";

$n = 1;
$rsa_date = rsa_date ($dbcnx); 
if (count($rsa_date) > 0) {
	foreach ($rsa_date as $a) {	
		$jacartaip=null;
		$arrj=null;
		$output=null;
		$new_output=null;

		$date = $a['date'];
		
		$rsa_date_offline = rsa_date_offline ($dbcnx, $date);	//161		
		echo "<tr>";
		echo "<td align=center>".$n++."</td>";
		echo "<td align=center>".date('H:i:s d.m.y', strtotime($date))."</td>";
		echo "<td align=center>".$rsa_date_offline."</td>";
		$offline[] = $rsa_date_offline;
		echo "</tr>";
		
		$jacartaip=null;
		$arrj=null;
		$output=null;
		$new_output=null;
	}
}
echo "</tr>";
echo "</tbody>";	
echo "</table>";
echo "</div></div>";


include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ 