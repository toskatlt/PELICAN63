<?php
include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */

function scanFolder ($folder) {
	$count = count(scandir($folder));
	$ittem = scandir($folder);
	for ($i = 2; $i < $count; $i++) {	
		if (is_dir($folder."/".$ittem[$i])) echo "<b>".$ittem[$i]." - папка </b><br>"; 
		elseif (is_file($folder."/".$ittem[$i])) echo $ittem[$i]." - файл <br>";		
	}	
}

$folder = '\\192.168.0.191\noname\1_5PRO';
scanFolder($folder);