<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_object.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_terminals.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_jabber.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_admin.php");

echo "<link type='text/css' rel='stylesheet' href='/highslide/highslide.css'/>";
echo "<script type='text/javascript' src='/highslide/highslide.js'></script>";
echo "<script type='text/javascript' src='/jquery/jquery.min.js'></script>";
echo "<script type='text/javascript' src='/jquery/jquery.metadata.js'></script>";
echo "<script type='text/javascript' src='/jquery/jquery.tablesorter.js'></script>";
echo "<script type='text/javascript' src='/js/js.js'></script>";
echo "<script type='text/javascript' src='/js/spin/spin.js'></script>";
echo "<script type='text/javascript' src='/js/spin/hideshow.j'></script>";

/*
function a ($dbcnx) {
	$query = mysql_query("SELECT SUBSTRING_INDEX(ws.ip, '.', 3) as ip FROM ws, object, building as b WHERE ws.id_object=object.id and b.id=object.id_building and object.type = 'ПЕЛИКАН' and object.open=1 and ws.os LIKE '%linux%' and b.area IN ('SMR','TLT') GROUP BY SUBSTRING_INDEX(ws.ip, '.', 3)", $dbcnx);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

$array = a($dbcnx);

$str = '';
$count = count($array);
$i = 1;
foreach ($array as $arr) {
	if ($count == $i++) {
		$str .= 'name ~ '.$arr[ip].'.';
	} else {
		$str .= 'name ~ '.$arr[ip].'. or ';
	}	
}

echo $str.'<br>';
*/

if (isset($_POST['id_object'])) {
	$id_object = $_POST['id_object'];

	$videoCamSelectInObject = videoCamSelectInObject ($dbcnx, $id_object);

	if (!file_exists("video/".$id_object)) { mkdir("video/".$id_object, 0777); }

	$ctx = stream_context_create(array('http'=>
		array(
			'timeout' => 1,
		)
	));
}


/*
if (isset($_GET['id_object'])) {
	$id_object = $_GET['id_object'];
}
*/


foreach ($videoCamSelectInObject as $vcsio) {
	$output = null;
	exec('ping -c1 -w1 '.$vcsio['ip'].' > /dev/null && echo 1|| echo 0', $output);
	
	if (!file_exists("video/".$id_object."/".$vcsio['ip'])) { mkdir("video/".$id_object."/".$vcsio['ip'], 0777); }
	if (!file_exists("video/".$id_object."/".$vcsio['ip']."/full/")) { mkdir("video/".$id_object."/".$vcsio['ip']."/full/", 0777); }
	
	$width = '1200';
	$height = '982';
	
	$randval = rand();
	
	if ($vcsio['model'] == 'RVIHD') {
		if ($output[0] == '1') {
			echo "<center><b>{$vcsio['model']} [{$vcsio['ip']}]</b></center><br>";
			for ($i = 1; $i <= $vcsio['channel']; $i++) {		
			
				$pic_com = file_get_contents("http://admin:".$vcsio['pass']."@".$vcsio['ip']."/ISAPI/Streaming/channels/".$i."01/picture", false, $ctx);
	
				file_put_contents("video/".$id_object."/".$vcsio['ip']."/".$i.".jpg", $pic_com);
				
				$img = "video/".$id_object."/".$vcsio['ip']."/".$i.".jpg";
				$imsize = getimagesize($img);
				
				$width_img = $imsize[0]; //ширина	
				$height_img = $imsize[1]; // высота
				
				resize($width, $height, $i, $width_img, $height_img, $id_object, $vcsio['ip']);
				
				echo "<a href='video/".$id_object."/".$vcsio['ip']."/full/".$i.".jpg?n=".$randval."' class='highslide' onclick='return hs.expand(this)'><img src='video/".$id_object."/".$vcsio['ip']."/".$i.".jpg?n=".$randval."' style='width: 127px;' class='highslide'></a>";
				if (($i == '4') or ($i == '8') or ($i == '12')) {
					echo "<br>";
				}
			}
		} else { echo "<center><b style='color: red;'>!! RVIHD [".$vcsio['ip']."] !!</b></center><br>"; }
	}
	elseif ($vcsio['model'] == 'RVIHDR') {
		if ($output[0] == '1') {
			echo "<center><b>{$vcsio['model']} [{$vcsio['ip']}]</b></center><br>";
			for ($i = 1; $i <= $vcsio['channel']; $i++) {
				
				exec("avconv -rtsp_transport tcp -i 'rtsp://admin:".$vcsio['pass']."@".$vcsio['ip'].":554/cam/realmonitor?channel=".$i."&subtype=0' -f image2 -vframes 1 -pix_fmt yuvj420p video/".$id_object."/".$vcsio['ip']."/".$i.".jpg", $output);

				//echo "avconv -rtsp_transport tcp -i 'rtsp://admin:".$vcsio['pass']."@".$vcsio['ip'].":554/cam/realmonitor?channel=".$i."&subtype=0' -f image2 -vframes 1 -pix_fmt yuvj420p video/".$id_object."/".$vcsio['ip']."/".$i.".jpg<br><br>";
				//echo "/usr/bin/ffmpeg -rtsp_transport tcp -i 'rtsp://admin:".$vcsio['pass']."@".$vcsio['ip'].":554/cam/realmonitor?channel=".$i."&subtype=0&proto=Onvif' -vcodec copy -f mjpeg -vframes 1 -r 1 video/".$id_object."/".$vcsio['ip']."/".$i.".jpg<br><br>";

				$img = "video/".$id_object."/".$vcsio['ip']."/".$i.".jpg";
				$imsize = getimagesize($img);
				
				$width_img = $imsize[0]; //ширина	
				$height_img = $imsize[1]; // высота
				
				resize($width, $height, $i, $width_img, $height_img, $id_object, $vcsio['ip']);
				
				echo "<a href='video/".$id_object."/".$vcsio['ip']."/full/".$i.".jpg?n=".$randval."' class='highslide' onclick='return hs.expand(this)'><img src='video/".$id_object."/".$vcsio['ip']."/".$i.".jpg?n=".$randval."' style='width: 127px;' class='highslide'></a>";
				if (($i == '4') or ($i == '8') or ($i == '12')) {
					echo "<br>";
				}
			}
		} else { echo "<center><b style='color: red;'>!! RVIHDR [".$vcsio['ip']."] !!</b></center><br>"; }
	}
	elseif ($vcsio['model'] == 'RVI') {
		if ($output[0] == '1') {
			echo "<center><b>{$vcsio['model']} [{$vcsio['ip']}]</b></center><br>";
			for ($i = 1; $i <= $vcsio['channel']; $i++) {
				
				exec("avconv -rtsp_transport tcp -i 'rtsp://admin:".$vcsio['pass']."@".$vcsio['ip'].":554/cam/realmonitor?channel=".$i."&subtype=0' -f image2 -vframes 1 -pix_fmt yuvj420p video/".$id_object."/".$vcsio['ip']."/".$i.".jpg", $output);

				$img = "video/".$id_object."/".$vcsio['ip']."/".$i.".jpg";
				$imsize = getimagesize($img);
				
				$width_img = $imsize[0]; //ширина	
				$height_img = $imsize[1]; // высота
				
				resize($width, $height, $i, $width_img, $height_img, $id_object, $vcsio['ip']);
				
				echo "<a href='video/".$id_object."/".$vcsio['ip']."/full/".$i.".jpg?n=".$randval."' class='highslide' onclick='return hs.expand(this)'><img src='video/".$id_object."/".$vcsio['ip']."/".$i.".jpg?n=".$randval."' style='width: 127px;' class='highslide'></a>";
				if (($i == '4') or ($i == '8') or ($i == '12')) {
					echo "<br>";
				}
			}
		} else { echo "<center><b style='color: red;'>!! RVI [".$vcsio['ip']."] !!</b></center><br>"; }
	}
	elseif ($vcsio['model'] == 'GV') {
		echo "<center><b>GV [".$vcsio['ip']."]</b></center><br>";
		echo "<br>";
		echo '<center><b>С видеорегистратора GV невозможно снять и просмотреть камеры удаленно</b></center><br>';
		echo "<br>";
	}
	echo "<br><br>";
}