<?php
header('Content-type: text/html; charset=utf-8');
include "/var/www/html/www/config.php";
$uploaddir = '/var/www/html/uploads/';
$uploadfile = $uploaddir . basename('DOP_COMM.TXT');

//echo $uploadfile."<br>";
function a ($dbcnx, $kod_sklada) {
	$query = mysql_query("SELECT * FROM `south_conf` WHERE kod_sklada='".$kod_sklada."' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

$handle = fopen($uploadfile, "r");
while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
    for ($c=0; $c < 1; $c++) {
		$data[1]= iconv("cp866", "utf8", $data[1]);

		$a = null;	
		$a = a ($dbcnx, $data[0]);
		if (isset($a['id'])) {
			//echo $a['id_object']." - kod sklada <br>";	
			//echo "<br>";
			
			// DEL SPACES
			//$data[1]= preg_replace("/  +/"," ",$data[1]);  
			$name= trim($data[1]);  
			// DEL SPACES
			echo "UPDATE `object` SET `name_south`='".$name."' WHERE `id`='".$a['id_object']."'<br><br>";
			mysql_query("UPDATE `object` SET `name_south`='".$name."' WHERE `id`='".$a['id_object']."'", $dbcnx);
			
			echo "<br>";
			echo "<hr>";
			echo "<br>";
			
		}
	}
}
fclose($handle);

?>
