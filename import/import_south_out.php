<?php
header("Content-Type: text/html; charset=utf8");
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

class barcode {

  protected static $code39 = array(
    '0' => 'bwbwwwbbbwbbbwbw', '1' => 'bbbwbwwwbwbwbbbw',
    '2' => 'bwbbbwwwbwbwbbbw', '3' => 'bbbwbbbwwwbwbwbw',
    '4' => 'bwbwwwbbbwbwbbbw', '5' => 'bbbwbwwwbbbwbwbw',
    '6' => 'bwbbbwwwbbbwbwbw', '7' => 'bwbwwwbwbbbwbbbw',
    '8' => 'bbbwbwwwbwbbbwbw', '9' => 'bwbbbwwwbwbbbwbw',
  );

  public static function code39($text) {
    if (!preg_match('/^[A-Z0-9-. $+\/%]+$/i', $text)) {
      throw new Exception('Ошибка ввода');
    }

    $text = '*'.strtoupper($text).'*'; 
    $length = strlen($text);
    $chars = str_split($text);
    $colors = '';

    foreach ($chars as $char) {
      $colors .= self::$code39[$char];
    }

    $html = '
            <div style=" float:left;">
            <div>';

    foreach (str_split($colors) as $i => $color) {
      if ($color=='b') {
        $html.='<SPAN style="BORDER-LEFT: 0.02in solid; DISPLAY: inline-block; HEIGHT: 1in;"></SPAN>';
      } else {
        $html.='<SPAN style="BORDER-LEFT: white 0.02in solid; DISPLAY: inline-block; HEIGHT: 1in;"></SPAN>';
      }
    }

    $html.='</div>
            <div style="float:left; width:100%;" align=center >'.$text.'</div></div>';
  //  echo htmlspecialchars($html);
    echo $html;
  }

}

	include "/var/www/html/www/config.php";
	$uploaddir = '/var/www/html/uploads/';
	$uploadfile = $uploaddir . basename('OUT.TXT');

//echo $uploadfile."<br>";
/*
function a ($dbcnx, $kod_sklada) {
	$query = mysql_query("SELECT * FROM `south_conf` WHERE kod_sklada='".$kod_sklada."' ", $dbcnx);
	$result = mysql_fetch_assoc($query);
	return $result;
}*/
	exec('cat /var/www/html/uploads/OUT.TXT | iconv -f cp866 -t utf8 > /var/www/html/uploads/OUT2.TXT', $output);
	$uploadfile = $uploaddir . basename('OUT2.TXT');
	
	
$handle = fopen($uploadfile, "r");

$file_array =  file($uploadfile);
$num_str =  count($file_array);
$num_str-=4;

function searchAlkoKod ($dbcnx, $alko) {
	$query = mysql_query("SELECT * FROM `alko_south` WHERE `name`='".$alko."' ", $dbcnx);	
	$row = mysql_fetch_assoc($query);
	return $row['alkokod'];
}	


for ($i = 0; $i < $num_str; $i++) {

	if ($i == 3) {
		preg_match('/Кому\s+:\s+(.*?)\s#/usi', $file_array[$i], $sklad);
		echo $sklad[1]." - Строка со складом<br>";
			echo "<br>";
			echo "<hr>";
			echo "<br>";
	}	
	if ($i > 7) {
		
		preg_match('/\d+\s(.*?)\s(бут|шт)/usi', $file_array[$i], $alko);
		
		if(strpos($alko[1], 'H')) {
			$alko[1] = preg_replace('/H/', 'Н', $alko[1]);
		}
		
		$searchAlkoKod = searchAlkoKod ($dbcnx, $alko[1]);
		echo $alko[1]." <b>".$searchAlkoKod."</b> <img src='http://chart.apis.google.com/chart?cht=qr&chs=300x300&chl=".$searchAlkoKod."'>";
			echo "<br>";
			echo "<hr>";
			echo "<br>";
	}	
}

fclose($handle);
	
	//str_replace('&nbsp;', ' ', $contents);
	//echo $contents;
	
	//preg_match( '/Кому.*?:(.*?)#/usi' , $contents , $to); 	