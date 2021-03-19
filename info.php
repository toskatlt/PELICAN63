<?php
include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php"); /* HEADER */
include "config.php";

	$day_today = date("d");
	$month_today = date("m");
	
	$kod = $day_today * $month_today;
	$rest = substr($kod, 0, 4);
	$strlen = strlen($rest);
	if ($strlen == 1){ $kodinst = '77070'.$rest.'07070'; }
	else { $kodinst = '7707'.$rest.'07070'; }
	
echo "<div style='position:relative;'>";	
echo "<center>";	

	echo "<b style='color:#fff;'>КОД ИНСТАЛЯЦИИ: ".$kodinst."</b><br>";
	echo "<b style='color:#fff;'>ИНСТАЛЛЯТОР: 006</b><br>";
	
	echo "<br>";
	echo "<b style='color:#fff;'><a href='mailto:support@neo63.ru'>Оставить заявку в тех.поддержку</b></a>";
	echo "<br>";
	
	echo "<br><br>";
	echo "<table class='tablesorter' style='margin:auto; padding-bottom:20;' cellspacing='1'>";
	echo "<thead>";
	echo "<tr><th width='96'>Дата</th><th width='340'>Имя</th><th width='160'>Телефон</th>";
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
	
	echo "<br><br>";
	
	echo "<table class='tablesorter' style='margin:auto; padding-bottom:20;' cellspacing='1'>";
	echo "<thead>";
	echo "<tr><th width='340'>Имя</th><th width='160'>Телефон</th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tr>";

	$dutyAdmin = dutyAdmin ($dbcnx);
	foreach ($dutyAdmin as $admin) {
		echo "<tr>";
		// Имя дежурного
		echo "<td align=center valign=middle style='border:0px;'><table class='table_m' cellspacing=0><td style='border:0px;'><a href='img/face_admin/".$admin['id'].".jpg' class='highslide' onclick='return hs.expand(this)'><img align=center src='img/face_admin/".$admin['id'].".jpg' alt=".$admin['fio']." width=32 height=32 class='leftimg' style='padding-right:0px; padding-left:0px'></a></td><td  style='border:0px;'><p align=middle>".$admin['fio']."</p></td></table></td>";
		// Телефон дежурного
		echo "<td align=center>".$admin['phone']."</td> ";
		echo "</tr>";
	}
	echo "</tr>";
	echo "</table>";
	echo "<br>";
echo "</center>";
echo "</div>";		