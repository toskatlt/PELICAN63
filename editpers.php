<?php
echo "<head><title>Редактирование данных пользователей</title></head>";

/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_avto.php");	
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_domain_user.php");
?>
<script>
function selected2(a) {
	var id_group = a.value;
	$.ajax({			
		type: "POST",			
		url: "select/position.php",
		data: {
			'group2': id_group
		},
		success: function(html){  
			$("#position").html(html);  
		}
	});
	$.ajax({			
		type: "POST",			
		url: "select/domain_user_position.php",
		data: {
			'id_group': id_group
		},
		success: function(html){  
			$("#domain_user_position").html(html);  
		}
	});	
}

window.onload = function() {
   selected3();
};
var selected3 = function selected3(id_domain_user) {
	//var id_group3 = a.value;
	var id_group3 = document.getElementById("group").value
	var id_domain_user = document.getElementById("id_domain_user").value;
	$.ajax({			
		type: "POST",			
		url: "select/position.php",
		data: {
			'id_domain_user' : id_domain_user,
			'group3': id_group3
		},
		success: function(html){  
			$("#position").html(html);  
		}
	});
}

function positionEditPersonal() {
	var id_domain_user = document.getElementById("id_domain_user").value;
	var id_position = document.getElementById("positionEditPersonal").value;
	$.ajax({			
		type: "POST",			
		url: "select/position.php",
		data: {
			'select_4' : '0',
			'id_domain_user' : id_domain_user,
			'id_position': id_position
		}
	});
	document.getElementById('img').style='display: inline-block;width: 11px;';
}

var myAction = function(id_domain_user){
		var text_fio = document.getElementById("text_fio").value;
		var id_domain_user = document.getElementById("id_domain_user").value;
	$.ajax({
		method: "POST",
		url: "eupd/edit_du.php",
		dataType: "json",
		data: {
			'id_domain_user' : id_domain_user,
			'text_fio': text_fio
		}
	})
};
$(function fio(){
    $("#text_fio").on("click", ()=>{
        $("#text_fio").attr("contenteditable", "true").focus();
    });
	$("#text_fio").on("blur", ()=> { 
			myAction();
	}).keydown(function(){
		if(event.keyCode == 13) {
			myAction();
		}
	});	
});
//////////////////////////////////////////////////////////////////////////////
var myPhone = function(){
		var phone = document.getElementById("phone").value;
		var id_domain_user = document.getElementById("id_domain_user").value;
	$.ajax({
		method: "POST",
		url: "eupd/edit_du.php",
		dataType: "json",
		data: {
			'id_domain_user' : id_domain_user,
			'phone': phone
		}
	})
};
$(function phone(){
    $("#phone").on("click", ()=>{
        $("#phone").attr("contenteditable", "true").focus();
    });	
	$("#phone").on("blur", ()=> { 
			myPhone();
	}).keydown(function(){
		if(event.keyCode == 13) {
			myPhone();
		}
	});	
});
//////////////////////////////////////////////////////////////////////////////
var myEmail = function(){
		var email = document.getElementById("email").value;
		var id_domain_user = document.getElementById("id_domain_user").value;
	$.ajax({
		method: "POST",
		url: "eupd/edit_du.php",
		dataType: "json",
		data: {
			'id_domain_user' : id_domain_user,
			'email': email
		}
	})
};
$(function email(){
    $("#email").on("click", ()=>{
        $("#email").attr("contenteditable", "true").focus();
    });
	$("#email").on("blur", ()=> { 
			myEmail();
	}).keydown(function(){
		if(event.keyCode == 13) {
			myEmail();
		}
	});	
});
//////////////////////////////////////////////////////////////////////////////
var myJabber = function(){
		var jabber = document.getElementById("jabber").value;
		var id_domain_user = document.getElementById("id_domain_user").value;
	$.ajax({
		method: "POST",
		url: "eupd/edit_du.php",
		dataType: "json",
		data: {
			'id_domain_user' : id_domain_user,
			'jabber': jabber
		}
	})
};
$(function jabber(){
    $("#jabber").on("click", ()=>{
        $("#jabber").attr("contenteditable", "true").focus();
    });
	$("#jabber").on("blur", ()=> { 
			myJabber();
	}).keydown(function(){
		if(event.keyCode == 13) {
			myJabber();
		}
	});	
});

</script>
<style>
td.id {
	font-weight:bolt;
	font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;
	font-size: 14px;
	color: #666;
	text-align:left;
}	
</style>

<?php			

//include "test/vendor/autoload.php";

if (isset($_GET['e'])) $etype = $_GET['e'];
if (isset($_GET['id'])) $id_domain_user = $_GET['id'];
//if (isset($_GET['id'])) $id_object = $_GET['id'];
			
if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);

	echo "<div class='card_body'>";
	echo "<div class='card'>";

	
	if ((isset($userdata)) and ($userdata['access'] > 1)) {
	//////   3   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// СМЕНА ЛИЧНЫХ ДАННЫХ ПОЛЬЗОВАТЕЛЯ ПО АВТОМОБИЛЮ ДЛЯ ОТЧЕТОВ ПО ЗАПРАВКЕ 	
		if ($etype == 3) {
			if (isset($_POST['enter3'])) {
				mysql_query("INSERT INTO `avto`(`id_domain_user`, `model`, `number`, `d_license`, `fuel_limit`, `fuel_mark`, `fuel_consumption`, `tabel_number`, `mileage`, `tank`) VALUES ('".$userdata['id']."', '".$_POST['model']."', '".$_POST['number']."', '".$_POST['d_license']."', '".$_POST['fuel_limit']."', '".$_POST['fuel_mark']."', '".$_POST['fuel_consumption']."', '".$_POST['tabel_number']."', '".$_POST['mileage']."', '".$_POST['tank']."') ON DUPLICATE KEY UPDATE `model`='".$_POST['model']."', `number`='".$_POST['number']."', `d_license`='".$_POST['d_license']."', `fuel_limit`='".$_POST['fuel_limit']."', `fuel_mark`='".$_POST['fuel_mark']."', `fuel_consumption`='".$_POST['fuel_consumption']."', `tabel_number`='".$_POST['tabel_number']."', `mileage`='".$_POST['mileage']."', `tank`='".$_POST['tank']."'", $dbcnx);
			} 
			echo "<center><b>СМЕНА ДАННЫХ АВТОМОБИЛЯ</b></center><br><br>";
			echo "<form method='POST' action='editpers?e=".$etype."'><table>";
				$selectAvto = selectAvto ($dbcnx, $userdata['id']);
				if (empty($selectAvto)) { mysql_query("INSERT INTO `avto` (`id_domain_user`) VALUES ('".$userdata['id']."')", $dbcnx); }
				echo "<input name='id' type='hidden' size='2' value='".$selectAvto['id']."'>";
			echo "<tr><td>Модель: </td><td><input name='model' type='text' size='20' value='".$selectAvto['model']."'></td></tr>";
			echo "<tr><td>Гос.номер: </td><td><input name='number' type='text' size='20' value='".$selectAvto['number']."'></td></tr>";
			echo "<tr><td>Номер водит.удост: </td><td><input name='d_license' type='text' size='20' value='".$selectAvto['d_license']."'></td></tr>";
			echo "<tr><td>Размер бака авто: </td><td><input name='fuel_limit' type='text' size='20' value='".$selectAvto['fuel_limit']."'></td></tr>";
			echo "<tr><td>Модель бензина: </td><td><input name='fuel_mark' type='text' size='20' value='".$selectAvto['fuel_mark']."'></td></tr>";
			echo "<tr><td>Расход топлива: </td><td><input name='fuel_consumption' type='text' size='20' value='".$selectAvto['fuel_consumption']."'></td></tr>";
			echo "<tr><td>Табельный номер: </td><td><input name='tabel_number' type='text' size='20' value='".$selectAvto['tabel_number']."'></td></tr>";
			echo "<tr><td>Пробег авто: </td><td><input name='mileage' type='text' size='20' value='".$selectAvto['mileage']."'></td></tr>";
			echo "<tr><td>В баке: </td><td><input name='tank' type='text' size='20' value='".$selectAvto['tank']."'></td></tr>";
			echo "<tr><td></td><td></td></tr>";
			echo "<tr><td colspan='2'><br><center><input class='zapravka_button' type='submit' name='enter3' value='Сменить'><input class='zapravka_button' type='button' onclick=javascript:window.location='personal.php' value='&#8592; Назад'/></center></tr></td>";
			echo "</table></form>";
			echo "</div></div>";
		}
	}
	if ((isset($userdata)) and ($userdata['access'] > 5)) {
		
		# Добавление нового пользователя!
		if (isset($_POST['add_domain_user'])) {
			$user = $_POST['login']; // ЛОГИН ПОЛЬЗОВАТЕЛЯ
			$jabber = $_POST['jabber']; // ГРУППА В JABBER
            $name = $_POST['name'];

			$username = $user."@neo63.ru";			
			$selectUser = selectUser ($dbcnx_pf, $username);
			
			echo "<br><center>";
			if (!isset($selectUser[0]['username'])) {
				echo "<b> !!! Пользователь с таким логином уже существует в базе POSTFIXADMIN.MAILBOX !!! </b><br><br>";
			}
			
			$maildir = "neo63.ru/".$user."/";
			if (!empty($_POST['pass'])) {
				$userpass = $_POST['pass'];
			} else {	
				$userpass = $user."3000";
			}
			
			# ПОИСК ВОЗМОЖНЫХ ОШИБОК
			$err = array();
			if(!preg_match("/^[a-zA-Z0-9._]+$/", $_POST['login'])) {
				$err[] = "Логин может состоять только из букв английского алфавита и цифр";
			}
			if(strlen($_POST['login']) < 2 or strlen($_POST['login']) > 40) {
				$err[] = "Логин должен быть не меньше 2-х символов в длину и не больше 40";
			}
			$query = mysql_query("SELECT COUNT(id) FROM domain_user WHERE username='".mysql_real_escape_string($user)."'", $dbcnx);
			if(mysql_result($query, 0) > 0) {
				$err[] = "Пользователь с таким логином уже существует в базе PELICAN.DOMAIN_USER";
			}	
			
			# Если ошибок нет
			if(count($err) == 0) { 
			
				if (!isset($selectUser[0]['username'])) {
					$pass = md5($userpass);
					# Добавление пользователя на почтовый сервер
					mysql_query("INSERT INTO `mailbox`(`username`, `name`, `password`, `maildir`, `quota`, `local_part`, `domain`, `created`, `modified`, `active`) VALUES ('".$username."', '".$name."', ''".$pass."', '".$maildir."', 0, '".$user."', 'neo63.ru', '".$date_time."', '".$date_time."', 1)", $dbcnx_pf);
				}
				
				
				# Добавление пользователя на джабер сервер
				if ($jabber != '') { mysql_query("INSERT INTO `ofGroupUser`(`groupName`, `username`, `administrator`) VALUES ('".$jabber."', '".$user."', '0')", $dbcnx_j); }
				
				/*
					// Добавление пользователя в Jabber
					$api = new Gidkom\OpenFireRestApi\OpenFireRestApi;

					$api->secret = "qdatmnN7s9TL4rr2";
					$api->host = "10.63.0.2";
					$api->port = "9090";  // default 9090

					$api->useSSL = false;
					$api->plugin = "/plugins/restapi/v1";  // plugin 
					
					$result = $api->addUser($user, '1', $_POST['name'], $username, array('Group 1'));	
				*/
				
				# Убераем лишние пробелы и делаем двойное шифрование
				$password = md5(md5(trim($userpass)));
				# Добавление пользователя в таблицу domain_user
				mysql_query("INSERT INTO `domain_user`(`username`, `password`, `fio`, `phone`, `id_position`, `id_object`, `run`) VALUES ('".$user."', '".$password."', '".$_POST['name']."', '".$_POST['phone']."', '".$_POST['position']."', '".$_POST['office']."','1')", $dbcnx);

				$kundeid = mysql_insert_id($dbcnx);
				# Добавление пользователя в таблицу email
				mysql_query("INSERT INTO `email`(`id_domain_user`, `email`) VALUES ('".$kundeid."', '".$username."')", $dbcnx);
				# Добавление пользователя в таблицу jabber
				mysql_query("INSERT INTO `jabber`(`id_domain_user`, `jabber`) VALUES ('".$kundeid."', '".$user."')", $dbcnx);			
				
				////   LOG   ///////////////////////////////
				$update_log = "ADD_USER: ".$user." ".$name."";
				mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$userdata['id']."', '".$date_time."', 'domain_user', '".$_POST['office']."', '".$update_log."')", $dbcnx);
				
				# Добавление пользователя в таблицу squid, для получения прав доступа к интернету
				if (isset($_POST['internet'])) { $on = 1;  } 
				else { $on = 0; }	
					mysql_query("INSERT INTO `passwd`(`user`, `password`, `enabled`, `fullname`) VALUES ('".$user."','".$userpass."','".$on."','".$_POST['name']."')", $dbcnx05);
				
				//exec("sudo ssh root@10.63.0.107 dcedit add ".$user, $output);
				
				echo "<b> ►►►  ПОЛЬЗОВАТЕЛЬ [ ".$user." ] УСПЕШНО ДОБАВЛЕН ◄◄◄ </b><br>";
				
			} else {
				print "<b>При регистрации произошли следующие ошибки:</b><br>";
				foreach($err AS $error) {
					print $error."<br>";
				}
			}				
			echo "</center><br><hr><br>";
		}	
		
		
/////////// КАРТОЧКА ПОЛЬЗОВАТЕЛЯ //////////////////////////////////////////////////////////////////////////////////////////
		if ((isset($id_domain_user)) and (is_numeric($id_domain_user))) {
			$selectDomainUser = selectDomainUser ($dbcnx, $id_domain_user);
			//var_dump($selectDomainUser);
			echo "<div class='link'><a href='domain_user' title='Доменные пользователи'><img width='20px' src='img/back_arrow.png'></a></div>";
			echo "<center><b>ПОЛЬЗОВАТЕЛЬ ";
			if (!empty($selectDomainUser['fio'])) {
				echo mb_strtoupper($selectDomainUser['fio']);
				echo "<br>";
			}	
			echo "ID: ".$id_domain_user."";
			
			echo " ❮ ";
			echo "<form name='del_domain_user' method='POST' action='domain_user' style='DISPLAY: inline'>";
			echo "<input type='image' src='img/delete.png' style='vertical-align:bottom' title='УДАЛИТЬ УЧЕТНУЮ ЗАПИСЬ ДОМЕННОГО ПОЛЬЗОВАТЕЛЯ' width='20px' onclick=\"return deleteDomainUser();\"/>";
			echo "<input name='del_domain_user_id' type='hidden' size='4' value='".$id_domain_user."'>";					
			echo "</form>";
			echo " ❯ ";
			
			echo "</b></center><br><br>";
			
			// СКРЫТЫЕ ДАННЫЕ ID ДОМЕННОГО ПОЛЬЗОВАТЕЛЯ
			echo "<input type='hidden' id='id_domain_user' class='id_domain_user' value='".$selectDomainUser['id']."'>";
			
			echo "<div class='face_staff'>";
			if (file_exists("img/face_staff/".$selectDomainUser['fio'].".jpg")) { 
				echo "<img src='img/face_staff/".$selectDomainUser['fio'].".jpg' style='width:225px;'>";
			} else {
				echo "<img src='img/face_staff/none.jpg' style='width:225px;'>";	
			}	
			
			echo "</div>";
			
			echo "<table>";
			echo "<tr>";
			echo "<td style='width:195px;' class='id'>";
				echo "Уч.запись сайта: <b>";	
			echo "</td><td class='id'>";
					echo "<input id='username' class='username' style='height:30px; width:280px;' value='".$selectDomainUser['username']."'>"; 
			echo "</b>";
				if ($selectDomainUser['run'] == 0) { echo " | <b style='color: red;'>ОТКЛЮЧЕНА</b>"; }
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td style='width:195px;' class='id'>";
				echo "ФИО [полностью]: ";
			echo "</td><td class='id'>";
				echo "<input id='text_fio' class='text_fio' style='height:30px; width:280px;' value='".$selectDomainUser['fio']."'>";
			echo "</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td style='width:195px;' class='id'>";
				echo "Телефон: ";
			echo "</td><td class='id'>";
				echo "<input id='phone' class='phone' style='height:30px; width:280px;' value='".$selectDomainUser['phone']."'>"; 
			echo "</td>";
			echo "</tr>";
			
			$selectEmailToUser = selectEmailToUser ($dbcnx, $id_domain_user);
			echo "<tr>";
			echo "<td style='width:195px;' class='id'>";
				echo "Эл.почта [surname.n]: ";
			echo "</td><td class='id'>";
				echo "<input id='email' class='email' style='height:30px; width:280px;' value='".$selectEmailToUser."'>"; 	
			echo "</td>";
			echo "</tr>";

				$selectJabberUser[0] = selectJabber ($dbcnx, $id_domain_user);
				
				if ((empty($selectJabberUser[0])) and (!empty($selectEmailToUser))) {
					$jabber = explode("@", $selectEmailToUser);
					$selectJabberUser = selectJabberUser ($dbcnx_j, $jabber[0]);						
				} elseif ((empty($selectJabberUser[0])) and (!empty($selectDomainUser['username']))) {	
					$jabber = $selectDomainUser['username'];
					$selectJabberUser = selectJabberUser ($dbcnx_j, $jabber);					
				} 
				
			echo "<tr>";
			echo "<td style='width:195px;' class='id'>";	
				echo "Jabber: ";
			echo "</td><td class='id'>";
				echo "<input id='jabber' class='jabber' style='height:30px; width:280px;' value='".$selectJabberUser[0]."'>";
			echo "</td>";
			echo "</tr>";
			
			
			$lastIpAddress = lastIpAddress ($dbcnx_j, $selectJabberUser[0]);
			echo "<tr>";
			echo "<td style='width:195px;' class='id'>";			
				echo "IP адрес: ";
			echo "</td><td class='id' size='35' style='height:30px; width:280px;'>";	
				if (!empty($lastIpAddress)) { echo "<b>".$lastIpAddress."</b>"; }
				else { echo "<b>не присвоен</b>"; }
			echo "</td>";
			echo "</tr>";
		
			$selectSquadUser = selectSquadUser ($dbcnx05, $selectDomainUser['user']);
			if (!empty($selectSquadUser)) {
				mysql_query("INSERT INTO `passwd`(`user`, `password`, `enabled`, `fullname`) VALUES ('".$selectDomainUser['username']."', '".$selectDomainUser['username']."3000', '1', '".$selectDomainUser['fio']."')", $dbcnx05);
			}
			echo "<tr>";
			echo "<td style='width:195px;' class='id'>";
				echo "Интернет [squid 0.5]: ";
			echo "</td><td class='id' size='35' style='height:30px; width:280px;'>";
				echo "<b>л: ".$selectDomainUser['username']." п: ".$selectDomainUser['username']."3000</b>";
			echo "</td>";
			echo "</tr>";
			
			if ($userdata['access'] == 10) {
				echo "<tr>";
				echo "<td style='width:195px;' class='id'>";
					echo "Уровень доступа к сайту: "; 
				echo "</td><td class='id'><b>";
					echo "<input id='access' class='access' style='font-weight:bold;height:30px; width:280px;' value='".$selectDomainUser['access']."'>";
				echo "</b></td>";
				echo "</tr>";
			}
			
			echo "</table>";
			echo "<br><br>";
			echo "<table>";
			
			$selectObjectToUser = selectObjectToUser ($dbcnx, $id_domain_user);
			$ObjectOffice = ObjectOffice ($dbcnx);

			echo "<tr>";
			echo "<td style='width:195px;' class='id'>";
				echo "Офис: ";
			echo "</td><td class='id'>";		
				echo "<select size='1' name='office' id='office' size='35' style='height:30px; width:280px;'>";
				if (empty($selectObjectToUser[0]['name'])) { 
					echo "<option value='0' SELECTED></option>"; 
				} else { 
					echo "<option value='0'></option>"; 
				}
				foreach ($ObjectOffice as $sotu) {
					if ($selectObjectToUser[0]['id'] == $sotu['id']) { 
						echo "<option value='".$sotu['id']."' SELECTED>".$sotu['name']." [ ".$sotu['short_area']." ]</option>"; 
					} else { 
						echo "<option value='".$sotu['id']."'>".$sotu['name']." [ ".$sotu['short_area']." ]</option>"; 
					}
				}
				echo "</select>";
			echo "</td>";
			echo "</tr>";	
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			$selectPositionToUser = selectPositionToUser ($dbcnx, $id_domain_user);
			$allGroup = allGroup ($dbcnx);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			///// ОТДЕЛ
			echo "<tr>";
			echo "<td style='width:195px;' class='id'>";
				echo "Отдел: "; 
			echo "</td><td class='id'>";	
			echo "<select size='1' id='group' name='group' size='35' style='height:30px; width:280px;' onChange=\"selected3(this)\">";
				if (empty($selectPositionToUser[0]['name'])) { echo "<option value='0' SELECTED></option>"; }
				else { echo "<option value='0'></option>"; }
				foreach ($allGroup as $a) {
					if ($selectPositionToUser[0]['name'] == $a['name']) { echo "<option value='".$a['id']."' SELECTED>".$a['name']."</option>"; }
					else { echo "<option value='".$a['id']."'>".$a['name']."</option>"; }
				}
			echo "</select>";				
			echo "</td>";
			echo "</tr>";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			///// ДОЛЖНОСТЬ
			echo "<tr>";
			echo "<td style='width:195px;' class='id'>";
				echo "Должность: ";
			echo "</td><td id='position'>";

			echo "</td>";
			echo "</tr>";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			echo "</table>";
			echo "<br><br>";
			//echo "<br><p><center><input type='submit' name='enter' value='Сменить данные'></center></p>";

		
		} 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if ($_GET['id'] == 'n') { // ЕСЛИ $id_domain_user НЕ ЧИСЛО | ДОБАВЛЕНИЕ НОВОГО ПОЛЬЗОВАТЕЛЯ
			echo "<center><b>ДОБАВЛЕНИЕ ДОМЕННОГО ПОЛЬЗОВАТЕЛЯ (ПОЧТА и JABBER)</b><br>";
			echo "<br><form name='add' method='POST' action='editpers.php?id=n' enctype='multipart/form-data'>";
			
			echo "<hr><br>";
			echo "<b>УЧЕТНАЯ ЗАПИСЬ ДОМЕННОГО ПОЛЬЗОВАТЕЛЯ</b><br>";
			echo "<b style='font-size:10;color:#999;'>заводиться в виде [surname.n] например: ivanov.i</b>";
			echo "<br><br>";
			echo "<input name='login' type='text' size='35' style='height:30px; text-align:center;' placeholder='login'>";
			echo "<br><br>";
			
			echo "<hr><br>";
			echo "<b>ПАРОЛЬ ПОЛЬЗОВАТЕЛЯ</b><br>";
			echo "<b style='font-size:10;color:#999;'>Если пароль стандартный, оставить поле пустым</b>";
			echo "<br><br>";
			echo "<input name='pass' type='text' size='35' style='height:30px; text-align:center;' placeholder='password'>"; 
			echo "<br><br>";
			
			echo "<hr><br>";
			echo "<b>ФАМИЛИЯ ИМЯ ОТЧЕСТВО</b><br>";
			echo "<b style='font-size:10;color:#999;'>ФИО пользователя</b>";
			echo "<br><br>";
			echo "<input name='name' type='text' size='35' style='height:30px; text-align:center;' placeholder='имя'>"; 
			echo "<br><br>";

			$selectAllOfGroup = selectAllOfGroup ($dbcnx_j);
			echo "<hr><br><b>ВЫБОР ГРУППЫ ПОЛЬЗОВАТЕЛЯ В JABBER</b>";
			echo "<br><br>";
			echo "<select size='1' name='jabber' size='35' style='height:30px; width:280px;'><option value='' selected></option>";
			foreach	($selectAllOfGroup as $saog) {
				echo "<option value='".$saog['groupName']."'>".$saog['description']."</option>";
			}
			echo "</select>";
			echo "<br><br>";
			
			echo "<hr><br>";
			echo "<b>ВЫБОР ОТДЕЛА И ДОЛЖНОСТИ</b>";
			echo "<br><br>";
				echo "<select size='1' name='group' size='35' style='height:30px; width:280px;' id='group' onChange=\"selected2(this)\">";
				echo "<option value='' selected></option>";
					$allGroup = allGroup ($dbcnx);
				foreach ($allGroup as $a) {
					echo "<option value='".$a['id']."'>".$a['name']."</option>";
				}
				//echo "</select> <input type='button' target='_blank' onclick=javascript:window.location='editpers.php?id=o' style='width:150px; height:30px;' value='Добавить отдел'/>";
				echo "</select> <input type='button' onclick=javascript:window.open('editpers.php?id=o') style='width:150px; height:30px;' value='Добавить отдел'/>";
				echo "<br><br>";
				echo "<div id='position' style='display: inline-block'></div>";
			echo "<br><br>";
			
			echo "<hr><br>";
			echo "<b>ВЫБОР МЕСТА РАБОТЫ</b>";
			echo "<br><br>";
				echo "<select size='1' name='office' style='height:30px;width: 320px;'>";
				echo "<option value='' selected></option>";
				$allObject = allObjectOffice ($dbcnx);
				foreach ($allObject as $aO) {	
					echo "<option value='".$aO['id']."'>".$aO['name']." [ ".$aO['short_area']." ]</option>";	
				}	
				echo "</select>";
			echo "<br><br>";
			
			echo "<hr><br>";
			echo "<b>ИНТЕРНЕТ</b> <input name='internet' type='checkbox' style='width:18px; height:18px; vertical-align:middle;'>";	
			echo "<br><br>";
			
			echo "<hr><br>";
			echo "<input type='submit' name='add_domain_user' style='width:200px; height:30px;' value='ДОБАВИТЬ ПОЛЬЗОВАТЕЛЯ'></center>";
			echo "</form><br><br><br><br>";	
		}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if ($_GET['id'] == 'o') { // ЕСЛИ $id_domain_user НЕ ЧИСЛО | ДОБАВЛЕНИЕ НОВОГО ПОЛЬЗОВАТЕЛЯ
			if (isset($_POST['submit_'.$_GET['id']])) {
				if (isset($_POST['group'])) {
					mysql_query("INSERT INTO `group`(`name`) VALUES ('".$_POST['group']."')", $dbcnx);
				}
			}
			/////////////////////////////////////////////////////////////////////////////////
			echo "<center><b>ДОБАВЛЕНИЕ ОТДЕЛА</b></center><br>";
			echo "<hr><br>";
			echo "<form name='add' method='POST' action='editpers.php?id=".$_GET['id']."'>";
			
			echo "Новый отдел: <input name='group' type='text' size='20'>&nbsp;<input type='submit' name='submit_".$_GET['id']."' value='Добавить'><br><br>";
				$allGroup = allGroup ($dbcnx); $i=1;
			foreach ($allGroup as $a) {
				echo "[".$i++."]: ".$a['name']." <br>";
			}
			
			echo "<br>";
			echo "<p><input type='button' onclick=javascript:window.location='editpers.php?id=n' value='Добавить сотрудника'/> <input type='button' onclick=javascript:window.location='editpers.php?id=d' value='Добавить должность'/> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=20' value='Jabber / Email'/></p>";
			echo "</form>";
			echo "</div></div>";
		}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
		if ($_GET['id'] == 'd') { // ЕСЛИ $id_domain_user НЕ ЧИСЛО | ДОБАВЛЕНИЕ НОВОГО ПОЛЬЗОВАТЕЛЯ
			if (isset($_POST['submit_'.$_GET['id']])) {
				if ((isset($_POST['group'])) and (isset($_POST['position']))) {
					mysql_query("INSERT INTO `position`(`id_group`, `position`) VALUES ('".$_POST['group']."','".$_POST['position']."')", $dbcnx);
				}
			}
			/////////////////////////////////////////////////////////////////////////////////
			echo "<center><b>ДОБАВЛЕНИЕ ДОЛЖНОСТИ</b></center><br>";
			echo "<hr><br>";
			echo "<form name='add' method='POST' action='editpers.php?id=".$_GET['id']."'>";
			
			echo "<select size='1' name='group' style='height:21px;' id='group' onChange=\"selected(this)\">";
			echo "<option value='0' selected >Не выбрано</option>";
				$allGroup = allGroup ($dbcnx);
			foreach ($allGroup as $a) {
				echo "<option value='".$a['id']."'>".$a['name']."</option>";
			}
			echo "</select>&nbsp;<input name='position' type='text' size='20'>&nbsp;<input type='submit' name='submit_".$_GET['id']."' value='Ок'>";
			echo "<br><br><br>";
			echo "<div id='position'></div>";
			
			echo "<br><br>";
			echo "<p><input type='button' onclick=javascript:window.location='editpers.php?id=n' value='Добавить сотрудника'/> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=23' value='Добавить должность'/> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=20' value='Jabber / Email'/></p>";
			echo "</form>";
			echo "</div></div>";
		}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		

//////   1   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if ($etype == 1) {
			echo "<center><b>СМЕНА ИМЕНИ ПОЛЬЗОВАТЕЛЯ</b></center><br><br>";		
			echo "Сменить имя: <input name='newname' type='text' size='30' value='".$userdata['fio']."'></br></br>";
			echo "<br><p><center><input type='button' onclick=javascript:window.location='personal.php' value='Назад'/> <input type='submit' name='enter' value='Сменить'></center></p>";
			echo "</table></form>";
			echo "</div></div>";
		} 
//////   2   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
//////   20   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if ($etype == 20) {
			echo "<div class='link'>/ <a href='ip_pelican.php' title='Магазины'>Магазины</a> / <a href='card.php?id=".$id_object."' title='Пеликан'>".$nameObject."</a> / Редактирование</div> <div class='card'>";
			echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
			echo "<br><center><b>Добавление доменного пользователя (почта и jabber)</b></center><br><p>";
			echo "<form name='pers' method='POST' action='editpers.php?id=".$id_object."&e=".$etype."'>";
				
				echo "<table>";
				echo "<tr><td><center><b style='color:#666; font-size: 14px;'>Логин</b> </td><td><input name='login' type='text' size='35' style='height:30px' placeholder='login' title='Имя пользователя [ivanov.i] или название магазина [p8_2]'></center></td></tr>";
				echo "<tr><td><center><b style='color:#666; font-size: 14px;'>Пароль</b> </td><td><input name='pass' type='text' size='35' style='height:30px' placeholder='password' ttle='Если пароль стандартный, оставить поле пустым'></center></td></tr>"; 
				echo "<tr><td><center><b style='color:#666; font-size: 14px;'>ФИО или название</b> </td><td><input name='name' type='text' size='35' style='height:30px' placeholder='имя' title='Фамилия Имя пользователя или название магазина'></center></td></tr>"; 

				$selectAllOfGroup = selectAllOfGroup ($dbcnx_j);
				echo "<tr><td><center><b style='color:#666; font-size: 14px;'>Группа Jabber</b></center> </td><td><select size='1' name='group' title='Выбор группы пользователя в Jabber'><option value='' selected></option>";
				foreach	($selectAllOfGroup as $saog) {
					echo "<option value='".$saog['groupName']."'>".$saog['description']."</option>";
				}
				echo "</select></td></tr>";
				echo "<tr><td><center><input type='submit' name='submit' value='Добавить'></center></td></tr>";
				echo "</table><br>";
			
			echo "<p> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=24' value='Добавить сотрудника'/> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=22' value='Добавить отдел'/> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=23' value='Добавить должность'/></p>";
			echo "</form>";
			echo "</div></div>";
		}		
