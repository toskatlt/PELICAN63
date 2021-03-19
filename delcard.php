<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");


$date_time = date("y-m-d H:i:s");
if (isset($_GET['id'])) $id_object = $_GET['id'];
if (isset($_GET['e'])) $etype = $_GET['e'];

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	$uname = $userdata['id'];
	if($userdata['id_group'] == "1") {
		$selectObject = selectObject ($dbcnx, $id_object); $nameObject = $selectObject['name'];
		echo "<br><br><br><br><div class='card_body'>";
		echo "<div class='link'>/ <a href='ip_pelican.php' title='Магазины'>Магазины</a> / <a href='card.php?id=".$id_object."' title='Пеликан'>".$nameObject."</a> / <a href='editcard.php?id=".$id_object."&e=".$etype."' title='Пеликан'>Редактирование</a> / <a href='delcard.php?id=".$id_object."&e=".$etype."' title='Пеликан'>Удаление</a></div><div class='card'>";
		echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr>";
		echo "<br><center><b>Удаление данных в магазине ".$nameObject."</b></center><br><p>";
		echo "<form name='duser' method='POST' action='delcard.php?id=".$id_object."&e=".$etype."'><br>";
	

//////   8   //////////////////////////////////////////////////////////////////////////////////////		
		if (isset($_POST['submit8'])) {
			$arr=array();
			$arr=$_POST['video'];
			for ($i=0;$i<count($arr);$i++){
				
				$shop_video_get = $arr[$i];
				
				$ShopVideoSelectID = ShopVideoSelectID ($dbcnx, $shop_video_get);
				$video_select = $ShopVideoSelectID[0];
				
				$update_log = "VIDEO Удалено: ".$video_select." ";
				$log = "INSERT INTO `log` (`user_name`, `date`, `table`, `id_mag`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'video', '".$shop_id_get."', '".$update_log."')";
				mysql_query($log,$dbcnx);
				
				$del = "DELETE FROM `video` WHERE id='".$arr[$i]."'";
				mysql_query($del,$dbcnx);
			}
		header('Location: '.$_SERVER['PHP_SELF'].'?id='.$shop_id_get.'&e=8');
		die();
		}	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   3 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if ($etype == 3) {
			if (isset($_POST['submit3'])) {
				for ($i=0;$i<count($_POST['ws']);$i++) {
					$selectIPWSInObject = selectIPWSInObject ($dbcnx, $_POST['ws'][$i]);
					$update_log = "- Удалено: ".$selectIPWSInObject['ip']." ";
					mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'ws', '".$id_object."', '".$update_log."')", $dbcnx);
					mysql_query("DELETE FROM `ws` WHERE id='".$_POST['ws'][$i]."'", $dbcnx);
				}
			}		
			$i=1; $wsInObject = wsInObject ($dbcnx, $id_object);
			if (count($wsInObject) > 0) {	
				foreach ($wsInObject as $a) {
					echo "ПК [".$i++."]: ".$a['ip']." ".$w['os']." <label><input name='ws[]' type='checkbox' value='".$a['id']."'/></label><br>";
				}
			} else { echo "на объекте нет рабочих станций <br><br>"; }
			echo "<br>";
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Изменить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='submit' name='submit".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   4 +  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 4) {
			if (isset($_POST['submit4'])) {
				for ($i=0; $i < count($_POST['kas']); $i++){
					$selectWs = selectWs ($dbcnx, $_POST['kas'][$i]);				
					$update_log = "- Удалено: ".$selectWs['ip']." ";
					mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'ws', '".$id_object."', '".$update_log."')", $dbcnx);
					mysql_query("DELETE FROM `ws` WHERE id='".$_POST['kas'][$i]."'", $dbcnx);
				}
			}			
			$selectWsInObject = selectWsInObject ($dbcnx, $id_object, 1); $i = 1;
			foreach ($selectWsInObject as $a) {
				echo "Касса [".$i++."]: ".$a['ip']." <label><input name='kas[]' type='checkbox' value='".$a['id']."'/></label><br><br>";
			}	 
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Изменить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='submit' name='submit".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   5 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 5) {
			if (isset($_POST['submit5'])) {
				for ($i=0; $i<count($_POST['id']); $i++){
					$selectVes = selectVes ($dbcnx, $_POST['id'][$i]);
					$update_log = "- Удалено: ".$selectVes['ip']." ";
					mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$uname."', '".$date_time."', 'ves', '".$id_object."', '".$update_log."')", $dbcnx); 				
					mysql_query("DELETE FROM `ves` WHERE id='".$_POST['id'][$i]."'", $dbcnx);
				}
			}
			$vesInObject = vesInObject($dbcnx, $id_object); 
			$i = 1;
			foreach ($vesInObject as $a) {
				echo "Весы [".$i++."]: ".$a['ip']." <label><input name='id[]' type='checkbox' value='".$a['id']."'/></label><br>";
			}	
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Изменить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='submit' name='submit".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   7 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 7) {	
			if (isset($_POST['submit7'])) {
				for ($i=0; $i<count($_POST['id']); $i++){				
					mysql_query("DELETE FROM `printers` WHERE id='".$_POST['id'][$i]."'", $dbcnx);
				}
			}
	
			exec("printer_scan.py ".$id_object, $out, $err);
			
			$printersInObject = printersInObject($dbcnx, $id_object); $i = 1;
			foreach ($printersInObject as $printer) {		
				echo "<label><input name='id[]' type='checkbox' value='".$printer['id']."'/></label> [".$i++."]: <b>".$printer['print_name']."</b> на <b>".$printer['ip']."</b><br>";
			}
			echo "<br>";	
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='submit' name='submit".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}			
//////   8   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 8) {		
			echo "<br><br><br><br><div id='card_body'>";
			echo "<div class='link'>/ <a href='ip_pelican.php' title='Магазины'>Магазины</a> / <a href='card.php?id=".$shop_id."' title='Пеликан'>".$shop_name."</a> / <a href='editcard.php?id=".$shop_id."&e=".$etype."' title='Пеликан'>Редактирование</a> / <a href='delcard.php?id=".$shop_id."&e=".$etype."' title='Пеликан'>Удаление</a></div><div class='card'>";
			echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
			echo "<br><center><b>Удаление видеорегистраторов в магазине ".$shop_name."</b></center><br><br><p>";
			echo "<form name='video' method='POST' action='delcard.php?id=".$shop_id_get."&e=".$etype."'>";
				$z=1;
				$idmag = $shop_id;
				$vid = video ($dbcnx, $idmag);
				
				foreach ($vid as $sws):
				{
					
				$mag_vid = $sws['id_mag'];
				$ipvid = $sws['ip_video'];
				$idvid = $sws['id'];
				
				echo "Видеорегистратор[".$z."]: ".$ipvid;
				echo "<label><input name='video[]' type='checkbox' value='".$idvid."'/></label><br>";
				$z=$z+1;

				} endforeach;
				
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$shop_id_get."' value='Назад'/> <input type='button' onclick=javascript:window.location='editcard.php?id=".$shop_id_get."&e=".$etype."' value='Изменить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$shop_id_get."&e=".$etype."' value='Добавить'/> <input type='submit' name='submit".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   9 +   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 9) {		
			if (isset($_POST['submit9'])) {
				for ($i=0; $i<count($_POST['del']); $i++) {							
					$selectDomainUser = selectDomainUser ($dbcnx, $_POST['del'][$i]);
					$update_log = "- Удалено: ".$selectDomainUser[0]['username']." ";
					mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."','".$date_time."','domain_user','".$id_object."','".$update_log."')", $dbcnx);
					mysql_query("DELETE FROM `domain_user` WHERE id='".$_POST['del'][$i]."'", $dbcnx);
				}
			}			
			$selectDomainUserInObject = selectDomainUserInObject ($dbcnx, $id_object); $i=1;
			foreach ($selectDomainUserInObject as $a) {
				echo "Пользователь [".$i++."]: <b>".$a['username']."</b> <label><input name='del[]' type='checkbox' value='".$a['id']."'/></label><br><br>";
			}
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Изменить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='submit' name='submit".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   11 +  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($etype == 11) {
			if (isset($_POST['submit11'])) {
				for ($i=0; $i<count($_POST['del']); $i++) {	
					$selectTSD = selectTSD ($dbcnx, $_POST['del'][$i]);
					$update_log = "- Удалено: ".$selectTSD['imei']." | ".$selectTSD['serial']." ";
					mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."','".$date_time."','ws','".$id_object."','".$update_log."')", $dbcnx);
					mysql_query("DELETE FROM `ws` WHERE id='".$_POST['del'][$i]."'", $dbcnx);
				}
			}			
			$tsdInObject = tsdInObject ($dbcnx, $id_object); $i = 1;
			foreach ($tsdInObject as $a) {		
				echo "ТСД [".$i++."]: <b>IMEI: </b> ".$a['imei']." <b> | SERIAL: </b> ".$a['serial']." <label><input name='del[]' type='checkbox' value='".$a['id']."'/></label><br><br>";
			} 
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='editcard.php?id=".$id_object."&e=".$etype."' value='Изменить'> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='submit' name='submit".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}		
//////   13   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if ($etype == 13) {
			if (isset($_POST['submit13'])) {
				for ($i=0; $i<count($_POST['del']); $i++){
					$update_log = "- Удалено: ".$_POST['del'][$i]." ";
					mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."', '".$date_time."', 'opener', '".$id_object."', '".$update_log."')", $dbcnx);
					mysql_query("DELETE FROM `opener` WHERE id='".$_POST['del'][$i]."'", $dbcnx);
				}
			}			
			$whoOpener = whoOpener ($dbcnx, $id_object); $i = 1;
			foreach ($whoOpener as $a) {	
				echo "[".$i++."]: ".$a['fio']." <label><input name='del[]' type='checkbox' value='".$a['id']."'/></label><br>";
			}	
				
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='submit' name='submit".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}
//////   14   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if ($etype == 14) {
			if (isset($_POST['submit14'])) {
				for ($i=0; $i<count($_POST['del']); $i++){
					$update_log = "- Удалено: ".$_POST['del'][$i]." ";
					mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."', '".$date_time."', 'email', '".$id_object."', '".$update_log."')", $dbcnx);
					mysql_query("DELETE FROM `email` WHERE id='".$_POST['del'][$i]."'", $dbcnx);
				}
			}			
			$emailInObject = emailInObject ($dbcnx, $id_object); $i = 1;
			foreach ($emailInObject as $a) {	
				echo "[".$i++."]: ".$a['email']." <label><input name='del[]' type='checkbox' value='".$a['id']."'/></label><br>";
			}	
				
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='submit' name='submit".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}	
//////   15   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if ($etype == 15) {
			if (isset($_POST['submit15'])) {
				for ($i=0; $i<count($_POST['del']); $i++){
					$update_log = "- Удалено: ".$_POST['del'][$i]." ";
					mysql_query("INSERT INTO `log` (`id_domain_user`,`date`,`table`,`id_object`,`inquiry`) VALUES ('".$uname."', '".$date_time."', 'phone', '".$id_object."', '".$update_log."')", $dbcnx);
					mysql_query("DELETE FROM `phone` WHERE id='".$_POST['del'][$i]."'", $dbcnx);
				}
			}			
			$phoneInObject = phoneInObject ($dbcnx, $id_object); $i = 1;
			foreach ($phoneInObject as $a) {	
				echo "[".$i++."]: ".$a['number']." <label><input name='del[]' type='checkbox' value='".$a['id']."'/></label><br>";
			}	
				
			echo "<p><input type='button' onclick=javascript:window.location='card.php?id=".$id_object."' value='Назад'/> <input type='button' onclick=javascript:window.location='addcard.php?id=".$id_object."&e=".$etype."' value='Добавить'/> <input type='submit' name='submit".$etype."' value='Удалить'/></p>";
			echo "</form>";
		}		
//////  end  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
}
echo "<br><br>";	
include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");	