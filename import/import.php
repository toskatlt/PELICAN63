<?php
// ДОБАВЛЕНИЕ НОВЫХ ОБЪЕКТОВ В БД ЧЕРЕЗ СКАНИРОВАНИЕ ФАЙЛА DOP_COMM.TXT | 25.03.2019

header('Content-type: text/html; charset=utf-8');
include "/var/www/html/www/config.php";
$uploaddir = '/var/www/html/uploads/';
$uploadfile = $uploaddir . basename('DOP_COMM.TXT');

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
	echo '<br><br>';
}

function a ($dbcnx, $kod_sklada) {
	$query = mysql_query("SELECT * FROM `south_conf` WHERE kod_sklada='".$kod_sklada."' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}

function b ($dbcnx, $name) {
	$query = mysql_query("SELECT id FROM `object` WHERE name='".$name."' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}


$handle = fopen($uploadfile, "r"); // открываем и читаем файл DOP_COMM
while (($data = fgetcsv($handle, 2500, "|")) !== FALSE) { // делим строки на части разделителем "|"
    for ($c=0; $c < 1; $c++) {
		
		$data[1]= iconv("cp866", "utf8", $data[1]); // адрес объекта
		$data[2]= iconv("cp866", "utf8", $data[2]); // COMM
		$data[3]= iconv("cp866", "utf8", $data[3]); // имя склада
		$data[4]= iconv("cp866", "utf8", $data[4]); // полный адресс
		
			$data[1] = preg_replace("/  +/"," ",$data[1]);  // убираем лишние пробелы
			
			$name = trim($data[1]); // удаление пробелов
			$data[3] = trim($data[3]); // удаление пробелов
			
			$b = b($dbcnx, $name);
			
		$a = null;	
		$a = a ($dbcnx, $data[0]); // проверка кода склада в сауте, если код склада не обнаружен, добавляем новый магазин в БД
		//var_dump($a);
		//echo '<br><br>';
		
		if (!isset($a['id'])) { // ЕСЛИ КОД СКЛАДА НЕ ОБНАРУЖЕН В ТАБЛИЦЕ south_conf СРАБАТЫВАЕТ УСЛОВИЕ
			
			preg_match('/Самарская обл., г. Тольятти,\s(.*?)$/sui', $data[4], $address);
			if (isset($address[1])){ echo $address[1]."<br>";}
		//echo "<br>";
			
			if (stristr($data[4], 'Пеликан')) { $type = 'ПЕЛИКАН'; }
			elseif (stristr($data[4], 'Жигулевское')) { $type = 'ЖИГУЛЕВСКОЕ МОРЕ'; }
			elseif (stristr($data[4], 'ЖИГУЛЕВСКОЕ')) { $type = 'ЖИГУЛЕВСКОЕ МОРЕ'; }
			//else {$type = 'NULL';}
			// DEL SPACES 
			
			mysql_query("INSERT INTO `building`(`address`) VALUES ('".$data[4]."')", $dbcnx);
		//echo "INSERT INTO `building`(`address`) VALUES ('".$data[4]."') <br>";
			$query = mysql_query("SELECT LAST_INSERT_ID()", $dbcnx);
			$LAST_INSERT_ID = mysql_fetch_array($query);	
			
			// DEL SPACES
			//echo "INSERT INTO `object`(`id_building`, `type`, `name`, `open`, `name_south`) VALUES ('".$LAST_INSERT_ID[0]."','".$type."','".$name."','2','".$data[1]."')";
			mysql_query("INSERT INTO `object`(`id_building`, `type`, `name`, `open`, `name_south`) VALUES ('".$LAST_INSERT_ID[0]."','".$type."','".$name."','2','".$data[1]."')", $dbcnx);

			
			$a = a ($dbcnx, $data[3]);
			preg_match('/(COMM\d+)/sui', $data[2], $comm);
		//echo "INSERT INTO `south_conf`(`id_object`, `kod_sklada`, `group_chk`, `COMM`) VALUES ('".$b['id']."','".$data[0]."','".$data[3]."','".$comm[1]."')";
			mysql_query("INSERT INTO `south_conf`(`id_object`, `kod_sklada`, `group_chk`, `COMM`) VALUES ('".$b['id']."','".$data[0]."','".$data[3]."','".$comm[1]."')", $dbcnx);
			mysql_query("INSERT INTO `internet`(`id_object`) VALUES ('".$b['id']."')", $dbcnx);
			mysql_query("INSERT INTO `egais`(`id_object`) VALUES ('".$b['id']."')", $dbcnx);
			
		//echo "<br>";
		//echo "<hr>";
		//echo "<br>";
		} else {
			mysql_query("INSERT INTO `south_conf`(`id_object`, `group_chk`) VALUES ('".$b['id']."', '".$data[3]."'", $dbcnx);
			mysql_query("UPDATE `south_conf` SET `group_chk`='".$data[3]."' WHERE `id_object`='".$a['id_object']."'", $dbcnx);
		//echo "UPDATE `south_conf` SET `group_chk`='".$data[3]."' WHERE `id_object`='".$a['id_object']."'<br>";
		}	
	}
}

fclose($handle);

