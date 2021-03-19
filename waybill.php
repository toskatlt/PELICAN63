<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");

if (isset($_GET['e'])) $etype = $_GET['e'];
$date = date("d.m.Y");

?>
<style>
a.button1 {
  display: inline-block;
  width: 10em;
  height: 8em;
  font-size: 240%;
  color: rgba(255,255,255,.9);
  text-shadow: #2e7ebd 0 1px 2px;
  text-decoration: none;
  text-align: center;
  line-height: 0.7;
  font-weight: 400;
  white-space: pre-line;
  padding: .9em 0;
  border: 1px solid;
  border-color: #60a3d8 #2970a9 #2970a9 #60a3d8;
  border-radius: 6px;
  outline: none;
  background: #60a3d8 linear-gradient(#89bbe2, #60a3d8 50%, #378bce);
  box-shadow: inset rgba(255,255,255,.5) 1px 1px;
}
a.button1:first-line{
  font-size: 1000%;
  font-weight: 700;
}
a.button1:hover {
  color: rgb(255,255,255);
  background-image: linear-gradient(#9dc7e7, #74afdd 50%, #378bce);
}
a.button1:active {
  color: rgb(255,255,255);
  border-color: #2970a9;
  background-image: linear-gradient(#5796c8, #6aa2ce);
  box-shadow: none;
}
</style>
<!-- НОВЫЙ CSS3 !-->
<style>
.date, .date2 {
	top: 20px;
}
.date { left: calc(50% - 125px); }
.date2 { left: calc(100% - 125px); }

.akt, .akt2 {
	top: 30px;
	font-size: 26px;
	font-weight: bold;
}
.akt { left: calc(25% - 100px); }
.akt2 { left: calc(75% - 100px); }

.komu, .komu2, .otkogo, .otkogo2, .sdal {
	font-size: 16px;
	font-weight: bold;
}
.komu, .komu2 { top: 70px; }
.komu { left: 2%; }
.komu2 { left: 52%; }

.otkogo, .otkogo2 { top: 95px; }
.otkogo { left: 2%; }
.otkogo2 { left: 52%; }

.sdal, .prinal, .sdal2, .prinal2 {
	top: 89%;
}
.sdal {
	left: 3%;
}
.prinal {
	left: 25%;
}
.sdal2 {
	left: 53%;
}
.prinal2 {
	left: 75%;
}

//////////////////////
.hr, .hr2 {
	top: 90px;
}
.hr3, .hr4 {
	top: 115px;
}
.hr, .hr2, .hr3, .hr4 {
	width: 40%; 
}
.hr, .hr3 { left: 5%; }
.hr2, .hr4 { left: 55%; } 

/////////////////////
.hr5, .hr6 {
}
.hr7, .hr8 {
}
.hr5, .hr6, .hr7, .hr8 {
	top: 91.5%;
	width: 18%; 
	height: 0; 
	border-bottom: 1px solid black;
}
.hr5 { left: 5%; }
.hr6 { left: 28%; }
.hr7 { left: 55%; } 
.hr8 { left: 78%; } 

.logo, .logo2 { 
	width: 35px;
	top: 15px;	
}
.logo {
	left: 2%;
}
.logo2 {
	left: 52%;
}
.position__absl {
	position: absolute;
}
.input {
	padding: 0px 0px;
    border-width: 0px;
	font-size: 14px;
}
.hrV {
	top: -10;
	bottom: 10;
	height: 97%;
	width: 1px;
	left: 50%;
	color: black;
	background: black;
}
.table, .table2 {
	position: absolute;
	width: 48%;
	top: 130px;
}
.table { 
	left: 1%;
}
.table2 { 
	left: 51%;
}
.input_table, .input_table2 {
	width: 100%;
	border: none;
	border-bottom: 1px solid black;
}
.input_table2 {
	text-align: center;
}
.print {
	position: absolute;
	top: 95%;
	left: 52%;
}

</style>
<?php

if (isset($_GET['height'])) {
    //echo 'Высота экрана: ' . $_GET['height'] . "<br />\n";
}
else {
    echo "<script language='javascript'>\n";
    echo " location.href=\"${_SERVER['SCRIPT_NAME']}?${_SERVER['QUERY_STRING']}"
            . "&height=\" + screen.height;\n";
    echo "</script>\n";
}

if (isset($_GET['usr'])) $username = $_GET['usr'];

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) { 
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['id'] == $_COOKIE['id']) {
			$size_naim = '38%';
			$size_naim2 = '20%';
			$size_kol = '8%';
			
			if ($_GET['height'] < 769) {
				$y = 20;
			}
			elseif (($_GET['height'] > 899) and (($_GET['height'] < 1023))) {
				$y = 22;
			}
			elseif ($_GET['height'] > 1023) {
				$y = 24;
			}
			
			
			echo "<hr noshade class='hrV position__absl'>";
			
			echo  "<div class='date position__absl'>от: ".$date."</div>";
			echo  "<div class='date2 position__absl'>от: ".$date."</div>";
			echo  "<div class='akt position__absl'>НАКЛАДНАЯ</div>";
			echo  "<div class='akt2 position__absl'>НАКЛАДНАЯ</div>";
			
			echo "<div class='komu position__absl'>Кому: 
				<input class='input' type=text size=".$size_naim." value='Пеликан'>
			</div>";
			echo "<div class='hr position__absl'>&nbsp;</div>";
			echo "<div class='otkogo position__absl'> От кого: 
				<input class='input' type=text size=".$size_naim." value='Склад системных администраторов'>
			</div>";			
			echo "<div class='hr3 position__absl'>&nbsp;</div>";
			
			echo "<div class='komu2 position__absl'>Кому: 
				<input class='input' type=text size=".$size_naim." value='Склад системных администраторов'>
			</div>";
			echo "<div class='hr2 position__absl'>&nbsp;</div>";
			echo "<div class='otkogo2 position__absl'> От кого: 
				<input class='input' type=text size=".$size_naim." value='Пеликан'>
			</div>";			
			echo "<div class='hr4 position__absl'>&nbsp;</div>";
			
			echo  "<div class='logo position__absl'><img src='./img/logo_akt.png' width='45px'></div>";
			
			echo  "<div class='logo2 position__absl'><img src='./img/logo_akt.png' width='45px'></div>";
			
///////////////////////////////////////////////////////
			echo "<div class='table'>";
			echo "<table>";
			
			echo "<thead><tr>";
			echo "<th width='2%'>№</th>";
			echo "<th width=".$size_naim.">Наименование</th>";
			echo "<th width=".$size_kol.">Кол-во</th>";
			echo "</tr></thead>";
			echo "<tbody><tr>";
			
			for ($i=1;$i<$y;$i++){
				echo "<tr>";
				echo "<td><center>".$i."</center></td>";
				echo "<td><input type='text' class='input_table'></td>";
				echo "<td><input type='text' class='input_table2'></td>";
				echo "</tr>";
			}
			echo "</tr></tbody>";
			echo "</table>";
			echo "</div>";
			
			echo "<div class='sdal position__absl'>Сдал: 
					<input class='input' type=text size=".$size_naim2." value='".$username."'>
				</div>";
			echo "<div class='hr5 position__absl'>&nbsp;</div>";
			echo "<div class='prinal position__absl'>Принял: 
					<input class='input' type=text size=".$size_naim2." value=''>
				</div>";
			echo "<div class='hr6 position__absl'>&nbsp;</div>";
			
////////////////////////////////////////////////////				
			echo "<div class='table2'>";
			echo "<table>";
		
			echo "<thead><tr>";
			echo "<th width='2%'>№</th>";
			echo "<th width=".$size_naim.">Наименование</th>";
			echo "<th width=".$size_kol.">Кол-во</th>";
			echo "</tr></thead>";
			echo "<tbody><tr>";
			
			for ($i=1;$i<$y;$i++){
				echo "<tr>";
				echo "<td><center>".$i."</center></td>";
				echo "<td><input type='text' class='input_table'></td>";
				echo "<td><input type='text' class='input_table2'></td>";
				echo "</tr>";
			}
			echo "</tr></tbody>";
			echo "</table>";
			echo "</div>";
			
			echo "<div class='sdal2 position__absl'>Сдал: 
					<input class='input' type=text size=".$size_naim2." value=''>
				</div>";
			echo "<div class='hr7 position__absl'>&nbsp;</div>";
			echo "<div class='prinal2 position__absl'>Принял:
					<input class='input' type=text size=".$size_naim2." value='".$username."'>
				</div>";
			echo "<div class='hr8 position__absl'>&nbsp;</div>";
			
			echo  "<div class='print no-print'><a href='#print-this-document' onclick='print(); return false;'><img src='/img/printer-ico.gif'></a></div>";
			
/////////////////////   СТАРТОВАЯ СТРАНИЦА   ///////////////////////////////////////////////////  		
		/*else {
			echo '<br><br><br><br>';
			echo "<center><a href='waybill.php?e=1' class='button1' align='center'>1 половинка</a> <a href='waybill.php?e=2' class='button1' align='center'>2 половинки</a><center>";
			//echo "<input class='button1' type='button' onclick=javascript:window.location='waybill.php?e=1' value='1 половинка'/><input class='button1' type='button' onclick=javascript:window.location='addcard.php?e=2' value='2 половинки'/>";
		}*/
	}
}		