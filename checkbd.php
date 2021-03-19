<?php
include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."section/menu.php"); /* MENU */

$date_time = date("y-m-d H:i:s");

?>
<script>
$(document).ready(function() { 
    $("#myTable").tablesorter({sortList: [[0,0],[2,1]]}); 
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

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['id_group'] == "1") {
		
		$checkBD = checkBD1 ($dbcnx);

		echo "<div class='table2'>";
		echo "<table id='myTable' class='tablesorter' style='margin: auto;' cellspacing='1'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th style='width: 2%;' class='header'>№</th>";
		echo "<th style='width: 30%;' class='header'>Пеликан</th>";
		echo "<th style='width: 20%;' class='header'>Дата проверки</th>";
		
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";			
		$i = 1;
		foreach ($checkBD as $cbd) {
			echo "<tr>";
			echo "<td align=center>".$i++."</a></td>";
			echo "<td><a href='card.php?id=".$cbd['id']."'>".$cbd['name']."</a></td>";
				list($year, $month, $day) = sscanf($cbd['date'], "%2s %2s %2s");
				
				
				/*	foreach ($checkBD as $a) {
							list($year, $month, $day) = sscanf($a['date'], "%2s %2s %2s");
							echo "[".$i++."]: <b><a href='card?id=".$id_object."&ch=".$a['id']."'>".$day.".".$month.".".$year." ";
							$pos = strpos($a['log'], '!');
							if ($pos === false) {
								echo "[ без ошибок ]";
							} else {
								echo "[ ошибки есть ]";
							}	
							echo "</a></b><br>";
						}	
					} else { echo "данные о проверках в магазине нет"; }
				*/
				
				
				
			echo "<td align=center>";
				if (!empty($cbd['date'])) {
					echo "<div hidden='true'>".$cbd['date']."</div><b><a href='card?id=".$cbd['id']."&ch=".$cbd['ch_id']."'>".$day.".".$month.".".$year." ";
					$pos = strpos($cbd['log'], 'НЕ проведена');
					if ($pos == true) {
						echo "<b style='color:red;'> НЕ ПРОВЕДЕНА<b>";
					} else {
						$pos = strpos($cbd['log'], '!');
						if ($pos == false) {
							echo " [ без ошибок ]";
						} else {
							echo " [ ошибки есть ]";
						}
					}	
						echo "</a></b><br>";
				}
				else { echo "данные о проверках в магазине нет"; }
			echo "</td>";
			
			
			
			
			
			
			
			
			echo "</tr>";
		}
		echo "</tbody></table></div>";
		echo "<br><br>";
	}
	echo "<p id='back-top'><a href='#top'><span></span></a></p><br><br>";
}	
include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ 