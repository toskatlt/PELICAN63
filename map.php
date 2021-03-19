<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");

if (isset($_GET['id'])) {
	$idGetObject = $_GET['id'];
	$selectObject = selectObject ($dbcnx, $idGetObject);
	
	if (!empty($selectObject['gcod'])) {
		$string = $selectObject['gcod'];
		$string = ereg_replace(" ", "+", $string);
	}
	else {
		$string = $selectObject['area']."+".$selectObject['address'];
		$string = ereg_replace(" ", "+", $string);

		switch ($selectObject['area']) {
			case "TLT":
				$russian = "Тольятти";
				break;
			case "SMR_OBL":
				$russian = "Самарская область";
				break;
			case "SMR":
				$russian = "Самара";
				break;
			case "KNL":
				$russian = "Кинель";
				break;
			case "SZN":
				$russian = "Сызрань";
				break;	
			case "ZHG":
				$russian = "Жигулевск";
				break;
		}
		
		$string = str_replace($selectObject['area'], $russian, $string); 
	}
	header("Location: https://maps.google.ru/maps?q="."$string");
}
?>                                     