<?php
include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."section/menu.php"); /* MENU */
?>
<script>
$(document).ready(function() { 
    $("#myTable").tablesorter({sortList: [[0,0],[2,0]]}); 
} );

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
</script> 
<?php

if (isset($_GET['obl'])){ $shop_obl = $_GET['obl']; }
if (isset($_GET['find'])){ $find_object = $_GET['find']; } else $find_object = '';
if (isset($_POST['find'])){ $find_object = $_POST['find']; }

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if ($userdata['access'] > '3') {
			echo "<div class='table2'>";
			echo "<table id='myTable' class='tablesorter'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th style='width: 2%;' class='header'>№</th>";
			echo "<th style='width: 20%;' class='header'>Название</th>";
			echo "<th style='width: 15%;' class='header'>Телефон</th>";
			if ($userdata['access'] > '4') {
				echo "<th style='width: 12%;' class='header'>IP адрес</th>";
				echo "<th style='width: 15%;' class='header'>Интернет</th>";
				echo "<th style='width: 10%;' class='header'>IP WAN</th>";
			}	
			echo "</tr>";
			echo "</thead>";

			/////////////////////////////////////////////////////////////////////////////			
			/////////////////////////////////////////////////////////////////////////////		
						
			echo "<tbody>";	
			$i = 1;	

			$allObject = allObjectOffice ($dbcnx);

			foreach ($allObject as $aO) {	
				$id_object = $aO['id'];
				$object_name = $aO['name']; 
				$building_address = $aO['address'];
				$area = $aO['area'];				

				echo "<tr><td align=left>".$i."</td>"; $i++;
				if ($aO['open'] == 2) {
					echo "<td align=left style='background-color: #fad4ff;'>";
				} else { echo "<td align=left>"; }
				echo "<a href='card?id=".$id_object."' title='".$building_address."'>".$object_name."</a></td>"; 
				
			////////////////////////  TELEPHONE  ////////////////////////
				$phones = null; $phoneInObject = phoneInObject ($dbcnx, $id_object);
				if (isset($phoneInObject[0]['number'])) {
					foreach ($phoneInObject as $phone) {
						$phone['number'] = str_replace(" ","",$phone['number']);
						if (strlen($phone['number']) == '6') { $phones[] = preg_replace("/(\d{2})(\d{2})(\d{2})/","\\1-\\2-\\3",$phone['number']); }
						elseif (strlen($phone['number']) == '10') { $phones[] = preg_replace("/(\d{2})(\d{2})(\d{2}),(\d{3})/","\\1-\\2-\\3 д.\\4",$phone['number']); }
						elseif (strlen($phone['number']) == '12') { $phones[] = preg_replace("/\((\d{3})\)(\d{7})/","8(\\1)\\2",$phone['number']); }
					}
					$implode = implode(", ", $phones);
					echo "<td align=left>".$implode."</td>";
				} else {
					echo "<td align=left>нет данных</td>";
				}
			////////////////////////  IP адрес  ////////////////////////
				if ($userdata['access'] > '4') {
					$selectInternetMaskInObject = selectInternetMaskInObject ($dbcnx, $id_object);		
					if (isset($selectInternetMaskInObject)) {
						$piecesIPRoute = piecesIPRoute ($selectInternetMaskInObject);		
						echo "<td align=left><div hidden='true'>".$piecesIPRoute."</div> ".$selectInternetMaskInObject.".4</td> ";
					} else {
						echo "<td align=left>не задан</td>";			
					}
				}	
			////////////////////////  ИНТЕРНЕТ  ////////////////////////
				if ($userdata['access'] > '4') {
					echo "<td align=left>";	
					$int = selectInternetInObjectISP ($dbcnx, $id_object);
						if (isset($int['name'])) {
							echo "<div hidden='true'>".$int['id_isp']."</div><img width='13' src='img/".$int['name'].".png' title='".$int['name']." | ".$int['phone']."'> ";
						}
					echo $int['agreement'];	
					echo "</td>"; 
				}
			//////////////////////// IP WAN	 ////////////////////////
				if ($userdata['access'] > '4') {
					echo "<td align=left>".$int['ext_ip']."</td>";
				}
			}					
			echo "</tr>";
			echo "</tbody>";	
			echo "</table>";
			echo "</div>";
		echo "<p id='back-top'><a href='#top'><span></span></a></p><br><br>";	
	}
}

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ 	