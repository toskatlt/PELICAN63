<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['id_group'] == "1") {
		echo "<div class='table2' style='top: 160px;'>";
		echo "<table id='myTable' class='tablesorter2' style='width: 90%;margin: auto;padding-bottom: 70;'>";
		echo "<thead><tr>";
		echo "<th style='width: 2%;' class='header'>N</th>";
		echo "<th style='width: 25%;' class='header'>Username</th>";
		echo "<th style='width: 15%;' class='header'>IP</th>";
		echo "<th style='width: 12%;' class='header'>FIO</th>";
		echo "<th style='width: 7%;' class='header'>LOG ON</th>";
		echo "<th style='width: 7%;' class='header'>LOG OFF</th>";
		echo "<th style='width: 7%;' class='header'>Email</th>";
		echo "<th style='width: 3%;' class='header'>Status</th>";
		echo "</tr></thead>";	
		echo "<tbody>";
		$jabberStats = jabberStats ($dbcnx_j);
		$num_list=1;
		foreach ($jabberStats as $js) { 
			$js['Data Logon'] = substr($js['Data Logon'], 2, 10);
			if (isset($js['Data logoff'])) $js['Data logoff'] = substr($js['Data logoff'], 2, 10);
			
			if ($js['Status'] == 'unavailable') $color = 'F6CECE';
			elseif ($js['Status'] == 'available') $color = 'E0EEE0';
			else $color = 'FAEBD7';
			
			echo "<tr style='background-color: #".$color.";text-align: center;'>";
			echo "<td>".$num_list++."</td>";
			echo "<td>".$js['username']."</td>";
			echo "<td>".$js['ip']."</td>";
			echo "<td>".$js['FIO']."</td>";
			echo "<td>".date('d/m/Y H:i:s',$js['Data Logon'])."</td>";
			if (isset($js['Data logoff'])) echo "<td>".date('d/m/Y H:i:s',$js['Data logoff'])."</td>";
			else echo "<td>".date('d/m/Y H:i:s',$js['Data Logon'])."</td>";
			echo "<td>".$js['Email']."</td>";
			echo "<td>".$js['Status']."</td>";	
			echo "</tr>";
		}
		echo "</tbody>";	
		echo "</table>";
		echo "</div>";
		echo "<p id='back-top'><a href='#top'><span></span></a></p><br><br>";
	}
}	
?>
 
<script>
$(document).ready(function() { 
    if ($("table#myTable tbody tr").length > 0) {
		$("#myTable").tablesorter({sortList: [[0,0],[2,0]]}); 
	}
});

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

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");