<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_object.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_domain_user.php");

$search = '';

//$userdata = authorization ($dbcnx, $_COOKIE['id']);

echo "<div class='table2'>";
echo "<table id='myTable' class='tablesorter'>";
echo "<thead>";
echo "<tr>";
echo "<th style='width: 2%;' class='header'>№</th>";
echo "<th style='width: 20%;' class='header'>ФИО</th>";
echo "<th style='width: 10%;' class='header'>username</th>";
echo "<th style='width: 8%;' class='header'>телефон</th>";
echo "<th style='width: 12%;' class='header'>email</th>";
echo "<th style='width: 12%;' class='header'>офис</th>";
echo "<th style='width: 15%;' class='header'>отдел</th>";
echo "<th style='width: 25%;' class='header'>должность</th>";
echo "<th style='width: 5%;' class='header'>int</th>";
echo "</tr>";
echo "</thead>";

/////////////////////////////////////////////////////////////////////////////			
/////////////////////////////////////////////////////////////////////////////		
			
echo "<tbody>";	
$i=1;	

//$_POST['change']= 'all';

//var_dump($_GET);

if (isset($_POST['searchUser'])) { // ЕСЛИ ЗАДАН ПОИСК ПО ИМЕНИ ПОЛЬЗОВАТЕЛЯ
	$a = findUserByName ($dbcnx, $_POST['searchUser']);
	if(count($a) > 0) {	
		$allUser = $a;
	} else {
		$switcher = switcher ($_POST['searchUser']);
		$a = findUserByName ($dbcnx, $switcher);
		if(count($a) > 0) {	
			$allUser = $a;
		} else echo '<center>По вашему запросу ничего не найдено</center>';
	}
}

elseif (isset($_POST['change'])) {
	if ($_POST['change'] != 'all') {
		if ($_POST['change'] == 'cls') {
			$allUser = allObjectClose ($dbcnx);
		} elseif ($_POST['change'] == 'nob') {
			$allUser = allObjectNoBuilding ($dbcnx);
		} elseif ($_POST['change'] == 'shop') {
			$allUser = allUserSelectObject ($dbcnx, 'ПЕЛИКАН');
		} elseif ($_POST['change'] == 'stuff') {
			$allUser = allUserSelectObject ($dbcnx, 'Офис');
		} else {	
			$allUser = allObjectWithArea ($dbcnx, $_POST['change']);
		}	
	} else {
		$allUser = allUser($dbcnx);
	}
}

foreach ($allUser as $aU) {	
	$selectObjectToUser = selectObjectToUser ($dbcnx, $aU['id']);
	echo "<tr>";
	if ($selectObjectToUser[0]['type'] == 'ПЕЛИКАН') {
		echo "<td style='background-color: #e6bd72;'>";
	} else { echo "<td>"; }
	echo $i++."</td>";
	
	echo "<td align=left>";
	if ((isset($aU['run'])) and ($aU['run'] == '0')) {
		echo "<a href='editpers?id=".$aU['id']."' style='color: red;'>";
	} else {
		echo "<a href='editpers?id=".$aU['id']."'>";
	}
	echo "".$aU['fio']."</a></td>";
	
	echo "<td><a href='editpers?id=".$aU['id']."'>".$aU['username']."</a></td>";
	
////////// ТЕЛЕФОН ///////////////////////////////////////////////////////
	echo "<td align=left>";
		if ($selectObjectToUser[0]['type'] == 'ПЕЛИКАН') {
			$phoneInObject = phoneInObject ($dbcnx, $selectObjectToUser[0]['id']);
			if (!empty($$phoneInObject[0]['number'])) {
				echo $phoneInObject[0]['number'];
			}
		} else {
			echo $aU['phone'];
		}	
	echo "</td>";
////////// EMAIL /////////////////////////////////////////////////////////		
	$selectEmailToUser = selectEmailToUser ($dbcnx, $aU['id']);
	echo "<td align=left>";
		if (!empty($selectEmailToUser)) {
			echo $selectEmailToUser;	
		} elseif ($selectObjectToUser[0]['type'] == 'ПЕЛИКАН') {
			$emailInObject = emailInObject ($dbcnx, $selectObjectToUser[0]['id']);
			echo $emailInObject[0]['email'];
		}
	echo "</td>";

////////// ОФИС /////////////////////////////////////////////////////////		

	echo "<td align=left>";
		if (!empty($selectObjectToUser[0]['name'])) {
			echo $selectObjectToUser[0]['name'];	
		}	
	echo "</td>";
		
////////// ОТДЕЛ ///////////////////////////////////////////////////////	
	$selectPositionToUser = selectPositionToUser ($dbcnx, $aU['id']);
	echo "<td align=left>";
		if (!empty($selectPositionToUser)) {
			echo $selectPositionToUser[0]['name'];	
		}	
		if ($selectObjectToUser[0]['type'] == 'ПЕЛИКАН') {
			echo "Магазин Пеликан";
		}	
	echo "</td>";
	
////////// ДОЛЖНОСТЬ ///////////////////////////////////////////////////		
	echo "<td align=left>";
		if (!empty($selectPositionToUser)) {
			echo $selectPositionToUser[0]['position'];	
		} else {
			
			$positiontypes = [
				"Директор" => ["uprav"],
				"Оператор" => ["oper","oper2","oper3"],
				"Товаровед" => ["manager", "tov", "manager2", "manager1", "tovar"],
				"Грузчик" => ["tsd", "tsd2", "tsd1"],
				"Производство" => ["pro", "pro2"]
				];
			
			$position = explode("_", $aU['username']);
			
			foreach($positiontypes as $postype=>$postypes) {
				if( in_array($position[count($position)-1], $postypes) ) {
					echo $postype;
					break;
				}
			}
		}	
	echo "</td>";
	
////////// ИНТЕРНЕТ ///////////////////////////////////////////////////	
	echo "<td align=left>";
		$selectSquadUser = selectSquadUser ($dbcnx05, $aU['username']);
		if (!empty($selectSquadUser)) {
			echo "<img src='img/internet.png' style='width:20px;'>";	
		}	
	echo "</td>";
	
	echo "</tr>";
}					
echo "</tbody>";	
echo "</table>";
echo "</div>";

?>
<script>
$(document).ready(function() { 
    $("#myTable").tablesorter({sortList: [[0,0],[2,0]]}); 
} );
</script>
