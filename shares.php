<?php
echo "<head><title>Акции</title></head>";

include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."section/menu.php"); /* MENU */

require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_shares.php");

$monthes = array(
    1 => 'Января', 2 => 'Февраля', 3 => 'Марта', 4 => 'Апреля',
    5 => 'Мая', 6 => 'Июня', 7 => 'Июля', 8 => 'Августа',
    9 => 'Сентября', 10 => 'Октября', 11 => 'Ноября', 12 => 'Декабря'
);

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if (isset($userdata)) {
		echo "<div><center>";
		echo "<br><br>";
		echo "<div class='card_body' style='width:70%;'><div class='card' style='width:95%;'>";
		
		if (isset($_GET['new'])) { ////// СОЗДАНИЕ НОВОЙ РЕКЛАМНОЙ КОМПАНИИ
			if (isset($_POST['grt'])) {
				mysql_query("INSERT INTO `company`(`name_shares`, `start`, `stop`) VALUES ('".$_POST['name_shares']."', '".$_POST['start']."', '".$_POST['stop']."')", $dbcnx_tc);
				$mysql_insert_id = mysql_insert_id($dbcnx_tc);
				for ($i = 0; $i < count($_POST['grt']); $i++) {
					if ($_POST['sgrt'][$i] != '0') {
						mysql_query("INSERT INTO `company_group`(`id_company`, `id_group`, `percent`) VALUES ('".$mysql_insert_id."','".$_POST['sgrt'][$i]."','".$_POST['per'][$i]."')", $dbcnx_tc);
					} else {
						mysql_query("INSERT INTO `company_group`(`id_company`, `id_group`, `percent`) VALUES ('".$mysql_insert_id."','".$_POST['grt'][$i]."','".$_POST['per'][$i]."')", $dbcnx_tc);
					}	
				}			
			}
			echo '<b>+ НОВАЯ АКЦИОНННАЯ КОМПАНИЯ +</b><br>';
			echo '<br><div class="lenta_hr_news"><hr></div><br>';
			echo '<form action="shares.php?new" method="post">';
				echo '<b>Название</b>';
				echo '<br><input id="name_shares" name="name_shares" type="name" style="height:30px; width:650px;">';
				echo '<br><br>';
				// echo '<b>Описание</b>';
				// echo '<br><textarea id="description_shares" name="description_shares" rows="4" cols="30" style="height:90px; width:650px;"></textarea>';
				// echo '<br><br>';
				echo '<b>Начало и конец компании</b><br>';
				
				echo '<input id="start" name="start" type="date" style="height:30px; width:324px;"> ';
				echo '<input id="stop" name="stop" type="date" style="height:30px; width:324px;">';
				
				echo '<br><br>';
				echo '<b>Выбор групп компании</b>';
				$southAllGroup = southAllGroup ($dbcnx_tc);
				echo '<br>';
				echo "<div id='stroka-1'><br>";
				echo "</div>";
				echo '<br><b id="plus" onclick="add();" style="cursor: pointer;">+</b>';
			
			echo '<br><br>';
			echo '<button>СОЗДАТЬ</button>';
			
			echo '</form>';
			echo '</div></div>';
			
		} else {
			
			$timestamp = date('Y-m-d');
				$shares_startup = shares_startup ($dbcnx_tc, $timestamp);
			echo '<b>ТЕКУЩИЕ АКЦИИ [ '.count($shares_startup).' ] <a href="shares.php?new">+</a></b><br>';
			echo "<br><hr>";
				echo "<table style='width:90%;'>";
				if (count($shares_startup) > 0) {
					foreach ($shares_startup as $ss) {
						echo "<tr>";
							$start = date("d", strtotime($ss['start'])) ." ". $monthes[date("n", strtotime($ss['start']))] ." ". date("Y", strtotime($ss['start']));
							$stop = date("d", strtotime($ss['stop'])) ." ". $monthes[(date("n", strtotime($ss['stop'])))] ." ". date("Y", strtotime($ss['stop']));
							echo "<td style='width:5%;'> ".$ss['id'].". </td><td style='width:60%;'><a href='shares.php?id=".$ss['id']."'> ".$ss['name_shares']." </a></td><td style='width:20%;'>с <b>".$start."</b></td><td style='width:20%;'>по <b>".$stop."</b></td>";
						echo "<tr>";
					}
				} else { echo "<tr><td style='width:100%;' colspan='4'> нет акций </td><tr>"; }	
				echo "</table>";
			echo "<hr><br>";
			
				$shares_future = shares_future ($dbcnx_tc, $timestamp);
			echo '<b>БУДУЩИЕ АКЦИИ [ '.count($shares_future).' ]</b><br>';
			echo "<br><hr>";
				echo "<table style='width:90%;'>";
				if (count($shares_future) > 0) {
					foreach ($shares_future as $sf) {
						echo "<tr>";
							$start = date("d", strtotime($sf['start'])) ." ". $monthes[date("n", strtotime($sf['start']))] ." ". date("Y", strtotime($sf['start']));
							$stop = date("d", strtotime($sf['stop'])) ." ". $monthes[(date("n", strtotime($sf['stop'])))] ." ". date("Y", strtotime($sf['stop']));
							echo "<td style='width:5%;'> ".$sf['id'].". </td><td style='width:60%;'><a href='shares.php?id=".$sf['id']."'> ".$sf['name_shares']." </a></td><td style='width:20%;'>".$start." -</td><td style='width:20%;'>- ".$stop." </td>";
						echo "<tr>";
					}
				} else { echo "<tr><td style='width:100%;' colspan='4'> нет акций </td><tr>"; }	
				echo "</table>";
			echo "<hr><br>";
			
				$shares_close = shares_close ($dbcnx_tc, $timestamp);
			echo '<b>ЗАКОНЧЕННЫЕ АКЦИИ [ '.count($shares_close).' ]</b><br>';
			echo "<br><hr>";
				echo "<table style='width:90%;'>";
				if (count($shares_close) > 0) {
					foreach ($shares_close as $sc) {
						echo "<tr>";
							$start = date("d", strtotime($sc['start'])) ." ". $monthes[date("n", strtotime($sc['start']))] ." ". date("Y", strtotime($sc['start']));
							$stop = date("d", strtotime($sc['stop'])) ." ". $monthes[(date("n", strtotime($sc['stop'])))] ." ". date("Y", strtotime($sc['stop']));
							echo "<td style='width:5%;'> ".$sc['id'].". </td><td style='width:60%;'><a href='shares.php?id=".$sc['id']."'> ".$sc['name_shares']." </a></td><td style='width:20%;'>с <b>".$start."</b></td><td style='width:20%;'>по <b>".$stop."</b></td>";
						echo "<tr>";
					}
				} else { echo "<tr><td style='width:100%;' colspan='4'> нет акций </td><tr>"; }	
				echo "</table>";
			echo "<hr><br>";
		}		
		echo "</center></div>";
	}
}

 /* FOOTER */ include ($_SERVER["DOCUMENT_ROOT"]."section/footer.php");
 
