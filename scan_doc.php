<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

require_once("./function/function_scan.php");

function resize_scan ($width, $height, $filename, $width_img, $height_img) {
	$im = imagecreatetruecolor($width, $height);
	$im1 = imagecreatefromjpeg('/var/www/html/www/scan/'.$filename.'.jpg');
	
	imagealphablending($im, true);
	imagealphablending($im, 1);
	imagealphablending($im1, 1);
	
	//imagecopy($im, $im1, 0, 0, 0, 0, $width, $height);
	imagecopyresampled($im, $im1, 0, 0, 0, 0, $width, $height, $width_img, $height_img);	

	imagejpeg($im, '/var/www/html/www/scan/resize/'.$filename.'.jpg');
	imagedestroy($im); 
}
function resize_min_scan($width_min, $height_min, $filename, $width_img, $height_img) {
	//echo "resize_min <br>";

	$im = imagecreatetruecolor($width_min, $height_min);	
	$im1 = imagecreatefromjpeg('/var/www/html/www/scan/'.$filename.'.jpg');
	
	imagealphablending($im, true);
	imagealphablending($im, 1);
	imagealphablending($im1, 1);
	
	//imagecopy($im, $im1, 0, 0, 0, 0, $width, $height);
	imagecopyresampled($im, $im1, 0, 0, 0, 0, $width_min, $height_min, $width_img, $height_img);	
	
	imagejpeg($im, '/var/www/html/www/scan/preview/'.$filename.'.jpg');
	imagedestroy($im); 
}

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);	
	if(($userdata['id_group'] == "1") and ($userdata['access'] > 7)) {
		if (isset($_POST['enter']))	{ //////////////////////////////////// ЕСЛИ $_POST['enter'] НЕ ПУСТОЙ
			$d = 0;
			echo "Файлов к загрузке: ".count($_FILES["filename"]["name"])."<br><br>";
			echo "<br><center><a href='/scan_doc.php'>вернуться к загрузке</a></center><br>";
			
			for ($i=0;$i<(count($_FILES["filename"]["name"]));$i++){ 
				 
				if($_FILES["filename"]["size"][$i] > 1024*5*1024) {
					echo "<br><br><br><br><center>";
					//var_dump($_FILES["filename"]["size"]);
					//var_dump($_FILES["filename"]["name"]);
					echo ("Размер файла превышает пять мегабайт");
					echo "</center>";
					//exit;
				}
				else {
					$filename_up = $_FILES["filename"]["name"][$i];
					//echo $filename_up." - имя файла<br>";
					$scan_publicat = scan_publicat($dbcnx, $filename_up);
					//echo var_dump($scan_publicat)." - scan publicat<br>";
					if (isset($scan_publicat[0]['id'])) {
						echo "<br><center>";
						echo ("Документ с именем \"<b style='color: red;'>".$filename_up."</b>\" уже был загружен");
						echo "<br>";
						echo "<a href='/scan_doc.php'>вернуться к загрузке</a>";
						echo "</center>";
						//exit;
					}
					else {
					// Проверяем загружен ли файл
						if(is_uploaded_file($_FILES["filename"]["tmp_name"][$i]))
						{								
							echo "<br>";							
							$filename = md5(microtime());			
							// Если файл загружен успешно, перемещаем его
							// из временной директории в конечную
							move_uploaded_file($_FILES["filename"]["tmp_name"][$i], "/var/www/html/www/scan/".$filename.".jpg");
							$img = '/var/www/html/www/scan/'.$filename.'.jpg';
							$imsize = getimagesize($img);
							echo "<center>";
							//echo "Файл загружен в папку home/web_www/public_html/scan/ <br>";
							$width_img = $imsize[0]; //ширина	
							$height_img = $imsize[1]; // высота
							$max = 1200; // чем меньше значение, тем меньше картинка
							$min = 100; // привьюшка
							if (($width_img > $height_img) and ($width_img > $max)) { // изображение альбомное
								//echo "изображение альбомное<br>";
								$z = $width_img/$max;
								$width = $width_img/$z;
								$height = $height_img/$z;
								resize_scan ($width, $height, $filename, $width_img, $height_img);
								
								$z = $width_img/$min;
								$width_min = $width_img/$z;
								$height_min = $height_img/$z;						
								resize_min_scan ($width_min, $height_min, $filename, $width_img, $height_img);
								
								$insert = "INSERT INTO `scan_doc`(`filename`, `oldname`, `whet`, `responsible`, `date`) VALUES ('".$filename."','".$filename_up."','0','0','0000-00-00')";
								mysql_query($insert,$dbcnx);
								$d++;
							}
							elseif (($width_img < $height_img) and ($height_img > $max)) { // изображение листовое
								//echo "изображение листовое<br>";
								$z = $height_img/$max;
								$width = $width_img/$z;
								$height = $height_img/$z;
								resize_scan($width, $height, $filename, $width_img, $height_img);
								
								$z = $height_img/$min;
								$width_min = $width_img/$z;
								$height_min = $height_img/$z;						
								resize_min_scan($width_min, $height_min, $filename, $width_img, $height_img);
								
								$insert = "INSERT INTO `scan_doc`(`filename`, `oldname`, `whet`, `responsible`, `date`) VALUES ('".$filename."','".$filename_up."','0','0','0000-00-00')";
								mysql_query($insert,$dbcnx);
								$d++;
							}
							elseif ($height_img > $max) { // изображение квадратное
								//echo "изображение квадратное<br>";
								$z = $height_img/$max;
								$width = $width_img/$z;
								$height = $height_img/$z;
								resize_scan($width, $height, $filename, $width_img, $height_img);
								
								$z = $height_img/$min;
								$width_min = $width_img/$z;
								$height_min = $height_img/$z;						
								resize_min_scan($width_min, $height_min, $filename, $width_img, $height_img);
								
								$insert = "INSERT INTO `scan_doc`(`filename`, `oldname`, `whet`, `responsible`, `date`) VALUES ('".$filename."','".$filename_up."','0','0','0000-00-00')";
								mysql_query($insert, $dbcnx);
								$d++;
							}
							else {
								echo "изображение плохого качества<br>";
							}
							
						} else {
							echo "<center>";
							echo "Ошибка загрузки файла";
							echo "</center>";
						}
					}
				}			
			}
			echo "Добавлено документов: ".$d."<br><br>";
			header('Location: '.$_SERVER['PHP_SELF']);
			exit;
		} 	
		else {	//////////////////////////////////// ЕСЛИ ФАЙЛ СКАНА НЕ ЗАГРУЖЕН
			echo "<br><br><br><br><br><center>";
			echo "<a href='/scan.php'><img width='15px' src='img/back_arrow.png' title='Вернуться к списку документов'></a> ПРИКРЕПЛЯЕМ ДОКУМЕНТ [не более 20 за раз]";
			echo "<br><br><br>";
			echo "<form name='curl' method='POST' action='scan_doc.php' enctype='multipart/form-data'>";
			echo "<input type='file' name='filename[]' multiple style='width: 250px;'/> <input type='submit' name='enter' value='сохранить'>";
			echo "</form>";
			echo "</center>";
		}
	}
}

echo "<br><br><br>";
/* FOOTER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");