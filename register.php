<?php
include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php"); /* MENU */

if(isset($_POST['submit'])) {
    $err = array();
    if(!preg_match("/^[a-zA-Z0-9.]+$/", $_POST['login'])) {
        $err[] = "Логин может состоять только из букв английского алфавита и цифр";
    }
    if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30) {
        $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
    }
    $query = mysql_query("SELECT COUNT(id) FROM domain_user WHERE username='".mysql_real_escape_string($_POST['login'])."'",$dbcnx);
    if(mysql_result($query, 0) > 0) {
        $err[] = "Пользователь с таким логином уже существует в базе данных";
    }
    if(count($err) == 0) { 
        $login = $_POST['login'];
        # Убераем лишние пробелы и делаем двойное шифрование
        $password = md5(md5(trim($_POST['password'])));
        mysql_query("INSERT INTO domain_user SET username='".$login."', password='".$password."'",$dbcnx);
        header("Location: index.php"); exit();
    } else {
        print "<b>При регистрации произошли следующие ошибки:</b><br>";
        foreach($err AS $error) {
            print $error."<br>";
        }
    }
}

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['access'] > 3) {
		echo "<div class='login' style='width: 250px;height: 250px;position: absolute;top: 50%;left: 50%;margin: -125px 0 0 -125px;'>";
		echo "<form method='POST'>";
		echo "<p>Логин: <br>";
		echo "<input type='text' name='login' size='40'></p>";
		echo "<p>Пароль: <br>";
		echo "<input type='password' name='password' size='40'></p>";
		echo "<br><p><center><input type='submit' name='submit' value='Зарегистрироваться'></center></p>";
		echo "</form></div>";
	}
}
		
include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ ?>