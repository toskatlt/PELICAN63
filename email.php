<?php
if (isset($_POST['checkbox'])) header( 'Location: mailto:'.implode(",",$_POST["checkbox"]), true, 301 );

/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");
	
if (isset($_GET['group'])) {
	$group = $_GET['group'];
}
if (isset($_GET['del'])) {
	mysql_query("UPDATE `domain_user` SET run='0' WHERE `id`='".$_GET['del']."'", $dbcnx);
}
?>
<script>
$(document).ready(function(){
    $("#back-top").hide();
    $(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('#back-top').fadeIn();
            } else {
                $('#back-top').fadeOut();
            }
        });
        $('#back-top a').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });
});
window.setTimeout(onChangeMail, 0);
</script>
<style> b:hover { color: #29bed9; } </style>
<?php

$guesip = $_SERVER["REMOTE_ADDR"];
$ip = explode(".", $guesip);

if ((($ip[0] == '192') and ($ip[1] == '168')) or ($ip[0] == '10')  or ($guesip == '81.28.162.69')) {	
	$date = date("d.m.Y");
	$time = strtotime("+1 day");
	$fecha = date("d.m.Y", $time);
	
	$selectAllArea = selectAllArea ($dbcnx);	
	$area = ["ТОЛЬЯТТИ" => ["TLT"],"САМАРСКАЯ ОБЛАСТЬ" => ["SMR_OBL"],"САМАРА" => ["SMR"],"ЖИГУЛЕВСК" => ["ZHG"],"КИНЕЛЬ" => ["KNL"],"СЫЗРАНЬ" => ["SZN"],"НОВОКУЙБЫШЕВСК" => ["NKS"]];
	$curpostype = "";

	?>
	
	<div class='email_block'>
		
		<p onclick='onChangeMailOffice()' class='email_link'>Офис</p><p onchange='onChangeMail()' class='email_link'>Склад</p><p class="email_title">Магазины</p>

		<select size='1' name='select' onchange='onChangeMail()' class='shops_select' id='select'> 
			<option value='all' SELECTED>ВСЕ РАЙОНЫ</option>		
			<?php
			
			foreach ($selectAllArea as $a) {
				echo "<option value='".$a['area']."'>";
				foreach($area as $postype=>$postypes) {
					if( in_array($a['area'], $postypes) ) {
						echo $postype;
					}
				}
				echo "</option>";
			}
			
			?>
			<option value='PEL'>ПЕЛИКАФЕ</option>
		</select>
		
		<span id='all_shops' style='display:inline-block;'><form action='mailto:all_shops@neo63.ru'  target='_blank' method='POST'><input type='submit' value='ОТПРАВИТЬ НА ВСЕ МАГАЗИНЫ' class='email_button'></form></span>
	</div>
	
	<div id='selectEmail' class='selectEmail'></div>
	
	<p id='back-top'><a href='#top'><span></span></a></p>
<?php	

}

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); ?>

