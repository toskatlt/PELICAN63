<?php
//include $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$timestamp = date('Y-m-d G:i:s');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   АКЦИИ   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function southAllGroup ($dbcnx_tc) {
	$query = mysql_query("SELECT id, cod_grt, grt FROM `south_group` group by cod_grt ORDER BY `south_group`.`grt` ASC", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectAllSouthGroup ($dbcnx_tc) {
	$query = mysql_query("SELECT * FROM `south_group` ORDER BY `south_group`.`sgrt` ASC", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectSouthGroup ($dbcnx_tc, $cod_grt) {
	$query = mysql_query("SELECT * FROM `south_group` WHERE cod_grt='".$cod_grt."' ORDER BY `south_group`.`sgrt` ASC", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function shares_future ($dbcnx_tc, $timestamp) {
	$query = mysql_query("SELECT * FROM `company` WHERE `start` > '".$timestamp."'", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function shares_startup ($dbcnx_tc, $timestamp) {
	$query = mysql_query("SELECT * FROM `company` WHERE `start` >= '".$timestamp."' AND `stop` >= '".$timestamp."'", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function shares_close ($dbcnx_tc, $timestamp) {
	$query = mysql_query("SELECT * FROM `company` WHERE `stop` < '".$timestamp."'", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectCompanyGroup ($dbcnx_tc, $id_company) {
	$query = mysql_query("SELECT * FROM `company_group` WHERE `id_company` = '".$id_company."'", $dbcnx_tc);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectSouthGroupGrt ($dbcnx_tc, $cod_grt) {
	$query = mysql_query("SELECT count(*), `id`, `cod_grt`, `grt`, `cod_sgrt`, `sgrt` FROM `south_group` WHERE `cod_grt` = '".$cod_grt."'", $dbcnx_tc);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function selectSouthGroupSgrt ($dbcnx_tc, $cod_sgrt) {
	$query = mysql_query("SELECT * FROM `south_group` WHERE `cod_sgrt` = '".$cod_sgrt."'", $dbcnx_tc);
	$result = mysql_fetch_assoc($query);
	return $result;
}
