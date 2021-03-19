<?php
header('Content-type: text/html; charset=utf-8');
require_once("../config.php");
require_once("../function/function_object.php");
$uploaddir = '/var/www/html/uploads/';
$uploadfile = $uploaddir . basename('IP.CSV');

function a ($dbcnx, $kod_sklada) {
	$query = mysql_query("SELECT * FROM `south_conf` WHERE kod_sklada='".$kod_sklada."' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

try {
    if (
        !isset($_FILES['data']['error']) ||
        is_array($_FILES['data']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }
    switch ($_FILES['data']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }
    if ($_FILES['data']['size'] > 1000000) {
        throw new RuntimeException('Exceeded filesize limit.');
    }
    if (move_uploaded_file($_FILES['data']['tmp_name'], $uploadfile)) {
        $data = file_get_contents($uploadfile);
        file_put_contents($uploadfile, $data);
    } else {
        throw new RuntimeException('ERROR! File is not saved.');
    }
} catch (RuntimeException $e) {
    echo $e->getMessage();
}

//echo "<br>";
//echo "<br>";

$handle = fopen($uploadfile, "r");
while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
    for ($c=0; $c < 1; $c++) {
		$data[1]= iconv("cp866","utf8",$data[1]);
		$data[2]= iconv("cp866","utf8",$data[2]);
		$data[2] = trim($data[2]);
		$data[3]= iconv("cp866","utf8",$data[3]);

		$a = null;	
		$a = a ($dbcnx, $data[0]);
		if (isset($a['id_object'])) {
			//echo $data[0]." - kod sklada <br>";	
			//echo "<br>";
			
			// DEL SPACES
			$data[2]= preg_replace("/  +/"," ",$data[2]);  
			// DEL SPACES
			//echo        "INSERT INTO `egais`(`id_object`, `ip`) VALUES ('".$a['id_object']."','".$data[2]."') ON DUPLICATE KEY UPDATE `ip`='".$data[2]."' ";
			$selectAtolInObject = selectAtolInObject ($dbcnx, $id_object);
			
			if($selectAtolInObject) {
				mysql_query("UPDATE `ip`='".$data[2]."' WHERE `id_object`='".$id_object."'", $dbcnx);	
			} else {
				mysql_query("INSERT INTO `egais` (`id_object`, `ip`) VALUES ('".$a['id_object']."','".$data[2]."')", $dbcnx);
			}		
			
			//echo "<br>";
			//echo "<hr>";
			//echo "<br>";
		}
	}
}
fclose($handle);