//////   21   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if ($etype == 21) {
			echo "<div class='link'>/ <a href='ip_pelican.php' title='Магазины'>Магазины</a> / <a href='card.php?id=".$id_object."' title='Пеликан'>".$nameObject."</a> / Редактирование</div> <div class='card'>";
			echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
			echo "<br><center><b>Добавление пользователей в Офис</b></center><br><p>";
			echo "<form name='pers' method='POST' action='editpers.php?id=".$id_object."&e=".$etype."'>";
			echo "<table>";	
			
			$allGroup = allGroup ($dbcnx);
			$allPosition = allPosition ($dbcnx);
			
			echo "</table>";
			echo "<p><input type='submit' name='submit' value='Ок'> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=24' value='Добавить сотрудника'/> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=22' value='Добавить отдел'/> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=23' value='Добавить должность'/>  <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=20' value='Jabber / Email'/></p>";
			echo "</form>";
			echo "</div></div>";
		}
//////   23   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		?>
		<script>
		function selected(a) {
			var id_group = a.value;
				$.ajax({			
					type: "POST",			
					url: "select/position.php",
					data: {
						'group': id_group
					},
					success: function(html){  
						$("#position").html(html);  
					}
				});
		}
		</script>
		<?php	
		if ($etype == 23) {
			if (isset($_POST['submit23'])) {
				if ((isset($_POST['group'])) and (isset($_POST['position']))) {
					mysql_query("INSERT INTO `position`(`id_group`, `position`) VALUES ('".$_POST['group']."','".$_POST['position']."')", $dbcnx);
				}
			}
			echo "<br><br><br><br><div id='card_body'>";
			echo "<div class='link'>/ <a href='ip_pelican.php' title='Магазины'>Магазины</a> / <a href='card.php?id=".$id_object."' title='Пеликан'>".$nameObject."</a> / Редактирование</div> <div class='card'>";
			echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
			echo "<br><center><b>Добавление должности в отдел</b></center><br><p>";
			echo "<form name='shop' method='POST' action='editpers.php?id=".$id_object."&e=".$etype."'>";
			
			echo "<select size='1' name='group' style='height:21px;' id='group' onChange=\"selected(this)\">";
			echo "<option value='0' selected >Не выбрано</option>";
				$allGroup = allGroup ($dbcnx);
			foreach ($allGroup as $a) {
				echo "<option value='".$a['id']."'>".$a['name']."</option>";
			}
			echo "</select>&nbsp;<input name='position' type='text' size='20'>&nbsp;<input type='submit' name='submit".$etype."' value='Ок'>";
			echo "<br><br><br>";
			echo "<div id='position'></div>";
			echo "<br>";
			echo "<p><input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=24' value='Добавить сотрудника'/> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=22' value='Добавить отдел'/> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=20' value='Jabber / Email'/></p>";
			echo "</form>";
			echo "</div></div>";
		}
