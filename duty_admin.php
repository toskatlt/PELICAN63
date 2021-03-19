<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

?>
<style> 
#SELECT {
	height: 24px;
}
#img_face {
	border: 1px solid #fff;
	width: 50px;
	height: 50px;
}
</style>		
<?php
if (isset($_POST["date"])) {
	mysql_query("INSERT INTO `calendar` (`date`, `id_domain_user`) VALUES ('".$_POST['date']."','".$_POST['select']."')", $dbcnx);
}
if (isset($_POST["delete"])) {
	foreach ($_POST['checkbox'] as $del){ 
		mysql_query("DELETE FROM calendar WHERE id=".$del, $dbcnx);
	}
}

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if ($userdata['id_group'] == "1") {
		echo "<div align=center>";
		echo "<h3 style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #fff;text-align:center;'>Ответственный дежурный:</h3>";

		//ВЫВОД ДЕЖУРНЫХ
		echo "<form name='' method='post' id='example_group2' >";
		echo "<table class='tablesorter'  style='margin:auto; padding-bottom:20;' cellspacing='1'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th width='96'>Дата</th>";
		echo "<th width='340'>Имя</th>";
		echo "<th width='160'>Телефон</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tr>";
			
		$date = date("d.m.Y");
		$time = strtotime("+1 day");
		$fecha = date("d.m.Y", $time);
		$nearestDuty = nearestDuty ($dbcnx);	
		foreach ($nearestDuty as $row) {
			echo "<tr>";
			$newdate=date('d.m.Y', strtotime($row['date']));
			echo "<td align=center>";
			if ($date == $newdate) echo "Сегодня";		
			elseif ($newdate == $fecha) echo "Завтра";
			else echo $newdate;
			
			echo "</td>";
			// Имя дежурного
			echo "<td align=center valign=middle style='border:0px;'><table class='table_m' cellspacing=0><td style='border:0px;'><a href='img/face_admin/".$row['id'].".jpg' class='highslide' onclick='return hs.expand(this)'><img align=center src='img/face_admin/".$row['id'].".jpg' alt=".$row['fio']." width=32 height=32 class='leftimg' style='padding-right:0px; padding-left:0px'></a></td><td  style='border:0px;'><p align=middle>".$row['fio']."</p></td></table></td>";
			// Телефон дежурного
			echo "<td align=center>".$row['phone']."</td> ";
			echo "</tr>";
		}
		echo "</tr>";
		echo "</table>";
		echo "</form>";
			
		echo "<table>";	
		echo "<h3 style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #fff;text-align:center;'>Назначение дежурного:</h3>";
		echo "<form action='' method='post'>";
		echo "<select name='select' id='select'>";
		
		$dutyAdmin = dutyAdmin ($dbcnx);			
		foreach ($dutyAdmin as $adminSelect){	
			echo "<option name='".$adminSelect['id']."' value='".$adminSelect['id']."'>".$adminSelect['fio']."</option>";
		}
		echo "</select>";
		echo "<input type='date' min='2015-01-01' name='date'>";
		echo "<input type='submit' name='submit1' value='Добавить' style='width:90px; height:24px' onclick=window.location.reload()><br>";
		echo "</form>";
		echo "</table>";

		// Начало таблицы
		echo "<form name='delete' method='post' id='example_group2' style='margin:auto; margin-bottom:50px;'>";
		echo "<table class='tablesorter'  style='margin:auto;' cellspacing='1'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th>Дата</th>";
		echo "<th>Имя</th>";
		echo "<th>Телефон</th>";
		echo "<th><input type='submit' name='delete' value='Удалить' style='width:120'><br></th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tr>";
		
		$admin25top = admin25top($dbcnx);
		foreach ($admin25top as $top) {
			$today = time();
			$timestamp = strtotime($top['date']);
			$newdate=date('d.m.Y', strtotime($top['date']));
			if ($timestamp >= $today ) {
				echo "<tr><td width='125'>".$newdate."</td>";
				echo "<td align=center valign=middle style='border:0px;'><table class='table_m' cellspacing=0><td style='border:0px;'><a href='/img/face_admin/".$top['id_domain_user'].".jpg' class='highslide' onclick='return hs.expand(this)'><img align=center src='img/face_admin/".$top['id_domain_user'].".jpg' alt=".$top['fio']." width=32 height=32 class='leftimg' style='padding-right:0px; padding-left:0px'></a></td><td  style='border:0px;'><p align=middle>".$top['fio']."</p></td></table></td>";
				echo "<td align=center width='125'>".$top['phone']."</td> ";
				echo "<td width='180'><input type='checkbox' name='checkbox[]' value=".$top['id']."></td>";
				echo "</tr>";
			} else {
				echo "<tr ><td width='125' style='background: #e6e6e6;'>".$newdate."</td>";			
				echo "<td align=center valign=middle style='border:0px; background:#e6e6e6;'><table class='table_m' cellspacing=0><td style='border:0px;background:#e6e6e6;''><a href='/img/face_admin/".$top['id_domain_user'].".jpg' class='highslide' onclick='return hs.expand(this)'><img align=center src='img/face_admin/".$top['id_domain_user'].".jpg' alt=".$top['fio']." width=32 height=32 class='leftimg' style='padding-right:0px; padding-left:0px'></a></td><td  style='border:0px;background:#e6e6e6;'><p align=middle>".$top['fio']."</p></td></table></td>";
				echo "<td style='background: #e6e6e6;' align=center width='125'>".$top['phone']."</td> ";
				echo "<td style='background: #e6e6e6;' width='180'><input type='checkbox' name='checkbox[]' value=".$top['id']."></td>";
				echo "</tr>";
			}
		}
		echo "</tr>";
		echo "</table>";
		echo "</form>";
		//-------------------------------------------------------------------------------------
		echo "<div class='face_admin' style='position: absolute;top: 200px;left: 75px;'>";	
			echo "<a href=/img/face_admin/624.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/624.jpg id='img_face'></a><br>";
			echo "<a href=/img/face_admin/631.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/631.jpg id='img_face'></a><br>";
			echo "<a href=/img/face_admin/625.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/625.jpg id='img_face'></a><br>";
			//echo "<a href=/img/face_admin/623.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/623.jpg id='img_face'></a><br>";
			echo "<a href=/img/face_admin/634.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/634.jpg id='img_face'></a><br>";
			echo "<a href=/img/face_admin/633.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/633.jpg id='img_face'></a><br>";
			//echo "<a href=/img/face_admin/628.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/628.jpg id='img_face'></a><br>";
			echo "<a href=/img/face_admin/627.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/627.jpg id='img_face'></a><br>";
			echo "<a href=/img/face_admin/632.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/632.jpg id='img_face'></a><br>";
			echo "<a href=/img/face_admin/629.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/629.jpg id='img_face'></a><br>";
			echo "<a href=/img/face_admin/630.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/630.jpg id='img_face'></a><br>";
			echo "<a href=/img/face_admin/718.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/718.jpg id='img_face'></a><br>";
			echo "<a href=/img/face_admin/766.jpg class='highslide' onclick='return hs.expand(this)'><img src=/img/face_admin/766.jpg id='img_face'></a><br>";
		echo "</div>";
		//------------------------------------------------------------------------------------	
		echo "</div>";
	}
}	

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); ?>