<?php
/* HEADER */ include_once ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include_once ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

require_once("./function/function_scan.php");

echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">';

//var_dump($_POST);

function make_seed() {
	list($usec, $sec) = explode(' ', microtime());
	return (float) $sec + ((float) $usec * 100000);
}

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);	
	if(($userdata['id_group'] == "1") and ($userdata['access'] > 6)) {

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////     РЕДАКТИРОВАНИЕ СКАНОВ     ////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

		if (isset($_POST['submit_delete_x'])) {	 // УДАЛЕНИЕ КАРТИНКИ
			mysql_query("DELETE `scan_doc`, `scanned` FROM `scan_doc`, `scanned` WHERE `scan_doc`.`filename` = `scanned`.`filename` AND `scan_doc`.`filename` = '".$_POST['filename']."' ", $dbcnx);
		}
/*
		if (isset($_POST['save'])) {										
			mysql_query("DELETE FROM `scanned` WHERE `filename`='".$_POST['filename']."'", $dbcnx);
			$whet = whet ($dbcnx, $_POST['filename']);					
			$pieces = explode(" ", $_POST['hash']);
			
			$update_log = "SCAN: изменение описание скана ".$_POST['filename']." ";
			mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$_COOKIE['id']."', '".$date_time."', 'scan', '".$_POST['sklad']."', '".$update_log."')", $dbcnx);
			
			for ($s=0; $s<count($pieces); $s++) {						
				mysql_query("INSERT INTO `scanned`(`filename`, `hash`) VALUES ('".$_POST['filename']."','".$pieces[$s]."')", $dbcnx);
			}
			$w = $whet[0]['whet'] + 1;
			mysql_query("UPDATE `scan_doc` SET `whet`='".$w."',`date`='".$_POST['scan_date']."', `responsible`='".$_POST['admin']."', `id_object`='".$_POST['sklad']."' WHERE `filename`='".$_POST['filename']."'", $dbcnx);
		}
*/		
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////     РЕДАКТИРОВАНИЕ СКАНОВ - КОНЕЦ     ////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		//////// ЕСЛИ ИДЕТ СОРТИРОВКА ПО АДМИНАМ
		if ((isset($_POST['admin_find'])) and ($_POST['admin_find'] > 0)) {
			$admin = $_POST['admin_find'];
		}
		//////// ЕСЛИ ИДЕТ ПОИСК ПО ХЕШ ТЕГУ
		echo '<br>';
		if (isset($_POST['find'])) {
			$find = $_POST['find'];
			//echo "!! POST['find'] <br>";
		} elseif (!empty($_POST['findt'])) {
			$find = $_POST['hash_tags_find_post'];
			//echo "!! POST['hash_tags_find_post'] <br>";
		} elseif (!empty($_POST['hash_tags_find_post'])) {
			$find = $_POST['hash_tags_find_post'];
			//echo "!! POST['hash_tags_find_post'] <br>";
		} elseif (!empty($_POST['sklad_find']) or ($_POST['sklad_find'] === 0)) {
			$find = $_POST['sklad_find'];
			//echo $_POST['sklad_find']." !! POST['sklad_find'] <br>";
		} elseif (!empty($_POST['admin_find'])) {	
			$find = $_POST['admin_find'];
			//echo "!! POST['sklad_find'] <br>";
		} else {
			$find = 'all';
			//echo "all <br>";
		} 
		
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
//////// НОМЕР ТЕКУЩЕЙ СТРАНИЦЫ /////////////////////////////////////////////////////////////////////////////////////////////////

		if (isset($_POST['page'])) { 
			$page = $_POST['page']; 
		} else { 
			$page = 1;
		}
		
		//echo "<br>";
		//echo "Страница номер: ".$page." <br>";
		
		$per_page = 15; // количество записей, выводимых на странице
		$start = abs(($page-1) * $per_page); // вычисляем первый оператор для LIMIT
		
		//echo "Показаны документы с ".$start." по ".($start + 20)." <br>";
		
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// #############################################################################################################################	
	
		if ((isset($find)) and ($find != 'all') and (!isset($_POST['hash_tags_find_post']))) {
			//echo "1 <br>";
			//echo $find." - find <br>";
			$scan_all = scan_find_hash ($dbcnx, $find, $start, $per_page);
			$scan_count = scan_find_count ($dbcnx, $find);
			$total_rows = $scan_count[0]['count(filename)'];
		}
		// ЕСЛИ ПОИСК ИДЕТ ПО СКЛАДУ
		elseif (!empty($_POST['sklad_find'])  or ($_POST['sklad_find'] === 0)) {
			//echo $find." - id склада <br>";	
			//echo "2 <br>";			
			$scan_all = scan_find_object ($dbcnx, $_POST['sklad_find'], $start, $per_page);
			$scan_find_object_count = scan_find_object_count ($dbcnx, $_POST['sklad_find']);
			$total_rows = $scan_find_object_count;	
		}
		elseif (!empty($_POST['admin_find'])) {
			//echo "3 <br>";
			//echo $find." - id админа <br>";	
			$scan_all = scan_find_admin ($dbcnx, $_POST['admin_find'], $start, $per_page);
			$scan_find_admin_count = scan_find_admin_count ($dbcnx, $_POST['admin_find']);
			$total_rows = $scan_find_admin_count;	
		}
		// ЕСЛИ ПОИСК ИДЕТ ПО ХЕШТЕГУ
		elseif (!empty($_POST['hash_tags_find_post'])) {
			//echo "3 <br>";
			//echo $find." - хештег <br>";	
			$tags_find = $_POST['hash_tags_find_post'];			
			$tags_find_arr = explode(" ", $tags_find);
			for ($i=0; $i<count($tags_find_arr); $i++) {
				if ($tags_find_arr[$i] != "") {
					$tagsarr[] = $tags_find_arr[$i];
				}
			}
			$where = '';
			$where .= "scanned.filename = scan_doc.filename and scanned.hash LIKE '%".$tagsarr[0]."%' and scan_doc.date >= '".$_POST['start']."' and scan_doc.date <= '".$_POST['end']."' ";
			
			for ($i=1;$i<count($tagsarr);$i++) {
				if ($tagsarr[$i] != "") {
					$where .= " OR scanned.filename = scan_doc.filename and scanned.hash LIKE '%".$tagsarr[$i]."%' and scan_doc.date >= '".$_POST['start']."' and scan_doc.date <= '".$_POST['end']."' ";
				}
			}
			//echo $where."<br>";
			$scan_find_hash_post = scan_find_hash_post ($dbcnx, $where, $admin);
			$scan_find_hash_post_count = scan_find_hash_post_count ($dbcnx, $where);
			$total_rows = $scan_find_hash_post_count[0]['COUNT(scanned.filename)']; // общее количество записей
			$q=0;
			//echo count($scan_find_hash_post)." - кол-во совпадений <br>";
			for ($i=0;$i<count($scan_find_hash_post);$i++) {
				//	echo $scan_find_hash_post[$i]['count(filename)']." - кол-во совпадений <br>";
				//	echo $count_tags_find_arr." - слов в запросе <br>";
				if ($scan_find_hash_post[$i]['count(scanned.filename)'] == count($tagsarr)) {
					$scan_all[]['filename'] = $scan_find_hash_post[$i]['filename'];
					//echo $scan_find_hash_post[$i]['filename']."<br>";
					//	echo "совпадение <br>";
					$q++;
				}
			}
			$scan_all = array_slice($scan_all, $start, $per_page); 
		} 
		// ЕСЛИ В ПОИСКЕ УКАЗАНЫ ТОЛЬКО ДАТЫ НАЧАЛА И КОНЦА
		elseif (!empty($_POST['start']) and !empty($_POST['end'])) {
			//echo "4 <br>";

			$where = '';
			$where .= "scanned.filename = scan_doc.filename and scan_doc.date >= '".$_POST['start']."' and scan_doc.date <= '".$_POST['end']."' ";
		
			$scan_all = scan_find_data_post ($dbcnx, $where, $start, $per_page);
			$scan_find_data_post_count = scan_find_data_post_count ($dbcnx, $_POST['start'], $_POST['end']);
			$total_rows = $scan_find_data_post_count[0]['COUNT(*)'];
			
		} 
		// ВЫВОД ВСЕХ ВОЗМОЖНЫХ ЗАПИСЕЙ В БАЗЕ
		else { // ВЫВОД ВСЕХ ВОЗМОЖНЫХ ЗАПИСЕЙ В БАЗЕ
			//echo "5 <br>";
			$scan_all = scan_all ($dbcnx, $start, $per_page);
		}
		
		//echo "<br><br>";
		//echo count($scan_all)." - count(scan_all) <br>";
		//echo $total_rows." - total_rows <br>";
		
		// $numberOfDocumentsPerPage - ТЕКУЩАЯ СТРАНИЦА
		if ((count($scan_all)) > $per_page) { // ЕСЛИ КОЛИЧЕСТВО СКАНОВ БОЛЬШЕ $per_page ТО ПЕРЕМЕННАЯ $numberOfDocumentsPerPage = $per_page
			$numberOfDocumentsPerPage = $per_page;		
		} else {
			$numberOfDocumentsPerPage = count($scan_all);
		}
		
/// #############################################################################################################################
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////// РАСЧЕТ КОЛИЧЕСТВА СТРАНИЦ ////////////////////////////////////////////////////////////////////////////////////
	
		$scan_all_count = scan_all_count ($dbcnx); ?>
		<div class='scan_body'>
			<div class="container scan_grid">	
				<div class='title_scan item1'> ДОКУМЕНТОВ В БАЗЕ [ <a href='/scan.php'><?=$scan_all_count?></a> ] |
					<?php
						if ((isset($_POST)) and ($find != 'all') and ($find != '')) {
							echo "ПОКАЗАНО [ ".$total_rows." ] | "; 
							$all = $total_rows;
						}
						elseif ((isset($q)) and ($q > 0)) {
							echo "ПОКАЗАНО [ ".$q." ] | "; 
							$all = $q;
						}
						elseif ((!empty($_POST['start'])) and (!empty($_POST['end']))) {
							echo "ПОКАЗАНО [ ".$total_rows." ] | "; 
							$all = $total_rows;
						}
						else { $all = $scan_all_count; }

						$scanNotWhet = scanNotWhet ($dbcnx);
				?>
					<a href='scan_doc.php' title='Добавить документ'>ДОБАВИТЬ</a> | НОВЫХ [ <?=$scanNotWhet?> ]
				</div> 
				
				<?php
		
				echo "<div class='find_hash'>";
					$last_date_scan = last_date_scan ($dbcnx);	
					echo "<form name='scan' method='POST' action='scan.php'>";

					if (isset($_POST['start'])) {
						$dateStart = $_POST['start'];
					} else { $dateStart = date(""); }
					if (isset($_POST['end'])) {
						$dateEnd = $_POST['end'];
					} else { $dateEnd = date("Y-m-d"); }	

					echo "<input id='meeting' name='start' style='width: 157px;' type='date' value='".$dateStart."'/><br>"; 
					echo "<input id='meeting' name='end' style='width: 157px;' type='date' value='".$dateEnd."'/><br>"; 
					echo "Хеш теги: <br>";
					echo "<TEXTAREA name='hash_tags_find_post' WRAP='virtual' COLS='17' ROWS='14' style='width: 157px;' placeholder='поиск по хештегам, через пробел'>";
						if (isset($_POST['hash_tags_find_post'])) {
							echo implode("\r\n", (array)$_POST['hash_tags_find_post']);
						}
					echo "</TEXTAREA><br>";
					echo "Сист.администратор: <br>";
					$AllAdminOnline = AllAdminOnline ($dbcnx);		
					echo "<select size='1' name='admin_find' style='width: 157px;height: 24px;'>";
					echo "<option value=''></option>";
						for ($o=0; $o<count($AllAdminOnline); $o++) {
							if ((isset($_POST['admin_find'])) and ($_POST['admin_find'] == $AllAdminOnline[$o]["id"])) {
								echo "<option value='".$AllAdminOnline[$o]["id"]."' selected>".$AllAdminOnline[$o]["fio"]."</option>";
							} else {				
								echo "<option value='".$AllAdminOnline[$o]["id"]."'>".$AllAdminOnline[$o]["fio"]."</option>";
							}
						}
					echo "</select><br>";
					echo "Склад: <br>";
				////////////// ВЫПОДАЮЩИЙ СПИСОК МАГАЗИНОВ	
					$scan_sklad = scan_sklad ($dbcnx);			
					echo "<select size='1' name='sklad_find' style='width: 157px;height: 24px;'>";
					echo "<option></option>";
					echo "<option value='0'>Без склада</option>";
						foreach ($scan_sklad as $ss) {
							if ($_POST['sklad_find'] == $ss["id"]) {
								echo "<option value='".$ss["id"]."' selected>".$ss["sklad"]."</option>";
							} else {
								echo "<option value='".$ss["id"]."'>".$ss["sklad"]."</option>";
							}
						}
					echo "</select>";

					echo "<p><input type='submit' value='ПОИСК'></p>";
					echo "<b id='showScroll'></b></form>";
				echo "</div>";	

				echo "<div class='scan_block scan_flex'>";
				if (count($scan_all) > 0) { // ЕСЛИ ДОКУМЕНТОВ БОЛЬШЕ 0
				
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		////////////////////  ПАГИНАЦИЯ ВВЕРХУ - НАЧАЛО   ////////////////////////////////////////////////////////////////////////			
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
					// $current_page - значение текущей страницы в меню пагинации
				
					$prev = 3; // шаг страниц
					$current_page = $page - $prev; // от текущего номера страницы вычитаем значение шага страниц
					if ($current_page < 1) $current_page = 1;	// если значение ТЕКУЩАЯ СТРАНИЦА - $prev будет ниже 0, то $current_page = 1
					$last_page = $page + $prev;	 // выводимые страницы следующие ($last_page) за текущей ($current_page) согласно шагу страницы ($prev)
					if ($last_page > ceil($all/$per_page)) $last_page = ceil($all/$per_page); // если последние страницы не содержат документов, то высчитываем последнюю страницу в пагинации

					$y = 1;
					echo '<div class="number_page">';	
						if ($current_page > 1) {
							echo "<div class='number'>";
								echo "<form method='POST' action='scan.php'>";
									echo "<input name='page' type='hidden' value='1'>";
									if (isset($admin)) { echo "<input name='admin_find' type='hidden' value='".$admin."'>"; }
									if (isset($_POST['sklad_find'])) { echo "<input name='sklad_find' type='hidden' value='".$_POST['sklad_find']."'>"; }
									if (isset($_POST['start'])) { echo "<input name='start' type='hidden' value='".$_POST['start']."'>"; }
									if (isset($_POST['end'])) { echo "<input name='end' type='hidden' value='".$_POST['end']."'>"; }
									if (isset($_POST['find'])) {
										echo "<input name='find' type='hidden' value='".$_POST['find']."'>";
										echo "<input type='submit' value='1 ' id='top10_hash'>";
									} elseif (isset($_POST["hash_tags_find_post"])) {
										echo "<input name='hash_tags_find_post' type='hidden' value='".$_POST['hash_tags_find_post']."'>";
										echo "<input type='submit' value='1 ' id='top10_hash'>";
									} else {
										//echo "<input name='find' type='hidden' value='".$_POST['find']."'>";
										echo "<input type='submit' value='1 ' id='top10_hash'>";
									}
								echo "</form>";
							echo "</div>";
						}
						$y = $current_page - 1;
						if ($current_page > 9) {
							echo "<div class='number'>...</div>";
						} else {
							for($i = 2; $i <$current_page; $i++) {
								echo "<div class='number'>";
									echo "<form method='POST' action='scan.php'>";
									echo "<input name='page' type='hidden' value='".$i."'>";
									if (isset($admin)) { echo "<input name='admin_find' type='hidden' value='".$admin."'>"; }
									if (isset($_POST['sklad_find'])) { echo "<input name='sklad_find' type='hidden' value='".$_POST['sklad_find']."'>"; }
									if (isset($_POST['start'])) { echo "<input name='start' type='hidden' value='".$_POST['start']."'>"; }
									if (isset($_POST['end'])) { echo "<input name='end' type='hidden' value='".$_POST['end']."'>"; }
									if (isset($_POST['find'])) {
										echo "<input name='find' type='hidden' value='".$_POST['find']."'>";
										echo "<input type='submit' value='".$i."' id='top10_hash'>";
									} elseif (isset($_POST["hash_tags_find_post"])) {
										echo "<input name='hash_tags_find_post' type='hidden' value='".$_POST['hash_tags_find_post']."'>";
										echo "<input type='submit' value='".$i."' id='top10_hash'>";
									} else {
										echo "<input type='submit' value='".$i."' id='top10_hash'>";
									}
									echo "</form>";
								echo "</div>";
							}
						}
						
						for ($b = $current_page; $b <= $last_page; $b++) {
							if($b == $page) {
								echo "<div class='number black_number'>[".$b."]</div> ";
							} else {
								echo "<div class='number'>";
									echo "<form method='POST' action='scan.php'> ";
									echo "<input name='page' type='hidden' value='".$b."'>";
									if (isset($admin)) { echo "<input name='admin_find' type='hidden' value='".$admin."'>"; }
									if (isset($_POST['sklad_find'])) { echo "<input name='sklad_find' type='hidden' value='".$_POST['sklad_find']."'>"; }
									if (isset($_POST['start'])) { echo "<input name='start' type='hidden' value='".$_POST['start']."'>"; }
									if (isset($_POST['end'])) { echo "<input name='end' type='hidden' value='".$_POST['end']."'>"; }
									if (isset($_POST['find'])) {
										echo "<input name='find' type='hidden' value='".$_POST['find']."'>";
										echo "<input type='submit' value='".$b."' id='top10_hash'>";
									} elseif (isset($_POST["hash_tags_find_post"])){
										echo "<input name='hash_tags_find_post' type='hidden' value='".$_POST['hash_tags_find_post']."'>";
										echo "<input type='submit' value='".$b."' id='top10_hash'>";	
									} else {
										echo "<input type='submit' value='".$b."' id='top10_hash'>";
									}
									echo "</form>";
								echo "</div>";
							}
						}
						$y = $last_page + 1;
						if ($last_page < ceil($all/$per_page) && ceil($all/$per_page) - $last_page > 2) {
							echo "<div class='number'>...</div>";
						}
						$e = ceil($all/$per_page);
						if ($last_page < ceil($all/$per_page)) {
							echo "<div class='number'>";
								echo "<form method='POST' action='scan.php'> ";
								echo "<input name='page' type='hidden' value='".$e."'>";
								if (isset($admin)) { echo "<input name='admin_find' type='hidden' value='".$admin."'>"; }
								if (isset($_POST['sklad_find'])) { echo "<input name='sklad_find' type='hidden' value='".$_POST['sklad_find']."'>"; }
								if (isset($_POST['start'])) { echo "<input name='start' type='hidden' value='".$_POST['start']."'>"; }
								if (isset($_POST['end'])) { echo "<input name='end' type='hidden' value='".$_POST['end']."'>"; }
								if (isset($_POST['find'])) {
									echo "<input name='find' type='hidden' value='".$_POST['find']."'>";
									echo "<input type='submit' value='".$e."' id='top10_hash'>";
								} elseif (isset($_POST["hash_tags_find_post"])){
									echo "<input name='hash_tags_find_post' type='hidden' value='".$_POST['hash_tags_find_post']."'>";
									echo "<input type='submit' value='".$e."' id='top10_hash'>";
								} else {
									echo " <input type='submit' value='".$e."' id='top10_hash'> ";
								}
								echo "</form>";	
							echo "</div>";
						}
						echo "<div class='number'>";
							echo "<form method='POST' action='scan.php'>";
							echo "<select size='1' name='page' style='width: 50px;height: 24px;' onchange=\"this.form.submit()\">";
								for($i = 1; $i <= $e; $i++) {
									if ($i == $page) {
										echo "<option value='".$i."' selected>".$i."</option>";
									} else {
										echo "<option value='".$i."'>".$i."</option>";
									}
								}
							echo "</select>";
							echo "</form>";
						echo "</div>";
					echo '</div>';
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		////////////////////  НУМЕРАЦИЯ СТРАНИЦ ВВЕРХУ - КОНЕЦ   ////////////////////////////////////////////////////////////////////////			
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		////////////////////  ВЫВОД СКАНОВ С РЕДАКТИРОВАНИЕМ - НАЧАЛО   /////////////////////////////////////////////////////////////////	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				echo '<div class="scan_block">';
				$y = 1;
				for ($i=0; $i<$numberOfDocumentsPerPage; $i++) {

					$filename = $scan_all[$i]['filename'];
					$scan_hash = scan_hash ($dbcnx, $filename);	
					$scan_hash_date = scan_hash_date ($dbcnx, $filename);	
					$img = '/var/www/html/www/scan/preview/'.$filename.'.jpg';
					$imsize = getimagesize($img);
					$width_img = $imsize[0]; // ширина	
					$height_img = $imsize[1]; // высота
					if ($width_img > $height_img) { $top = 1; $left = 47; } 
					else { $top = -14; $left = 41; }

					$ScanHashDataStr = str_replace(',', ' ', $scan_hash[0]['hash']);

					if (isset($scan_hash_date[0]['date'])) {
						$date_tags = explode("-", $scan_hash_date[0]['date']);	
					} else { 
						$scan_hash_date[0]['date'] == '';  
					}
					srand(make_seed());
					$randval = rand();

					echo "<div class='scan_blocks' style='border: 2px solid #7ac8e4;'>";
						echo "<div class='scan_img'>";
							echo "<div class='scan_id'>";
								echo 'id:'.$scan_all[$i]['id'];
							echo "</div>";
							echo "<div>";
								echo "<a href='/scan/resize/".$filename.".jpg?n=".$randval."' class='highslide' onclick='return hs.expand(this)'>";
									echo "<img src='/scan/preview/".$filename.".jpg?n=".$randval."' style='max-width: 100px; max-height: 100px'>";
								echo "</a>";
							echo "</div>";
							echo "<div id='turn' class='scan_turn' data-id='".$filename."'>";
								echo "<input type='image' id='left' width='15px' src='img/left.png'>";
								echo "<input type='image' id='right' width='15px' src='img/right.png'>";
							echo "</div>";
						echo "</div>";
						echo "<div id='border'>";
							echo "<div class='scan_input'>";
								echo "<div class='scan_select_block'>";
									$AllAdminOnline = AllScanAdmin ($dbcnx);		
									if (isset($_POST['page'])) {
										echo "<input name='page' type='hidden' value='".$_POST['page']."'>"; 
									}
									echo "<select size='1' id='who' name='admin' style='width: 157px;height: 24px;'>";
										echo "<option value='0'></option>";
										for ($o=0; $o<count($AllAdminOnline); $o++) {
											if ($scan_hash_date[0]['responsible'] == $AllAdminOnline[$o]["id"]) {
												echo "<option value='".$AllAdminOnline[$o]["id"]."' selected>".$AllAdminOnline[$o]["fio"]."</option>";
											} else {
												echo "<option value='".$AllAdminOnline[$o]["id"]."'>".$AllAdminOnline[$o]["fio"]."</option>";
											}
										}
									echo "</select>";		
									echo "<input name='filename' type='hidden' size='2' value='".$filename."'>";
									echo "<input id='meeting' name='scan_date' type='date' value='".$scan_hash_date[0]['date']."'/>"; 	
									$scan_sklad = scan_sklad ($dbcnx);			
									echo "<select size='1' id='where' name='sklad' style='width:157px;height:24px;'>";
										echo "<option value='0'></option>";
										foreach ($scan_sklad as $shop) {
											if ($scan_hash_date[0]['id_object'] == $shop["id"]) {
												echo "<option value='".$shop["id"]."' selected>".$shop["sklad"]."</option>";
											} else {
												echo "<option value='".$shop["id"]."'>".$shop["sklad"]."</option>";
											}
										}
									echo "</select>";
								echo "</div>";
								echo "<div class='scan_icon scan_grid-1' id='".$filename."'>";
									echo "<b>".$scan_hash_date[0]['whet']."</b>";
									echo "<a onclick=\" newWindow = window.open('/scan/".$filename.".jpg', '_blank','height=844,width=1125'); newWindow.focus(); newWindow.print();\"><i class='fas fa-print'></i></a>";
									echo "<div id='save' title='save'>";
										echo "<i name='save' class='fas fa-save but-save'></i>";
									echo "</div>";
									if ($userdata['access'] > 8) {
										echo "<div id='del' title='delete'>";
											echo "<i name='del' class='fas fa-trash but-del'></i>";
										echo "</div>";
									} else {
										echo "<i name='del' class='fas fa-trash but-del'></i>";
									}	
									
								echo "</div>";
								echo "<div class='scan_textarea'>";
									echo "<textarea class='textarea' id='hash' name='hash' WRAP='virtual' COLS='40' ROWS='4' placeholder='поиск по хештегам, через ENTER' style='resize: none;'>".$ScanHashDataStr."</textarea>"; 
								echo "</div>";
							echo "</div>";
						echo "</div>";
					echo "</div>";
					$y++;
				}
				echo "</div>";
				echo "<div class='number_page'>";	
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
						echo "<div class='number'>";
							echo "<form method='POST' action='scan.php'>";
								echo "<input name='page' type='hidden' value='1'>";
								if (isset($admin)) { echo "<input name='admin_find' type='hidden' value='".$admin."'>"; }
								if (isset($_POST['sklad_find'])) { echo "<input name='sklad_find' type='hidden' value='".$_POST['sklad_find']."'>"; }
								if (isset($_POST['start'])) { echo "<input name='start' type='hidden' value='".$_POST['start']."'>"; }
								if (isset($_POST['end'])) { echo "<input name='end' type='hidden' value='".$_POST['end']."'>"; }
								if (isset($_POST['find'])) {
									echo "<input name='find' type='hidden' value='".$_POST['find']."'>";
									echo "<input type='submit' value='1 ' id='top10_hash'>";
								} elseif (isset($_POST["hash_tags_find_post"])) {
									echo "<input name='hash_tags_find_post' type='hidden' value='".$_POST['hash_tags_find_post']."'>";
									echo "<input type='submit' value='1 ' id='top10_hash'>";
								} else {
									echo "<input name='find' type='hidden' value='".$_POST['find']."'>";
									echo "<input type='submit' value='1 ' id='top10_hash'>";
								}
							echo "</form>";
						echo "</div>";
					}
					$y = $current_page - 1;
					if ($current_page > 9) {
						echo "<div class='number'>...</div>";
					} else {
						for($i = 2; $i <$current_page; $i++) {
							echo "<div class='number'>";
								echo "<form method='POST' action='scan.php'>";
								echo "<input name='page' type='hidden' value='".$i."'>";
								if (isset($admin)) { echo "<input name='admin_find' type='hidden' value='".$admin."'>"; }
								if (isset($_POST['sklad_find'])) { echo "<input name='sklad_find' type='hidden' value='".$_POST['sklad_find']."'>"; }
								if (isset($_POST['start'])) { echo "<input name='start' type='hidden' value='".$_POST['start']."'>"; }
								if (isset($_POST['end'])) { echo "<input name='end' type='hidden' value='".$_POST['end']."'>"; }
								if (isset($_POST['find'])) {
									echo "<input name='find' type='hidden' value='".$_POST['find']."'>";
									echo "<input type='submit' value='".$i."' id='top10_hash'>";
								} elseif (isset($_POST["hash_tags_find_post"])) {
									echo "<input name='hash_tags_find_post' type='hidden' value='".$_POST['hash_tags_find_post']."'>";
									echo "<input type='submit' value='".$i."' id='top10_hash'>";
								} else {
									echo "<input type='submit' value='".$i."' id='top10_hash'>";
								}
								echo "</form>";
							echo "</div>";
						}
					}
					for ($b = $current_page; $b <= $last_page; $b++) {
						if($b == $page) {
							echo "<div class='number black_number'>[".$b."]</div> ";
						} else {
							echo "<div class='number'>";
								echo "<form method='POST' action='scan.php'> ";
								echo "<input name='page' type='hidden' value='".$b."'>";
								if (isset($admin)) { echo "<input name='admin_find' type='hidden' value='".$admin."'>"; }
								if (isset($_POST['sklad_find'])) { echo "<input name='sklad_find' type='hidden' value='".$_POST['sklad_find']."'>"; }
								if (isset($_POST['start'])) { echo "<input name='start' type='hidden' value='".$_POST['start']."'>"; }
								if (isset($_POST['end'])) { echo "<input name='end' type='hidden' value='".$_POST['end']."'>"; }
								if (isset($_POST['find'])) {
									echo "<input name='find' type='hidden' value='".$_POST['find']."'>";
									echo "<input type='submit' value='".$b."' id='top10_hash'>";
								} elseif (isset($_POST["hash_tags_find_post"])){
									echo "<input name='hash_tags_find_post' type='hidden' value='".$_POST['hash_tags_find_post']."'>";
									echo "<input type='submit' value='".$b."' id='top10_hash'>";	
								} else {
									echo "<input type='submit' value='".$b."' id='top10_hash'>";
								}
								echo "</form>";
							echo "</div>";
						}
					}
					$y = $last_page + 1;
					if ($last_page < ceil($all/$per_page) && ceil($all/$per_page) - $last_page > 2) {
						echo "<div class='number'>...</div>";
					}
					$e = ceil($all/$per_page);
					if ($last_page < ceil($all/$per_page)) {
						echo "<div class='number'>";
							echo "<form method='POST' action='scan.php'> ";
							echo "<input name='page' type='hidden' value='".$e."'>";
							if (isset($admin)) { echo "<input name='admin_find' type='hidden' value='".$admin."'>"; }
							if (isset($_POST['sklad_find'])) { echo "<input name='sklad_find' type='hidden' value='".$_POST['sklad_find']."'>"; }
							if (isset($_POST['start'])) { echo "<input name='start' type='hidden' value='".$_POST['start']."'>"; }
							if (isset($_POST['end'])) { echo "<input name='end' type='hidden' value='".$_POST['end']."'>"; }
							if (isset($_POST['find'])) {
								echo "<input name='find' type='hidden' value='".$_POST['find']."'>";
								echo "<input type='submit' value='".$e."' id='top10_hash'>";
							} elseif (isset($_POST["hash_tags_find_post"])){
								echo "<input name='hash_tags_find_post' type='hidden' value='".$_POST['hash_tags_find_post']."'>";
								echo "<input type='submit' value='".$e."' id='top10_hash'>";
							} else {
								echo " <input type='submit' value='".$e."' id='top10_hash'> ";
							}
							echo "</form>";	
						echo "</div>";
					}
					echo "<div class='number'>";
						echo "<form method='POST' action='scan.php'>";
						echo "<select size='1' name='page' style='width: 50px;height: 24px;' onchange=\"this.form.submit()\">";
							for($i = 1; $i <= $e; $i++) {
								if ($i == $page) {
									echo "<option value='".$i."' selected>".$i."</option>";
								} else {
									echo "<option value='".$i."'>".$i."</option>";
								}
							}
						echo "</select>";
						echo "</form>";
					echo "</div>";
				} else {
					echo "<b>Документов не найдено</b>";
				}
				echo "</div>";
			echo "</div>";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