//////   24   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
		?>
		<script>
		function selected2(a) {
			var id_group = a.value;
				$.ajax({			
					type: "POST",			
					url: "select/position.php",
					data: {
						'group2': id_group
					},
					success: function(html){  
						$("#position").html(html);  
					}
				});
				$.ajax({			
					type: "POST",			
					url: "select/domain_user_position.php",
					data: {
						'id_group': id_group
					},
					success: function(html){  
						$("#domain_user_position").html(html);  
					}
				});	
		}
		</script>
		<?php			
		if ($etype == 24) { 
			if (isset($_POST['submit24'])) {
				if ((isset($_POST['group'])) and (isset($_POST['position'])) and (isset($_POST['fio'])) and (isset($_POST['phone'])) and (isset($_POST['email']))) {
					mysql_query("INSERT INTO `domain_user`(`fio`, `phone`, `id_position`, `id_object`, `run`) VALUES ('".$_POST['fio']."','".$_POST['phone']."', '".$_POST['position']."', '".$id_object."','1')", $dbcnx);
					$kundeid = mysql_insert_id($dbcnx);
					mysql_query("INSERT INTO `email`(`id_domain_user`, `email`) VALUES ('".$kundeid."', '".$_POST['email']."')", $dbcnx);		
				}
			}
			echo "<br><br><br><br><div id='card_body'>";
			echo "<div class='link'>/ <a href='ip_pelican.php' title='Магазины'>Магазины</a> / <a href='card.php?id=".$id_object."' title='Пеликан'>".$nameObject."</a> / Редактирование</div> <div class='card'>";
			echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
			echo "<br><center><b>Добавление сотрудника</b></center><br><p>";
			echo "<form name='add' method='POST' action='editpers.php?id=".$id_object."&e=".$etype."'>";			
				echo "<select size='1' name='group' style='height:21px;width: 250px;' id='group' onChange=\"selected2(this)\">";
				echo "<option value='0' selected >Не выбрано</option>";
					$allGroup = allGroup ($dbcnx);
				foreach ($allGroup as $a) {
					echo "<option value='".$a['id']."'>".$a['name']."</option>";
				}
				echo "</select>&nbsp;<div id='position' style='display: inline-block'></div>";
				
				echo "<br><br><select size='1' name='office' style='height:21px;width: 250px;'>";
				$allObject = allObjectOffice ($dbcnx);
				foreach ($allObject as $aO) {	
					echo "<option value='".$aO['id']."'>".$aO['name']."</option>";	
				}	
				echo "</select></br>";
			
			echo "<br><br>";
			echo "<table>";	
			echo "<tr><td><b>ФИО</b></td><td><input name='fio' type='text' size='20'></td></tr>";
			echo "<tr><td><b>Телефон</b></td><td><input name='phone' type='text' size='20'></td></tr>";
			echo "<tr><td><b>Email</b></td><td><input name='email' type='text' size='20'></td></tr>";
			echo "</table>";
			echo "<input type='submit' name='submit".$etype."' value='Добавить'>";
			echo "<br><br><br>";
			echo "<div id='domain_user_position'></div>";
			echo "<br>";
			echo "<p> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=23' value='Добавить должность'/> <input type='button' onclick=javascript:window.location='editpers.php?id=".$id_object."&e=22' value='Добавить отдел'/> </p>";
			echo "</form>";
			echo "</div></div>";
		}
*/		
	}
}	

/* FOOTER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");