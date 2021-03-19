<?php
// ИМПОРТ ДАННЫХ ДИРЕКТОРОВ ИЗ ДОКУМЕНТА out.csv В ТАБЛИЦУ kod1c

header('Content-type: text/html; charset=utf-8');
include "/var/www/html/www/config.php";
$uploaddir = '/var/www/html/uploads/';
$uploadfile = $uploaddir . basename('out.csv');

//echo $uploadfile." <br><br><br>";
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
   // echo $e->getMessage();
}

//echo "<br><br><br>";

$i = 1;

$handle = fopen($uploadfile, "r");
while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	
	if ($i == 1) { $data[0] = mb_substr($data[0], 1); }
	
	/*
	echo $data[0]." - fio <br>";
	echo $data[1]." - fio <br>";
	echo $data[2]." - phone <br>";
	echo "UPDATE `kod1c` SET `fio`='".$data[1]."',`phone`='".$data[2]."' WHERE `kod`='".$data[0]."' <br>";
	echo $i."<br><br><hr><br>";
	*/
	$i++;
	
	$data[2] = str_replace("#", " ", $data[2]);
	mysql_query("UPDATE `kod1c` SET `fio`='".$data[1]."',`phone`='".$data[2]."' WHERE `kod`='".$data[0]."' ", $dbcnx);
	
}
fclose($handle);

?>
