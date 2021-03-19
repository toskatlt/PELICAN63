<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";

 
if (isset($_GET['grt'])) {
	$query = mysql_query("SELECT cod_sgrt, sgrt FROM `south_group` WHERE cod_grt='".$_GET['grt']."' ORDER BY `south_group`.`sgrt` ASC", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i=0; $i<$n; $i++) { 
		$result[] = mysql_fetch_assoc($query); 
	}
}

if (isset($_GET['null'])) {
	$query = mysql_query("SELECT cod_grt, grt FROM `south_group` group by cod_grt ORDER BY `south_group`.`grt` ASC", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
}

print json_encode($result);