////////////////////  НУМЕРАЦИЯ СТРАНИЦ НИЖНЯЯ - КОНЕЦ   ////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			echo "<div class='hash'>";										
			$top10_hash = top10_hash($dbcnx);																			   	
			for ($i=0;$i<count($top10_hash);$i++){ 
				echo "<form method='POST' action='scan.php'>";
				echo "<input name='find' type='hidden' size='2' value='".$top10_hash[$i]['LOWER( hash )']."'>";
				echo "<input type='submit' name='submit' value='#".$top10_hash[$i]['LOWER( hash )']."' id='top10_hash'><br>";
				echo "</form>";
			}																												
			echo "</div>";	
		
		
		echo "</div>";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
}

?>
<script>

	let $divScanBlock = document.querySelector('.scan_block');
	$divScanBlock.addEventListener('click', handleDivClick);
	
	function handleDivClick (event) {
		let $id = event.target.parentNode.id;
		if ($id == 'turn') {
			let filename = event.target.parentNode.dataset.id;
			let on_turn = event.target.id;
			let randval = Math.random();

			$.ajax({
				type: "POST",
				url: "function/function_scan.php",
				data: {
					turn: on_turn,
					filename: filename,
				},
				success: function(html) {
					$(event.target.parentNode.previousSibling).html("<a href='/scan/resize/" + filename + ".jpg?n=" + randval + "' class='highslide' onclick='return hs.expand(this)'><img src='/scan/preview/" + filename +".jpg?n=" + randval + "' style='max-width: 100px;'></a>");
				}
			});
		}
		if ($id == 'save') {
			let filename = event.target.parentNode.parentNode.id;
			let count = event.target.parentNode.previousSibling.previousSibling.innerHTML;
			let who = event.target.parentNode.parentNode.previousSibling.querySelector('#who').value;
			let date = event.target.parentNode.parentNode.previousSibling.querySelector('#meeting').value;
			let where = event.target.parentNode.parentNode.previousSibling.querySelector('#where').value;
			let hash = event.target.parentNode.parentNode.nextSibling.querySelector('#hash').value;
			let greenBlock = event.target.parentNode.parentNode.parentNode.parentNode.parentNode;
			/*
				console.log('filename: ' + filename);
				console.log('who: ' + who);
				console.log('date: ' + date);
				console.log('where: ' + where);
				console.log('hash: ' + hash);
				console.log('count: ' + count);
			*/	
			$.ajax({
				type: "POST",
				url: "function/function_scan.php",
				data: {
					save: 'save',
					filename: filename,
					who: who,
					date: date,
					where: where,
					hash: hash,
					count: ++count,
				},
				success: function(html) {
					greenBlock.style.border = '2px solid green';
					$(event.target.parentNode.previousSibling.previousSibling).html(count);
				}
			});
		}
		if ($id == 'del') {
			if (confirm("Вы действительно хотите удалить этот документ?")) {
				let filename = event.target.parentNode.parentNode.id;
				let redBlock = event.target.parentNode.parentNode.parentNode.parentNode.parentNode;
				
					console.log('filename: ' + filename);
					
				$.ajax({
					type: "POST",
					url: "function/function_scan.php",
					data: {
						del: 'del',
						filename: filename,
					},
					success: function(html) {
						redBlock.style.border = '2px solid red';
					}
				});
			} else {
				return false;
			}
		}
	}

</script>
<?php

/* FOOTER */ include_once ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");