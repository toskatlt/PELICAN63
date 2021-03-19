<?php

include $_SERVER["DOCUMENT_ROOT"]."/config.php";
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_object.php");

?>

<script>
var formblock;
var forminputs;
	
function prepare() {
	formblock = document.getElementById('example_group2');
	//console.log(formblock);
	forminputs = formblock.getElementsByTagName('input'); 
	//console.log(forminputs);
}

if (window.addEventListener) {
	window.addEventListener("load", prepare, false);
} else if (window.attachEvent) {
	window.attachEvent("onload", prepare)
} else if (document.getElementById) {
	window.onload = prepare;
}
</script>

<?php

if (isset($_POST['change'])) {
	if (($_POST['change'] != 'all') and ($_POST['change'] != 'PEL') and ($_POST['change'] != 'office')) {
		$result = allObjectWithAreaEmail ($dbcnx, $_POST['change']);
	} elseif ($_POST['change'] == 'PEL') {
		$result = selectObjectWithPelicafe ($dbcnx);
	} elseif ($_POST['change'] == 'office') {
		$result = allGroup ($dbcnx);
	} else {
		$result = allObjectEmail ($dbcnx);
	}
}

echo "<div class='table2'>";
echo "<form name='card' method='POST' id='example_group2'>";
	echo "<table class='tablesorter' cellspacing='1'><thead>";
	echo "<tr>";

	if ($_POST['change'] == 'office') {
		echo "<th width='3%'>№</th>";
		echo "<th width='20%'>ФИО</th>";
		echo "<th width='37%'>Отдел / Должность</th>";
		echo "<th width='17%'>Телефон</th>";
		echo "<th width='13%'>E-MAIL</th>";
		echo "<th width='10%'>";
		echo "<input type='button' onclick=\"example_all()\" value='Отметить всех' style='width:120'/><br>
		<input type='button' onclick=\"example_noone()\" value='Сбросить все' style='width:120'/><br>
		<input type='submit' value='Отправить' style='width:120'><br>
		</th>";
		echo "</tr></thead>";

		$y=1;
		foreach ($result as $row) {
			echo "<tr><td colspan='6' style='color: #fff;text-shadow: 1px 1px 1px #133c4e;background: -webkit-gradient( linear, left bottom, left top, color-stop(0.02, #1a9bd6), color-stop(0.51, #3ea7d9), color-stop(0.87, #1a9bd6) );background: -moz-linear-gradient( center bottom, rgb(123,192,67) 2%, rgb(139,198,66) 51%, rgb(158,217,41) 87% );-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;-moz-border-radius: 5px 5px 0px 0px;border-top-left-radius: 5px;border-top-right-radius: 5px;'>".$row['name'].""; 
			//echo "<a href='#' onClick=\"select_group('area".$row['id']."', '1');\">Выбрать отдел</a>";
			echo "</td></tr>";
			$allOfficeUserEmailSelectGroup = allOfficeUserEmailSelectGroup ($dbcnx, $row['name']);
			if (!empty($allOfficeUserEmailSelectGroup)) {
				foreach ($allOfficeUserEmailSelectGroup as $a) {
					echo "<tr><td width='20px'>".$y++."</td>";
					echo "<td align=left width='130' title='".$a['id']."'>".$a['fio']."</td>";
					echo "<td align=left width='180' title='".$a['id_position']."'>".$a['position']."</td>";
					$a['phone'] = str_replace(" ","",$a['phone']);
					$phones = array();
					if (strlen($a['phone']) == '6') { $phones[] = preg_replace("/(\d{2})(\d{2})(\d{2})/","\\1-\\2-\\3",$a['phone']); }
					elseif (strlen($a['phone']) == '10') { $phones[] = preg_replace("/(\d{2})(\d{2})(\d{2}),(\d{3})/","\\1-\\2-\\3 д.\\4",$a['phone']); }
					elseif (strlen($a['phone']) == '12') { $phones[] = preg_replace("/\((\d{3})\)(\d{7})/","8(\\1)\\2",$a['phone']); }
					if (count($phones) > 0) {
						$implode = implode(", ", $phones);
					} else { $implode = ''; }
					$phones = null;
					echo "<td align=left width='180'>".$implode."</td>";						
					echo "<td align=left width='160'><a href='mailto:".$a['email']."'>".$a['email']."</a></td> ";
					echo "<td width='180'><input type='checkbox' name='checkbox[]' value=".$a['email'].">";	
					echo "</td></tr>";	
				}
			}
		}
	} else {
		?>
				<th width='3%'>№</th>
				<th width='20%'>Имя</th>
				<th width='37%'>Адрес</th>
				<th width='17%'>Телефон</th>
				<th width='13%'>E-MAIL</th>
				<th width='10%'>
					<input type='button' onclick='example_all()' value='Отметить всех' class='email_button'>
					<input type='button' onclick='example_noone()' value='Сбросить все' class='email_button'>
					<input type='submit' value='Отправить' class='email_button'>
				</th>
			</tr>
		</thead>

		<?php
		$y=1;
		foreach ($result as $row) {
			if ($row['email'] != '') {	
				echo "<tr><td width='20px'>".$y++."</td>";
				if (isset($row['telephone'])) { echo "<td align=left width='130' title='".$row['telephone']."' >"; }
				else { echo "<td align=left width='130'>"; }	
				if ((isset($userdata)) and ($userdata['id_position'] == "1")) {
					echo "<a href='card.php?id=".$row['id']."' title='".$row['address']."'>".$row['name']."</a>";
				} else {
					echo $row['name_south'];
				}
				echo "</td>";
				echo "<td align=left width='180'><a href='/map.php?id=".$row['id']."' title='".$row['name']."' title='".$row['name']."' target='_blank'>".$row['address']."</a></td>";
				$phoneInObject = phoneInObject ($dbcnx, $row['id']);
				foreach ($phoneInObject as $phone) {
					$phone['number'] = str_replace(" ","",$phone['number']);
					$phones = array();
					if (strlen($phone['number']) == '6') { $phones[] = preg_replace("/(\d{2})(\d{2})(\d{2})/","\\1-\\2-\\3",$phone['number']); }
					elseif (strlen($phone['number']) == '10') { $phones[] = preg_replace("/(\d{2})(\d{2})(\d{2}),(\d{3})/","\\1-\\2-\\3 д.\\4",$phone['number']); }
					elseif (strlen($phone['number']) == '12') { $phones[] = preg_replace("/\((\d{3})\)(\d{7})/","8(\\1)\\2",$phone['number']); }
				}
				if (count($phones) > 0) {
					$implode = implode(", ", $phones);
				} else { $implode = ''; }
				$phones = null;
				echo "<td align=left width='180'>".$implode."</td>";						
				echo "<td align=left width='160'><a href='mailto:".$row['email']."'>".$row['email']."</a></td> ";
				echo "<td width='180'><input type='checkbox' name='checkbox[]' value=".$row['email'].">";	
				echo "</td></tr>";
			}
		}
	}
	echo "</table>";
echo "</form>";
echo "</div>";