<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");

require_once("./function/function_scan.php");

function make_seed() {
	list($usec, $sec) = explode(' ', microtime());
	return (float) $sec + ((float) $usec * 100000);
}

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);	
	if(($userdata['id_position'] == "1") and ($userdata['access'] > 6)) {
		//////// ЕСЛИ ИДЕТ ПОИСК ПО ХЕШ ТЕГУ
		if (isset($_GET['find'])) {
			$find = $_GET['find'];
		} elseif (isset($_POST['findt'])) {
			$find = $_POST['hash_tags_find_post'];
		} elseif (isset($_POST['hash_tags_find_post'])) {
			$find = $_POST['hash_tags_find_post'];
		} else {
			$find = 'all';
		} 
		$per_page = 15; // количество записей, выводимых на странице
		
//////// НОМЕР ТЕКУЩЕЙ СТРАНИЦЫ 
		if (isset($_GET['page'])) {
			$page = $_GET['page']; 
		} else {
			$page = 1;
		}
		// вычисляем первый оператор для LIMIT
		$start = abs(($page-1) * $per_page);
		$scan_all_count = scan_all_count($dbcnx);
		
		if (isset($_POST['shop'])) {
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////     РЕДАКТИРОВАНИЕ СКАНОВ     ////////////////////////////////////////////////////////	
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			if (isset($_POST['submit_delete_x'])) {	
				mysql_query("DELETE FROM `scan_doc` WHERE `filename`='".$_POST['shop'][0]."' ", $dbcnx);
				mysql_query("DELETE FROM `scanned` WHERE `filename`='".$_POST['shop'][0]."' ", $dbcnx);
				header('Location: '.$_SERVER['REQUEST_URI']);
			} 
			if (isset($_POST['right_x'])) {
				$filename = 'scan/'.$_POST['shop'][0].'.jpg';
				$filename_resize = 'scan/resize/'.$_POST['shop'][0].'.jpg';
				$filename_preview = 'scan/preview/'.$_POST['shop'][0].'.jpg';
				$degrees = -90;
				// Content type
				header('Content-type: image/jpeg');
				// Load
				$source = imagecreatefromjpeg($filename);
				$source_resize = imagecreatefromjpeg($filename_resize);
				$source_preview = imagecreatefromjpeg($filename_preview);
				// Rotate
				$rotate = imagerotate($source, $degrees, 0);
				$rotate_resize = imagerotate($source_resize, $degrees, 0);
				$rotate_preview = imagerotate($source_preview, $degrees, 0);
				// Output
				imagejpeg($rotate, 'scan/'.$_POST['shop'][0].'.jpg');
				imagejpeg($rotate_resize, 'scan/resize/'.$_POST['shop'][0].'.jpg');
				imagejpeg($rotate_preview, 'scan/preview/'.$_POST['shop'][0].'.jpg');
				
				imagedestroy($source);
				imagedestroy($rotate);
				imagedestroy($source_resize);
				imagedestroy($rotate_resize);
				imagedestroy($source_preview);
				imagedestroy($rotate_preview);
				unset($_POST); 
				header('Location: '.$_SERVER['REQUEST_URI']);
			}
			if (isset($_POST['left_x'])) {
				$filename = 'scan/'.$_POST['shop'][0].'.jpg';
				$filename_resize = 'scan/resize/'.$_POST['shop'][0].'.jpg';
				$filename_preview = 'scan/preview/'.$_POST['shop'][0].'.jpg';
				$degrees = 90;
				// Content type
				header('Content-type: image/jpeg');
				// Load
				$source = imagecreatefromjpeg($filename);
				$source_resize = imagecreatefromjpeg($filename_resize);
				$source_preview = imagecreatefromjpeg($filename_preview);
				// Rotate
				$rotate = imagerotate($source, $degrees, 0);
				$rotate_resize = imagerotate($source_resize, $degrees, 0);
				$rotate_preview = imagerotate($source_preview, $degrees, 0);
				// Output
				imagejpeg($rotate, 'scan/'.$_POST['shop'][0].'.jpg');
				imagejpeg($rotate_resize, 'scan/resize/'.$_POST['shop'][0].'.jpg');
				imagejpeg($rotate_preview, 'scan/preview/'.$_POST['shop'][0].'.jpg');
				
				imagedestroy($source);
				imagedestroy($rotate);
				imagedestroy($source_resize);
				imagedestroy($rotate_resize);
				imagedestroy($source_preview);
				imagedestroy($rotate_preview); 
				//clearstatcache();
				//unset($_POST['left_x']);
				header('Location: '.$_SERVER['REQUEST_URI']);
			} else {
				for ($i=0;$i<count($_POST['shop']);$i+=5){											
				mysql_query("DELETE FROM `scanned` WHERE `filename`='".$_POST['shop'][$i]."'", $dbcnx);
				$filename = $_POST['shop'][$i];
				$whet = whet ($dbcnx, $filename);					
				$pieces = explode(" ", $_POST['shop'][$i+4]);
					for ($s=0;$s<count($pieces);$s++){						
						mysql_query("INSERT INTO `scanned`(`filename`, `hash`) VALUES ('".$_POST['shop'][$i]."','".$pieces[$s]."')", $dbcnx);
					}
					$w = $whet[0]['whet'] + 1;
					mysql_query("UPDATE `scan_doc` SET `whet`='".$w."',`date`='".$_POST['shop'][$i+2]."', `responsible`='".$_POST['shop'][$i+1]."', `id_object`='".$_POST['shop'][$i+3]."' WHERE `filename`='".$_POST['shop'][$i]."'", $dbcnx);
				}
			}
		}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
//////////////////////////////////////////     РЕДАКТИРОВАНИЕ СКАНОВ - КОНЕЦ     ////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		echo "<center>";	
		if (($find == 'all') or ($find == '')){
			$scan_all = scan_all($dbcnx, $start, $per_page);
		}	
		// ВЫВОД ЗАПИСЕЙ FIND
		elseif ((isset($find)) and ($find != 'all') and (!isset($_POST['findt']))) {
			$scan_all = scan_find_hash ($dbcnx, $find, $start, $per_page);
			$scan_count = scan_find_count ($dbcnx, $find);
			$total_rows = $scan_count[0]['count(filename)'];
		}
		// ЕСЛИ ПОИСК ИДЕТ ПО ХЕШТЕГУ
		elseif (isset($_POST['findt'])){
			
			$dateStart = $_POST['start'];
			$dateEnd = $_POST['end'];			
			$tags_find = $_POST['hash_tags_find_post'];			
			$tags_find_arr = explode("\r\n", $tags_find);
			
			for ($i=0;$i<count($tags_find_arr);$i++){
				if ($tags_find_arr[$i] != "") {
					$tagsarr[] = $tags_find_arr[$i];
				}
			}
			$where .= "scanned.filename = scan_doc.filename and scanned.hash LIKE '%".$tagsarr[0]."%' and scan_doc.date >= '".$dateStart."' and scan_doc.date <= '".$dateEnd."' ";
			
			for ($i=1;$i<count($tagsarr);$i++){
				if ($tagsarr[$i] != "") {
					$where .= " OR scanned.filename = scan_doc.filename and scanned.hash LIKE '%".$tagsarr[$i]."%' and scan_doc.date >= '".$dateStart."' and scan_doc.date <= '".$dateEnd."' ";
				}
			}
			$scan_find_hash_post = scan_find_hash_post ($dbcnx, $where);
			$scan_find_hash_post_count = scan_find_hash_post_count ($dbcnx, $where);
			$total_rows = $scan_find_hash_post_count[0]['COUNT(scanned.filename)']; // общее количество записей
			$q=0;
			for ($i=0;$i<count($scan_find_hash_post);$i++){
				
			//	echo $scan_find_hash_post[$i]['count(filename)']." - кол-во совпадений <br>";
			//	echo $count_tags_find_arr." - слов в запросе <br>";
				if ($scan_find_hash_post[$i]['count(scanned.filename)'] == count($tagsarr)) {
					$scan_all[]['filename'] = $scan_find_hash_post[$i]['filename'];
				//	echo "совпадение <br>";
					$q++;
				}
			}
			$total_rows = count($scan_all);
		} else {
			$scan_all = scan_all($dbcnx, $start, $per_page);
			$total_rows = $scan_all_count[0]['filename'];
		}	
		echo "<div class='title_term'> ДОКУМЕНТОВ В БАЗЕ [ <a href='/scan.php'>".$scan_all_count[0]['filename']."</a> ] | ";
			$all = $scan_all_count[0]['filename'];
			if ((isset($_GET['find'])) and ($find != 'all') and ($find != '')) {
				echo "ПОКАЗАНО [ ".$scan_count[0]['count(filename)']." ] | "; 
				$all = $scan_count[0]['count(filename)'];
			}
			elseif (((isset($q)) and ($q > 0)) or (isset($_POST['findt']))) {
				echo "ПОКАЗАНО [ ".$q." ] | "; 
				$all = $q;
			}
		echo "<a href='scan_doc.php' title='Добавить документ'>ДОБАВИТЬ</a></div>";	
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
////////////////////  НУМЕРАЦИЯ СТРАНИЦ ВВЕРХУ - НАЧАЛО   ///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$prev = 3;
		$current_page = $page - $prev;
		if ($current_page < 1) $current_page = 1;	
		$last_page = $page + $prev;	
		if ($last_page > ceil($all/$per_page)) $last_page = ceil($all/$per_page);
		
		$y = 1;
		if ($current_page > 1) {
			if (isset($_GET['find'])) {
				echo '<a href='.$_SERVER['PHP_SELF'].'?find='.$find.'&page='.$y.'>1</a> ';
			}
			elseif (isset($_POST["hash_tags_find_post"])){
				echo '<a href='.$_SERVER['PHP_SELF'].'?find='.$_POST["hash_tags_find_post"].'&page='.$y.'>1</a> ';
			}
			else {
				echo '<a href='.$_SERVER['PHP_SELF'].'?page='.$y.'>1</a> ';
			}	
		}
		$y = $current_page - 1;
		if ($current_page > 9) {
			if (isset($_GET['find'])) {
				echo '<a href='.$_SERVER['PHP_SELF'].'?find='.$find.'&page='.$y.'>...</a> ';
			}
			elseif (isset($_POST["hash_tags_find_post"])){
				echo '<a href='.$_SERVER['PHP_SELF'].'?find='.$_POST["hash_tags_find_post"].'&page='.$y.'>...</a> ';
			}
			else {
				echo '<a href='.$_SERVER['PHP_SELF'].'?page='.$y.'>...</a> ';
			}
		} else {
			for($i = 2;$i <$current_page;$i++){
				if (isset($_GET['find'])) {
					echo "<a href=".$_SERVER['PHP_SELF']."?find=".$find."&page=".$i.">".$i."</a> ";
				}
				elseif (isset($_POST["hash_tags_find_post"])){
					echo "<a href=".$_SERVER['PHP_SELF']."?find=".$_POST["hash_tags_find_post"]."&page=".$i.">".$i."</a> ";
				}
				else {
					echo "<a href=".$_SERVER['PHP_SELF']."?page=".$i.">".$i."</a> ";
				}
			}
		}	
		for($b = $current_page;$b < $last_page+1;$b++){
				if($b == $page) {
					echo "<b style='color:black;'>[".$b."]</b> ";
				} 
				else {
					if((isset($_POST['hash_tags_find_post'])) and (!isset($_GET['find']))){
						
						$tags_find = $_POST['hash_tags_find_post'];
						$tags_find_arr = str_replace("\r\n", "%20", $tags_find);
						
						$alink = "<a href=".$_SERVER['PHP_SELF']."?find=".$tags_find_arr;
						if($b != 1) $alink .= "&page={$b}";
						$alink .= ">".$b."</a> ";
						echo $alink;
					}
					elseif (isset($_GET['find'])) {
						$alink = "<a href=".$_SERVER['PHP_SELF']."?find=".$find;
						if($b != 1) $alink .= "&page={$b}";
						$alink .= ">".$b."</a> ";
						echo $alink;
					}
					else {
						$alink = "<a href=".$_SERVER['PHP_SELF'];
						if($b != 1) $alink .= "?page={$b}";
						$alink .= ">".$b."</a> ";
						echo $alink;
					}
					
				}
		}
		$y = $last_page + 1;
		if ($last_page < ceil($all/$per_page) && ceil($all/$per_page) - $last_page > 2) {
			if (isset($_GET['find'])) {
				echo "<a href=".$_SERVER['PHP_SELF']."?find=".$find."&page=".$y.">...</a> ";
			}
			elseif (isset($_POST["hash_tags_find_post"])){
				echo "<a href=".$_SERVER['PHP_SELF']."?find=".$_POST["hash_tags_find_post"]."&page=".$y.">...</a> ";
			}
			else {
				echo "<a href=".$_SERVER['PHP_SELF']."?page=".$y.">...</a> ";
			}
		}
		$e = ceil($all/$per_page);
		if ($last_page < ceil($all/$per_page)) {
			if (isset($_GET['find'])) {
				echo "<a href=".$_SERVER['PHP_SELF']."?find=".$find."&page=".$e.">".$e."</a> ";
			}
			elseif (isset($_POST["hash_tags_find_post"])){
				echo "<a href=".$_SERVER['PHP_SELF']."?find=".$_POST["hash_tags_find_post"]."&page=".$e.">".$e."</a> ";
			}
			else {
				echo "<a href=".$_SERVER['PHP_SELF']."?page=".$e.">".$e."</a> ";
			}
		}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
////////////////////  НУМЕРАЦИЯ СТРАНИЦ ВВЕРХУ - КОНЕЦ   ////////////////////////////////////////////////////////////////////////			
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
////////////////////  ВЫВОД СКАНОВ С РЕДАКТИРОВАНИЕМ - НАЧАЛО   /////////////////////////////////////////////////////////////////	
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		echo "<table BORDER CELLPADDING=10 CELLSPACING=0 bordercolor='#666' bgcolor='#C5C5C5'><tr>";
		$y = 1;
		echo "<tr>";
		for ($i=0;$i<count($scan_all);$i++){
			
			$filename = $scan_all[$i]['filename'];
			$scan_hash = scan_hash ($dbcnx, $filename);	
			$scan_hash_date = scan_hash_date ($dbcnx, $filename);	
			$img = '/home/web_www/public_html/scan/preview/'.$filename.'.jpg';
			$imsize = getimagesize($img);
			$width_img = $imsize[0]; // ширина	
			$height_img = $imsize[1]; // высота
			if ($width_img > $height_img) {
				$top = 1;
				$left = 47;
			}
			else {
				$top = -14;
				$left = 41;
			}
			$ScanHashDataStr = str_replace(',', ' ', $scan_hash[0]['hash']);
			//$DateDoc = date('d.m.Y', $scan_hash[0]['date']);
			//$DateDoc = $scan_hash[0]['date'];
			if (isset($scan_hash_date[0]['date'])) {
				$date_tags = explode("-",$scan_hash_date[0]['date']);	
			}
			else { $scan_hash_date[0]['date'] == '0000-00-00';  }
			srand(make_seed());
			$randval = rand();	
////////////// НАЧАЛО ФОРМЫ
			echo "<form method='POST' action='".$_SERVER['REQUEST_URI']."'>";
			
			echo "<tr><td style='text-align:center;width:125px;height:125px;color:#2B587A;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 11px;'  bordercolor='#666'><a href='/scan/resize/".$filename.".jpg?n=".$randval."' class='highslide' onclick='return hs.expand(this)'><img src='/scan/preview/".$filename.".jpg?n=".$randval."' title='".$ScanHashDataStr."' style='max-height: 100px; max-width: 100px;'></a>";
////////////// ИМЯ ФАЙЛА В СКРЫТОМ ПОЛЕ			
			echo "<input name='shop[]' type='hidden' size='2' value='".$filename."'>";
////////////// СКРЫТОЕ ПОЛЕ hash_tags_find_post ЕСЛИ ОНО НЕ ПУСТО 		
			if (isset($_POST['hash_tags_find_post']) and ($_POST['hash_tags_find_post']) != '') {
				echo "<input name='hash_tags_find_post' type='hidden' value='".$_POST['hash_tags_find_post']."'>";
				echo "<input name='findt' type='hidden' value='1'>";
			}
			elseif (isset($_GET['find'])){
				echo "<input name='hash_tags_find_post' type='hidden' value='".$_GET['find']."'>";
				echo "<input name='findt' type='hidden' value='1'>";
			}
			else {
			}
			
			//echo "<input name='shop[]' type='hidden' size='2' value='".$scan_hash[0]['date']."'>";
			
			$admin_user_id = admin_user_id ($dbcnx);
		
			echo "</td><td><div class='datetime' style='position: relative;top: -3px;left: 0px;'>";
			
			echo "<select size='1' name='shop[]' style='width: 157px;height: 24px;'>";
			echo "<option value='0'></option>";
				for ($o=0;$o<count($admin_user_id);$o++) {
					if ($scan_hash_date[0]['responsible'] == $admin_user_id[$o]["user_id"]) {
						echo "<option value='".$admin_user_id[$o]["user_id"]."' selected>".$admin_user_id[$o]["user_name"]."</option>";
					}
					else {
						echo "<option value='".$admin_user_id[$o]["user_id"]."'>".$admin_user_id[$o]["user_name"]."</option>";
					}
				}
			echo "</select>";	
			
/////////////// ВВОД ДАТЫ ОТСКАНИРОВАННОГО ДОКУМЕНТА	
			echo "<input id='meeting' name='shop[]' type='date' value='".$scan_hash_date[0]['date']."'/><br>"; 
			
////////////// ВЫПОДАЮЩИЙ СПИСОК МАГАЗИНОВ	
			$shopaa = scan_sklad ($dbcnx);
			
			echo "<select size='1' name='shop[]' style='width: 157px;height: 24px;'>";
			echo "<option value='0'></option>";
				foreach ($shopaa as $shop) {
					if ($scan_hash_date[0]['id_object'] == $shop["id"]) {
						echo "<option value='".$shop["id"]."' selected>".$shop["sklad"]."</option>";
					}
					else {
						echo "<option value='".$shop["id"]."'>".$shop["sklad"]."</option>";
					}
				}
			echo "</select>";
			echo "</div>";
			
			echo "<TEXTAREA name='shop[]' WRAP='virtual' COLS='40' ROWS='4' placeholder='поиск по хештегам, через ENTER' style='resize: none;'>".$ScanHashDataStr."</TEXTAREA>";
			$q=$i+1;
			echo "<input type='image' name='left' width='15px' src='img/left.png' style='position: relative;top: 8px;left: -455px;display:block-inline'>";
			echo "<input type='image' name='right' width='15px' src='img/right.png' style='position: relative;top: 8px;left: -345px;display:block-inline'>";
			echo "</td><td style='width:20px;text-align:center;'><b style='color: grey;'>".$scan_hash_date[0]['whet']."</b>
			<a onclick=\" newWindow = window.open('http://www2.neo63.ru/scan/".$filename.".jpg'); newWindow.focus(); newWindow.print();\"><img src='/img/print.png' style='width:15px;cursor:pointer;' title='Печать'></a>
			
			<input type='image' name='submit' width='12px' src='/img/save.png' style='width:15px;cursor:pointer;position: relative;top: 6px;'>
			<input type='image' name='submit_delete' width='12px' src='/img/delete.png' style='width:15px;cursor:pointer;position: relative;top: 12px;'>		
			</td></tr></form>";
			
			$y++;
		}
		echo "</tr>";
		echo "</tr></table>";
		echo "<br><br>";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
////////////////////  ВЫВОД СКАНОВ С РЕДАКТИРОВАНИЕМ - КОНЕЦ   //////////////////////////////////////////////////////////////////	
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////// НУМЕРАЦИЯ СТРАНИЦ НИЖНЯЯ - НАЧАЛО   ////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 

		$prev = 3;
		$current_page = $page - $prev;
		if ($current_page < 1) $current_page = 1;	
		$last_page = $page + $prev;	
		if ($last_page > ceil($all/$per_page)) $last_page = ceil($all/$per_page);	
		$y = 1;
		if ($current_page > 1) {
			if (isset($_GET['find'])) {
				echo '<a href='.$_SERVER['PHP_SELF'].'?find='.$find.'&page='.$y.'>1</a> ';
			}
			elseif (isset($_POST["hash_tags_find_post"])){
				echo '<a href='.$_SERVER['PHP_SELF'].'?find='.$_POST["hash_tags_find_post"].'&page='.$y.'>1</a> ';
			}
			else {
				echo '<a href='.$_SERVER['PHP_SELF'].'?page='.$y.'>1</a> ';
			}	
		}
		$y = $current_page - 1;
		if ($current_page > 9) {
			if (isset($_GET['find'])) {
				echo '<a href='.$_SERVER['PHP_SELF'].'?find='.$find.'&page='.$y.'>...</a> ';
			}
			elseif (isset($_POST["hash_tags_find_post"])){
				echo '<a href='.$_SERVER['PHP_SELF'].'?find='.$_POST["hash_tags_find_post"].'&page='.$y.'>...</a> ';
			}
			else {
				echo '<a href='.$_SERVER['PHP_SELF'].'?page='.$y.'>...</a> ';
			}
		} else {
			for($i = 2;$i <$current_page;$i++){
				if (isset($_GET['find'])) {
					echo "<a href=".$_SERVER['PHP_SELF']."?find=".$find."&page=".$i.">".$i."</a> ";
				}
				elseif (isset($_POST["hash_tags_find_post"])){
					echo "<a href=".$_SERVER['PHP_SELF']."?find=".$_POST["hash_tags_find_post"]."&page=".$i.">".$i."</a> ";
				}
				else {
					echo "<a href=".$_SERVER['PHP_SELF']."?page=".$i.">".$i."</a> ";
				}
			}
		}	
		for ($b = $current_page;$b < $last_page+1;$b++){
				if($b == $page) {
					echo "<b style='color:black;'>[".$b."]</b> ";
				} else {
					if((isset($_POST['hash_tags_find_post'])) and (!isset($_GET['find']))){
						
						$tags_find = $_POST['hash_tags_find_post'];
						$tags_find_arr = str_replace("\r\n", "%20", $tags_find);
						
						$alink = "<a href=".$_SERVER['PHP_SELF']."?find=".$tags_find_arr;
						if($b != 1) $alink .= "&page={$b}";
						$alink .= ">".$b."</a> ";
						echo $alink;
					}
					elseif (isset($_GET['find'])) {
						$alink = "<a href=".$_SERVER['PHP_SELF']."?find=".$find;
						if($b != 1) $alink .= "&page={$b}";
						$alink .= ">".$b."</a> ";
						echo $alink;
					}
					else {
						$alink = "<a href=".$_SERVER['PHP_SELF'];
						if($b != 1) $alink .= "?page={$b}";
						$alink .= ">".$b."</a> ";
						echo $alink;
					}
				}
		}
		$y = $last_page + 1;
		if ($last_page < ceil($all/$per_page) && ceil($all/$per_page) - $last_page > 2) {
			if (isset($_GET['find'])) {
				echo "<a href=".$_SERVER['PHP_SELF']."?find=".$find."&page=".$y.">...</a> ";
			}
			elseif (isset($_POST["hash_tags_find_post"])){
				echo "<a href=".$_SERVER['PHP_SELF']."?find=".$_POST["hash_tags_find_post"]."&page=".$y.">...</a> ";
			}
			else {
				echo "<a href=".$_SERVER['PHP_SELF']."?page=".$y.">...</a> ";
			}
		}
		$e = ceil($all/$per_page);
		if ($last_page < ceil($all/$per_page)) {
			if (isset($_GET['find'])) {
				echo "<a href=".$_SERVER['PHP_SELF']."?find=".$find."&page=".$e.">".$e."</a> ";
			}
			elseif (isset($_POST["hash_tags_find_post"])){
				echo "<a href=".$_SERVER['PHP_SELF']."?find=".$_POST["hash_tags_find_post"]."&page=".$e.">".$e."</a> ";
			}
			else {
				echo "<a href=".$_SERVER['PHP_SELF']."?page=".$e.">".$e."</a> ";
			}
		}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
////////////////////  НУМЕРАЦИЯ СТРАНИЦ НИЖНЯЯ - КОНЕЦ   ////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////// КОПИЯ ///////////////////////////	
	/*	for($t=1;$t<=$num_pages;$t++) {
			if ($t-1 == $page) {
				echo $t." ";
			} else {
				if (isset($_GET['find'])) {
					echo '<a href="'.$_SERVER['PHP_SELF'].'?find='.$find.'&page='.$t.'">'.$t."</a> ";
				}
				else {
					echo '<a href="'.$_SERVER['PHP_SELF'].'?page='.$t.'">'.$t."</a> ";
				}
			}
		}
	*/
///////////////////// КОПИЯ ///////////////////////////	
	
		echo "<br><br><br>";
		echo "</center>";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////// ТОП 10 САМЫХ РАСПРОСТРАННЕЫХ ХЕШТЕГОВ ///////////////////////////////////////////////////////////////////////////////
		echo "<div class='hash'>";														                          																										
		$top10_hash = top10_hash($dbcnx);																			   	
		for ($i=0;$i<count($top10_hash);$i++){ 
			echo "<a href='/scan.php?find=".$top10_hash[$i]['LOWER( hash )']."'>#".$top10_hash[$i]['LOWER( hash )']."</a><br>";
		}																												
		echo "</div>";																									
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////// ПОИСК ПО ХЕШ ТЕГАМ //////////////////////////////////////////////////////////////////////////////////////////////////
		echo "<div class='find_hash'>";
		
		$last_date_scan = last_date_scan ($dbcnx);
		
		echo "<form name='scan' method='POST' action='scan.php'>";
		
		if (isset($_POST['start'])) {
			$dateStart = $_POST['start'];
		} else { $dateStart = date("Y-m-d", strtotime("-1 year")); }
		if (isset($_POST['end'])) {
			$dateEnd = $_POST['end'];
		} else { $dateEnd = date("Y-m-d"); }	
		
		echo "<input id='meeting' name='start' type='date' value='".$dateStart."' min='".$last_date_scan[0]['date']."'/><br>"; 
		echo "<input id='meeting' name='end' type='date' value='".$dateEnd."'/><br>"; 
		echo "<br>";
		echo "<TEXTAREA name='hash_tags_find_post' WRAP='virtual' COLS='17' ROWS='14' placeholder='поиск по хештегам, через ENTER 
		Пример: 
		14_2
		мышь'>";
			if (isset($_POST['hash_tags_find_post'])) {
				if ((isset($tags_find_arr)) and ($tags_find_arr != '')) {
					echo implode("\r\n", $tags_find_arr);
				}
			}
		echo "</TEXTAREA>";
		echo "<p><input type='submit' name='findt' value='ПОИСК'></p>";
		echo "<b id='showScroll'></b></form>";
		echo "</div>";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	}
}

/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");
/* FOOTER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");


