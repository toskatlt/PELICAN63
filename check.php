<?php
include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php"); /* MENU */

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
    $userdata = authorization_lite ($dbcnx, $_COOKIE['id']);
    if (($userdata['hash'] !== $_COOKIE['hash']) or ($userdata['id'] !== $_COOKIE['id'])) {
		/*
		echo $_COOKIE['id'] ." - COOKIE['id'] порядковый номер пользователя в cookie</br>";
		echo $userdata['id'] ." - userdata['id']</br></br>";
		echo $_COOKIE['hash'] ." - COOKIE['hash']</br>";
		echo $userdata['hash'] ." - userdata['hash']</br></br>";
		echo $userdata['ip'] ." - userdata['ip']</br>";
		echo $_SERVER['REMOTE_ADDR'] ." - SERVER['REMOTE_ADDR']</br>";
		*/
		echo "</div>";
    } else {
		echo "<div class='inlogin' style='width: 500px;'>";
        echo "Добрый день, ".$userdata['fio']."<br><br>";
		if  ($userdata['id_group'] == "1") {
			echo "        <li><a href='ip_pelican.php'>IP PELICAN</a></li><br>";
			echo "        <li><a href='jabber.php'>Jabber</a></li><br>";
			echo "        <li><a href='http://192.168.0.2:9090/'>OpenFire</a></li><br>";
			echo "        <li><a href='http://forum.neo63.ru/'>Форум</a></li><br>";
			echo "        <li><a href='http://mail.neo63.ru/''>Почта Неотрейд</a></li><br>";
			echo "        <li><a href='duty_admin.php'>Дежурство</a></li><br>";
			echo "        <li><a href='zapravka.php'>Картриджи</a></li><br>";
			echo "        <li><a href='rsa.php'>RSA</a></li><br>";
			echo "        <li><a href='terminals.php'>Терминалы</a></li><br>";
		}
		elseif ($userdata['position'] == "GUEST") {
			echo "<li><a href='radio.php'>Радио</a></li><br>";
			echo "<li><a href='email.php'>Почтовые адреса</a></li><br>";
			echo "<li><a href='neocam.php'>Камера на парковку</a></li><br>";
		}
		echo "</div>";
    }
}

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */  ?>