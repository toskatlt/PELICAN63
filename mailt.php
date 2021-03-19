<?php
header("Content-Type: text/html; charset=utf-8");
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

include "config.php";
require_once("function/function_object.php");

$ds = ldap_connect("");  // предположим, что сервер LDAP находится тут

if ($ds) {
	
	$dn = "ou=people,dc=neo63,dc=ru";
	$filter = "(|(objectClass=*))";
	$justthese = array("dn");
	$sr = ldap_search($ds, $dn, $filter, $justthese);
	
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_bind($ds, "cn=admin,dc=neo63,dc=ru", "");
	
	ldap_control_paged_result($ds, 1000, true);
	
	$array = ldap_get_entries($ds, $sr);
	echo $array['count'];	
	echo "<br><br>";
	
	echo "<hr><br> Удаляем записи... <br>";
		
	for ($i=1; $i < $array['count']; $i++) {
		echo $array[$i]['dn']." - ".$i." <br>";
		ldap_delete($ds, $array[$i]['dn']);
	}
	
	echo "<br> Записи удалены... <br><hr>";
	$array2 = ldap_get_entries($ds, $sr);
	echo $array2['count'];	
	echo "<br><br>";
	
	$allUser = allUserPA ($dbcnx_pf); $i=0;	
	
	foreach ($allUser as $mail) {
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		// привязка к соответствующему dn для возможности обновления
		ldap_bind($ds, "cn=admin,dc=neo63,dc=ru", "");
		
		if (!empty($mail['name'])) {
			$info["cn"] = $mail['name'];
			$first_name = explode (' ', $mail['name']);
			$info["sn"] = $first_name[0];
		} else {	
			$info["cn"] = $mail['local_part'];
			$info["sn"] = $mail['local_part'];
		}
		$info["uidNumber"] = "11".$i++."";
		$info["gidNumber"] = "1100";
		$info["userPassword"] = "1";
		$info["homeDirectory"] = "/home/".$mail['local_part']."";
		$info["objectClass"][0] = "inetOrgPerson";
		$info["objectClass"][1] = "posixAccount";
		$info["objectClass"][2] = "top";
		$info["mail"] = $mail['username'];

		
		//echo $info["cn"]."<br>";
		//echo $info["sn"]."<br>";
		//echo $info["uidNumber"]."<br>";
		//echo $info["gidNumber"]."<br>";
		//echo $info["userPassword"]."<br>";
		//echo $info["homeDirectory"]."<br>";
		//echo $info["objectClass"][0]."<br>";
		//echo $info["objectClass"][1]."<br>";
		//echo $info["objectClass"][2]."<br>";
		//echo $info["mail"]."<br>";
		//echo "<br>";
		//echo "<br>";
		
		// Добавление данных
		ldap_add($ds, "uid=".$mail['local_part'].",ou=people,dc=neo63,dc=ru", $info);
	}	
} else {
    echo "Не могу соединиться с сервером LDAP";
}

ldap_close($ds);
?>