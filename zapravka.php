<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

$date = date("Y-m-d");

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) { 
	$userdata = authorization ($dbcnx, $_COOKIE['id']);	
	if($userdata['id_group'] == "1") {
		if (isset($_POST['submit'])) {
			$date_time = $_POST['date'];
			foreach($_POST as $key => $value) {
				if (($value != 'Сохранить') and ($value > 0) and ($key != 'date')) {
					$insrt[] = " ('".$userdata['id']."', '".$key."', '".$value."', '".$date_time."')";
				}
				$insert = implode(", ",$insrt);
			}
			mysql_query("INSERT INTO `zapravka`(`id_user`, `type`, `count`, `date`) VALUES ".$insert, $dbcnx);
			unset($_POST);
		}
		$select_month = select_month ($dbcnx);
		$select_minus1 = select_minus1 ($dbcnx);
		$select_minus2 = select_minus2 ($dbcnx);
		
		echo "<div class='container zapravka_grid'>";
			echo "<div class='zapravka_block item1'>";
				echo "<form method='POST' action='zapravka.php'>";
				echo "<table>";
				echo "<tr><td colspan='2' align='center'><b>Дата</b> <input id='meeting' name='date' class='zapravka_date' type='date' value='".$date."'/></td></tr>";
				$allCartridge = allCartridge ($dbcnx);
					foreach ($allCartridge as $c) {
						echo "<tr><td>".$c['name'].": </td><td><input type='text' name='".$c['id']."' pattern='[0-9]{1,2}' min='1' max='99'></td></tr>";
					}
				echo "<tr><td colspan='2' align='center'><input type='submit' class='zapravka_button' name='submit' value='Сохранить'></td></tr>";
				echo "</table>";
				echo "</center>";
				echo "</form>";
			echo "</div>";
			echo "<div class='zapravka_block'>";
			$s=0; foreach ($select_month as $sum) { $s = $s + $sum['sum(zapravka.count)']; }
				echo "<b>В этом месяце [ ".$s." ]: </b><br>";	
				foreach ($select_month as $sm) {
					echo $sm['name']." - ".$sm['sum(zapravka.count)']." шт<br>";
				}
			echo "</div>";
			echo "<div class='zapravka_block'>";
				$s=0; foreach ($select_minus1 as $sum) { $s = $s + $sum['sum(zapravka.count)']; }
				echo "<b>В прошлом месяце [ ".$s." ]: </b><br>";
				foreach ($select_minus1 as $sm) {
					echo $sm['name']." - ".$sm['sum(zapravka.count)']." шт<br>";
				}
			echo "</div>";
			echo "<div class='zapravka_block'>";
				$s=0; foreach ($select_minus2 as $sum) { $s = $s + $sum['sum(zapravka.count)']; }
				echo "<b>Два месяца назад [ ".$s." ]: </b><br>";
				foreach ($select_minus2 as $sm) {
					echo $sm['name']." - ".$sm['sum(zapravka.count)']." шт<br>";
				}
			echo "</div>";
		echo "</div>";
	}
}
include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ 