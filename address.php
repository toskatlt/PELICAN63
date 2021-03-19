<?php
include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."section/menu.php"); /* MENU */

// Connects to your Database 
include "config.php";
if (isset($_GET['group'])) {
	$group = $_GET['group'];
} else { $group = 'TLT'; }

function allObjectGroup ($dbcnx, $group) {
	$query = mysql_query("SELECT building.address, building.area, object.* FROM building, object WHERE building.id=object.id_building and object.open in (1) and building.area='".$group."' and  object.type = 'ПЕЛИКАН' ORDER BY `object`.`name` ASC", $dbcnx);	
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

$allObject = allObjectGroup ($dbcnx, $group);

?>
<script type='text/javascript' src='/jquery/jquery-latest.js'></script>
<script type='text/javascript' src='/jquery/jquery.tablesorter.js'></script>

<script type='text/javascript'>
$(document).ready(function(){
     $('#myTable').tablesorter();
    }
)
</script>
<style>
A {  
	text-decoration: none; 
	color: #0fb2f0; 
}
A:visited { color: #29bed9; }
A:active { color: #016084; }
</style>

<?php

echo "<div style='margin-bottom: 70px;'>";
	echo "<form name='' action='' method='post' id='example_group2'>";
    echo "<table  id='myTable' class='tablesorter'>";
echo "<thead>";
    echo "<tr>";
    echo "<th scope='col' style='width: 2%;'>№</th>";
    echo "<th scope='col'>Название магазина</th>";
	echo "<th scope='col'>Время работы</th>";
    echo "</tr>";
echo "</thead>";
$y=1;
if (!empty($allObject)) {
	foreach ($allObject as $row ) {
		echo "<tr>";
		echo "<td align=left width='40'>".$y++."</td>";
		echo "<td align=left width='430'><a href='/map.php?id=".$row['id']."' target='_blank'  title='".$row['name']."'>".$row['address']."</a></td> ";
		$selectSouthConfInObject = selectSouthConfInObject ($dbcnx, $row['id']);
		echo "<td align=left width='150'><b>".date("G:i", strtotime($selectSouthConfInObject['omIn']))."</b> - <b>".date("G:i", strtotime($selectSouthConfInObject['omOut']))."</b></td> ";	
		
		echo "</tr>";
	}
}
echo "</table>";
echo "</form>";
echo "</div>";

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ 

