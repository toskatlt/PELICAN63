<?php
include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */



$allJacartaBuilding = allJacartaBuilding ($dbcnx);

echo "<div class='table2' style='top: 50;'>";
echo "<table class='tablesorter' style='margin: auto;' cellspacing='1'>";

echo "<thead><tr>";
echo "<th style='width: 2%;'>№</th>";
echo "<th style='width: 20%;'>Пеликан</th>";
echo "<th style='width: 20%;'>RSA</th>";
echo "<th style='width: 20%;'>КПП</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";			
echo "<tr>";
echo "<td align=center>1</td>";
echo "<td align=center>Склад НЕОТРЕЙД</td>";
echo "<td align=center>030000005564</td>";
echo "<td align=center>632145082</td>";
echo "</tr>";

$i = 2;
foreach ($allJacartaBuilding as $arr) {

	$arrj = $arr['ip_ws'];
	$arrn = $arr['name'];
	$shop_id = $arr['id'];
	$shop_rsa_in_base = $arr['rsa'];
	$shop_address = $arr['address'];
	$shop_kpp = $arr['kpp'];
	$jacarta_num = $arr['jacarta_num'];

	echo "<tr>";
	echo "<td align=center>".$i++."</a></td>";
	echo "<td align=center>".$arrn." ( ".$shop_address." )</td>";
	if ($shop_rsa_in_base == '0'){
		echo "<td align=center'></td>";
	}
	else {
		echo "<td align=center>".$shop_rsa_in_base."</td>";
	}
	if ($shop_kpp == '0'){
		echo "<td align=center'></td>";
	}
	else {
		echo "<td align=center>".$shop_kpp."</td>";
	}
	echo "</tr>";
}
echo "</tbody>";	
echo "</table>";
echo "</div>";
echo "<br><br>";		

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ 