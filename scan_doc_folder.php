<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
require_once("./function/function_scan.php");

function resize_scan_folder ($width, $height, $filename, $file_body_new, $width_img, $height_img) {
	$info = new SplFileInfo($filename);
	$exp = $info->getExtension();	
	$im = imagecreatetruecolor($width, $height);	
	if ($exp == 'png') {
		$im1 = imagecreatefromstring(file_get_contents('/var/www/html/www/scan_doc/'.$filename));
	}
	else {
		$im1 = imagecreatefromjpeg('/var/www/html/www/scan_doc/'.$filename);
	}		
	imagealphablending($im, true);
	imagealphablending($im, 1);
	imagealphablending($im1, 1);
	imagecopyresampled($im, $im1, 0, 0, 0, 0, $width, $height, $width_img, $height_img);	
	imagejpeg($im, '/var/www/html/www/scan/resize/'.$file_body_new.'.jpg');
	imagedestroy($im);
}

function resize_min_scan_folder ($width_min, $height_min, $filename, $file_body_new, $width_img, $height_img) {
	$info = new SplFileInfo($filename);
	$exp = $info->getExtension();	
	$im = imagecreatetruecolor($width_min, $height_min);	
	if ($exp == 'png') {
		$im1 = imagecreatefromstring(file_get_contents('/var/www/html/www/scan_doc/'.$filename));
	}
	else {
		$im1 = imagecreatefromjpeg('/var/www/html/www/scan_doc/'.$filename);
	}		
	imagealphablending($im, true);
	imagealphablending($im, 1);
	imagealphablending($im1, 1);
	imagecopyresampled($im, $im1, 0, 0, 0, 0, $width_min, $height_min, $width_img, $height_img);		
	imagejpeg($im, '/var/www/html/www/scan/preview/'.$file_body_new.'.jpg');
	imagedestroy($im);
}

$directory = 'scan_doc';
$files = array_diff(scandir($directory), array('..', '.'));
echo "Документов  в папке: ".count($files);

$o=0; $w=0;
for ($i=2;$i<count($files)+2;$i++) {
	
	$o++; $filename = $files[$i];			
	$scan_publicat = scan_publicat ($dbcnx, $files[$i]);				
	if (isset($scan_publicat[0]['id'])) {
		echo "<br><center>";
		echo ("Документ с именем \"<b style='color: red;'>".$files[$i]."</b>\" уже был загружен");
		echo "<br><br>";
		echo "</center>";
	} else {
		$w++;

		$file_body_new = md5(microtime());
		copy ("scan_doc/".$filename, "scan/".$file_body_new.".jpg");
		
		$imsize = getimagesize('scan_doc/'.$filename);

		$width_img = $imsize[0]."<br>"; // ширина	
		$height_img = $imsize[1]."<br>"; // высота
		
		$max = 1000; // чем меньше значение, тем меньше картинка
		$min = 100; // привьюшка
		
		// width_img ширина 728
		// height_img высота 378
		
		echo "--------------------------------------<br>";
		
		if ($width_img > $height_img) {
			if ($width_img > 1000) { // изображение альбомное
				echo "изображение альбомное <br>";
				$z = $width_img/$max;
				$width = $width_img/$z;
				$height = $height_img/$z;						
				resize_scan_folder ($width, $height, $filename, $file_body_new, $width_img, $height_img);
								
				$z = $width_img/$min;
				$width_min = $width_img/$z;
				$height_min = $height_img/$z;						
				resize_min_scan_folder ($width_min, $height_min, $filename, $file_body_new, $width_img, $height_img);
			}
			else {
				echo "изображение альбомное, но меньше 1000 <br>";
				//$z = $width_img/$max;
				$width = 1000;
				$z = 1000 / $width_img * $height_img;
				$height =  round($z);	
				resize_scan_folder ($width, $height, $filename, $file_body_new, $width_img, $height_img);
								
				$z = $width_img/$min;
				$width_min = $width_img/$z;
				$height_min = $height_img/$z;						
				resize_min_scan_folder ($width_min, $height_min, $filename, $file_body_new, $width_img, $height_img);
			}				
		}
		elseif ($width_img < $height_img) { // изображение листовое
			if ($height_img > 1000) {
				echo "изображение листовое <br>";
				$z = $height_img/$max;
				$width = $width_img/$z;
				$height = $height_img/$z;
				resize_scan_folder ($width, $height, $filename, $file_body_new, $width_img, $height_img);
								
				$z = $height_img/$min;
				$width_min = $width_img/$z;
				$height_min = $height_img/$z;						
				resize_min_scan_folder ($width_min, $height_min, $filename, $file_body_new, $width_img, $height_img);
			}	
			else {
				echo "изображение листовое, но меньше 1000<br>";
				//$z = $width_img/$max;
				$height = 1000;
				$z = 1000 / $height_img * $width_img;
				$width =  round($z);	
				resize_scan_folder ($width, $height, $filename, $file_body_new, $width_img, $height_img);
					
				$z = $height_img/$min;
				$width_min = $width_img/$z;
				$height_min = $height_img/$z;						
				resize_min_scan_folder ($width_min, $height_min, $filename, $file_body_new, $width_img, $height_img);
			}
		}
		else { // изображение квадратное
			echo "изображение квадратное <br>";
			$z = $height_img/$max;
			$width = $width_img/$z;
			$height = $height_img/$z;
			resize_scan_folder ($width, $height, $filename, $file_body_new, $width_img, $height_img);
						
			$z = $height_img/$min;
			$width_min = $width_img/$z;
			$height_min = $height_img/$z;						
			resize_min_scan_folder ($width_min, $height_min, $filename, $file_body_new, $width_img, $height_img);
		}
	
		mysql_query("INSERT INTO `scan_doc` (`filename`, `oldname`, `whet`) VALUES ('".$file_body_new."', '".$filename."', 0)", $dbcnx);
		
		echo "<b color='red'>".$o."</b><br>";
		echo $files[$i]."<br><br>";
		echo $width_img." - ширина <br>";
		echo $height_img." - высота <br>";
		echo $width." - ширина resize<br>";
		echo $height." - высота resize<br>";
		echo $width_min." - ширина resize_min<br>";
		echo $height_min." - высота resize_min<br>";
		echo $filename." - исходное имя файла<br>";
		echo $file_body_new." - новое имя файла<br>";
		echo $date." - хештег даты<br>";
	}	
}	

echo "<br>";
echo "<br>";
echo "Документов проверено ".$o."<br>";
echo "Документов добавлено ".$w."<br>";
echo "<br>";
echo "<br>";
echo "<br>";

?>