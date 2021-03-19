<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

?>
<style>
@font-face {
	font-family: 'Dusha';
	src: url('/fonts/Dusha.ttf');
	url('/fonts/Dusha.ttf') format('ttf'),
	font-weight: normal;
	font-style: normal;
}
</style>
<?php
echo "<link type='text/css' rel='stylesheet' href='css/fut.css'/>";
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_avto.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_staff.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_vk.php");

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if (isset($userdata)) {
		?>
		<div class='card_body personal_flex' id='card_body'>
			<div class='reiting'>
				<?php if  ($userdata['access'] > "5") { ?>
					<a href=/stats.php><img class='reiting_block_img' src='img/reit.png' title='рейтинг'></a>
				<?php } ?>
			</div>
			<div class='card'>
					<p class='personal_title'>ЛИЧНЫЙ КАБИНЕТ ПОЛЬЗОВАТЕЛЯ <?=$userdata['id']?></p>
					<p>Пользователь: <b><?=$userdata['fio']?></b></p>
					<p>Логин: <b><?=$userdata['username']?></b></p>
					<p>Права доступа: <b><?=$userdata['access']?></b></p>
		
				<?php
				///////////   УРОВЕНЬ ДОСТУПА   ///////////////////////////////////////
				if ($userdata['id_group'] == '1') {
					$countOpenShopUser = countOpenShopUser ($dbcnx, $userdata['id']);
					echo "администратор <br>";
					echo "Открытых магазинов: <a href='stats.php?o'>".$countOpenShopUser."</a>";
				}
				echo "<br>";
				//echo "<a href='http://www.neo63.ru/editpers.php?e=1'>сменить имя пользователя</a></br>";
				//echo "<a href='http://www.neo63.ru/editpers.php?e=2'>сменить пароль</a></br>";
				
				
				$selectAvto = selectAvto ($dbcnx, $userdata['id']);
				if ($selectAvto['idx'] != 0) {
					///////////////////////// АВТО
					echo "<br><br><center><b>АВТОМОБИЛЬ</b></center><br>";
					if (isset($selectAvto)) {
						echo "Модель: <b>".$selectAvto['model']."</b></br>";
						echo "Гос.номер: <b>".$selectAvto['number']."</b></br>";
						echo "Номер водит.удост: <b>".$selectAvto['d_license']."</b></br>";
						echo "Размер бака авто: <b>".$selectAvto['fuel_limit']."</b></br>";
						echo "Модель бензина: <b>".$selectAvto['fuel_mark']."</b></br>";
						echo "Расход топлива: <b>".$selectAvto['fuel_consumption']."</b></br>";
						echo "Табельный номер: <b>".$selectAvto['tabel_number']."</b></br>";
						echo "Пробег авто: <b>".$selectAvto['mileage']."</b>";
					}
					///////////////////////// АВТО - КОНЕЦ

					echo "<br><br>";
					echo "<a href='/editpers?e=3' title='Данные авто'>сменить данные авто</a><br>";
					echo "<center> - <a href='fuel_check' title='Данные авто'>топливные чеки</a> - </center>";
				}


				if ($userdata['id_group'] == '1') {
					echo "<br><br><center><b>ТОП10 ВЫПОЛНЕННЫХ ЗАЯВОК ПО МАГАЗИНАМ</b></center><br>";
					$closeStaffTop = closeStaffTop ($dbcnx04, $userdata['id']);
					echo "<table><tr>";
					for ($i=0;$i<10;$i++) {
						echo "<tr><th style='font-weight:normal;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;sofifa_rus-align:left;'>";
						echo $closeStaffTop[$i]['name']." ";
						echo " </th> <th style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;sofifa_rus-align:left;'> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp".$closeStaffTop[$i]['COUNT(ost_ticket.name)'];
						echo "<br>";
					}
					echo "</tr></table>";
				}
				echo "<br><br>";
		echo "</div></div>";
	}
	echo "<br><br>";
}

/* FOOTER */ include ($_SERVER["DOCUMENT_ROOT"]."section/footer.php");