<?php
include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php"); /* MENU */

if (isset($_POST['enter'])) {
    if($_POST['name'] != "" and $_POST['pass'] != "") {
		
		$name = $_POST['name'];
		$pass = $_POST['pass'];
		
        $query = mysql_query("SELECT id, password FROM domain_user WHERE username='".$name."'", $dbcnx) or die(mysql_error());
		$data = mysql_fetch_assoc($query);
		
		# Сравниваем пароли
		if($data['password'] == md5(md5($pass))) {
			$hash = md5(generateCode(10));           
			# Записываем в БД новый хеш авторизации и IP
			mysql_query("UPDATE domain_user SET hash='".$hash."' WHERE id='".$data['id']."'", $dbcnx);	
			setcookie("id", $data['id'], time()+3600*24*31*24);
			setcookie("hash", $hash, time()+3600*24*31*24);
			header("Location: index.php"); exit();
		}
		else {
			print "<div class='login'>Вы ввели неправильный логин/пароль</div>";
		}
		
	}
}
else {
	echo "<div class='login'>";
		echo "<div class='block'>";
			echo "<form  method='POST'>";
			
				echo "<span class='input input--kaede'>";
					echo "<input class='input__field input__field--kaede' type='text' name='name' id='input-1'/>";
					echo "<label class='input__label input__label--kaede' for='input-1'>";
						echo "<span class='input__label-content input__label-content--kaede'>Login</span>";
					echo "</label>";
				echo "</span>";

				echo "<span class='input input--kaede'>";
					echo "<input class='input__field input__field--kaede' type='password' name='pass' id='input-2'/>";
					echo "<label class='input__label input__label--kaede' for='input-2'>";
						echo "<span class='input__label-content input__label-content--kaede'>Password</span>";
					echo "</label>";
				echo "</span>";
				
				echo "<center><input type='submit' name='enter' value='Подтвердить' class='button'></center>";
				
			echo "</form>";
		echo "</div>";
	echo "</div>";
}

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */  ?>