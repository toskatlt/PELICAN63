<?php
header("Content-Type: text/html; charset=utf8");
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

	include "/var/www/html/www/config.php";
	$uploaddir = '/var/www/html/uploads/';
	$uploadfile = $uploaddir . basename('o5.txt');
	
	//exec('cat /var/www/html/uploads/o5.txt | iconv -f cp1251 -t utf8 > /var/www/html/uploads/o5_2.txt', $output);
	//$uploadfile = $uploaddir . basename('o5_2.txt');
	
	
$handle = fopen($uploadfile, "r");
$file_array =  file($uploadfile);
	
	preg_match_all('/<rst:Quantity>(.*?)</usi', $file_array[0], $out);
	preg_match_all('/<pref:AlcCode>(.*?)</usi', $file_array[0], $out2);
	
	preg_match_all('/<pref:FullName>(.*?)</usi', $file_array[0], $out3);
	
	//var_dump($out);
	$s=1;
	for ($i=0; $i < count($out[0]); $i++) {
		
		echo $s++.". Код: ".$out2[1][$i]." кол-во: ".$out[1][$i]." | ".$out3[1][$i]."<br>";
	
	}	
	
//var_dump($file_array);	
	