?>
 <script type='text/javascript'>
var z = 0; 
 
function add() {
	var element = document.getElementById('stroka-1');
	
	var select = document.createElement('select');
	var option = document.createElement('option');
	
	
		select.id = 'grt['+ z +']';
		select.name = 'grt['+ z +']';
		select.setAttribute('onchange', 'grt(this.value, id)');
		select.setAttribute('style','height:30px;width:280px;margin-bottom:3px;');		
	
	var url = 'get_regions.php';
	$.get(
		url, "null",
		function (result) {
				option.text = '';
				option.value = 0;
				select.appendChild(option.cloneNode(true));
			for (var i = 0; i < result.length; i++) {
				option.text = result[i]['grt'];
				option.value = result[i]['cod_grt'];
				select.appendChild(option.cloneNode(true));
			}
		},
		"json"
	);
		
	var select_sgrt = document.createElement('select');
	var option_sgrt = document.createElement('option');	
	
		select_sgrt.id = 'sgrt['+ z +']';
		select_sgrt.name = 'sgrt['+ z +']';
		select_sgrt.setAttribute('style','height:30px; width:280px;');
		
			option.text = '- выберите группу -';
			option.value = 0;
			select_sgrt.appendChild(option.cloneNode(true));
			select_sgrt.disabled = true;

	var br = document.createElement('br');
	
	var input_per = document.createElement('input');
		input_per.id = 'per['+ z +']';
		input_per.name = 'per['+ z +']';
		input_per.setAttribute('style','height:30px; width:80px;');
		input_per.setAttribute('placeholder',' % скидки ');
	
	element.appendChild(select);
	element.appendChild( document.createTextNode( '\u00A0' ) );
	element.appendChild(select_sgrt);
	element.appendChild( document.createTextNode( '\u00A0' ) );
	element.appendChild(input_per);
	element.appendChild(br);
	
	z++;
}	

function grt(value, id) {
	var match = /[\d+]/gi;
	var s = match.exec(id);

	var sgrt = document.getElementById('sgrt['+ s +']');
	var option = document.createElement('option');
	
	if (value == '0') {
		sgrt.innerHTML = '<option>- выберите | группу -</option>';
		sgrt.disabled = true;
	} else {
		sgrt.innerHTML = '';
		sgrt.disabled = false;
	}
	
	var url = 'get_regions.php';
	$.get(
		url,
		"grt=" + value,
		function (result) {
				option.text = '';
				option.value = 0;
				sgrt.appendChild(option.cloneNode(true));
			for (var i = 0; i < result.length; i++) {
				option.text = result[i]['sgrt'];
				option.value = result[i]['cod_sgrt'];
				sgrt.appendChild(option.cloneNode(true));
			}
		},
		"json"
	);
}
</script>
