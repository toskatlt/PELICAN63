<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

$date_time = date("y-m-d H:i:s");
if (isset($_GET['id'])) $id_object = $_GET['id'];
if (isset($_GET['e'])) $etype = $_GET['e'];

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	$uname = $userdata['id'];
	if($userdata['id_group'] == "1") {
		
		echo '<div class="container">';
		
		$selectAllArea = selectAllArea ($dbcnx);	
		$area = ["ТОЛЬЯТТИ" => ["TLT"],"САМАРСКАЯ ОБЛАСТЬ" => ["SMR_OBL"],"САМАРА" => ["SMR"],"ЖИГУЛЕВСК" => ["ZHG"],"НОВОКУЙБЫШЕВСК" => ["NKS"],"КИНЕЛЬ" => ["KNL"],"СЫЗРАНЬ" => ["SZN"]];
		$curpostype = "";
		?>
		
			<center>
			<select size='1' name='select' onchange='select()' id='select'>
			<option value='0'></option>
			<?php foreach ($selectAllArea as $a) {
					echo "<option value='".$a['area']."'>";
					foreach($area as $postype=>$postypes) {
						if( in_array($a['area'], $postypes) ) {
							echo $postype;
						}
					}
					echo "</option>";
				} ?>
			</select>
			</center>
			<br><br>
			<div id='puppet'></div>
		<?php

	}
}

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");

?>
<script>
function select() {
	var change = document.getElementById('select').value;
	$.ajax({
		type: "POST",
		url: "function/function_object.php",
		data: {
			'change': change
		},
		success: function(html){
			$("#puppet").html(html);
		}
	});
}
</script>
