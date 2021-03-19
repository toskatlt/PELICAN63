<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

function selectSgrt ($grt, $dbcnx_tc) {
	$query = mysql_query("SELECT cod_sgrt, sgrt FROM `south_group` WHERE cod_grt='".$grt."' ORDER BY `south_group`.`sgrt` ASC", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i=0; $i<$n; $i++) { 
		$result[] = mysql_fetch_assoc($query); 
	}
	return $result;
}


function selectGrt ($dbcnx_tc) {
	$query = mysql_query("SELECT cod_grt, grt FROM `south_group` group by cod_grt ORDER BY `south_group`.`grt` ASC", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

$selectGrt = selectGrt ($dbcnx_tc);
echo '<div class="container details">';
	foreach ($selectGrt as $grt) {
		echo '<details>';
			echo '<summary class="grt-sum">'.$grt['grt'].' '.$grt['cod_grt'].'</summary>';
			$selectSgrt = selectSgrt ($grt['cod_grt'], $dbcnx_tc);
			foreach ($selectSgrt as $sgrt) {
				echo '<p class="sgrt">'.$sgrt['sgrt'].' '.$sgrt['cod_sgrt'].'</p>';
			}
		echo '</details>';
		echo '<br>';
	}
echo '</div>';








include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); ?>
