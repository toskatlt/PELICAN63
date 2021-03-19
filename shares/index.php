<?php
echo "<head><title>Редактирование данных пользователей</title></head>";

include ($_SERVER["DOCUMENT_ROOT"]."section/header_bt.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."section/menu_bt.php"); /* MENU */

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if (isset($userdata)) {
		echo "<center>";
		echo "<b>ТЕКУЩИЕ АКЦИИ [=sum=]</b><br><br>";
		echo "<b>БУДУЩИЕ АКЦИИ [=sum=]</b><br><br>";
		echo "<b>ЗАКОНЧЕННЫЕ АКЦИИ [=sum=]</b><br><br>";


		echo "</center>";
	}
}

 /* FOOTER */ include ($_SERVER["DOCUMENT_ROOT"]."section/footer.php");

