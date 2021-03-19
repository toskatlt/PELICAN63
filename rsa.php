<?php
include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."section/menu.php"); /* MENU */

$date_time = date("y-m-d H:i:s");

?>
<script>
$(document).ready(function() { 
    $("#myTable").tablesorter({sortList: [[3,0]]}); 
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

if (isset($_GET['u'])){		
	$update = $_GET['u'];		
} else {
	$update = 0;
}		

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['id_group'] == "1") {
		
		$allJacarta = allJacarta ($dbcnx);
		echo "<div class='rsa_button_block'>";
			echo "<input type='button' class='rsa_button' onclick=javascript:window.location='rsa.php?u=1' value='Обновить данные'/>";
			echo "<input type='button' class='rsa_button' onclick=javascript:window.location='rsa.php?u=2' value='Обновить FIO и всякую фигню'/>";
		echo "</div>";
		
		echo "<div class='table2'>";
		echo "<table id='myTable' class='tablesorter' style='margin: auto;' cellspacing='1'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th style='width: 3%;' class='header'>№</th>";
			$countRutoken = countRutoken ($dbcnx);
		echo "<th style='width: 20%;' class='header'>Пеликан [ <img src='img/rutoken.png' style='width:13px' alt='rutoken' title='rutoken'> ".$countRutoken." ]</th>";
		//echo "<th style='width: 14%;' class='header'>RSA IN BASE</th>";
		echo "<th style='width: 10%;' class='header'>Дата RSA</th>";
		echo "<th style='width: 10%;' class='header'>Дата GOST</th>";
		echo "<th style='width: 7%;' class='header'>КПП</th>";
		echo "<th style='width: 19%;' class='header'>ФИО на ключе</th>";
		echo "<th style='width: 19%;' class='header'>ФИО директора в магазине</th>";
		echo "<th style='width: 7%;' class='header'>IP</th>";
		//echo "<th style='width: 7%;' class='header'>Версия [".$percent."%]</th>";
		echo "<th style='width: 7%;' class='header'>Версия</th>";
		
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";			
		$i = 1;
		foreach ($allJacarta as $aj) {
			$id_object = $aj['id'];
			$selectJacartaInObject = selectJacartaInObject ($dbcnx, $id_object);
			$id = $aj['id'];
			$ip = $selectJacartaInObject['ip'];
			$date_rsa = $selectJacartaInObject['date_rsa'];
			$date_gost = $selectJacartaInObject['date_gost'];
			$rsa = $selectJacartaInObject['rsa'];
			$kpp = $selectJacartaInObject['kpp'];
			$fio = $selectJacartaInObject['fio'];
			$bild_number = $selectJacartaInObject['bild_number'];
			$name = $aj['name'];
						
			if ($update == 1) {
				?>
				<script>
				var id = '<?php echo $aj['id'] ?>';				
				$.ajax({			
					type: "POST",			
					url: "eupd/rsa_upd.php",
					data: {
						'id': id
					} 
				});
				</script>
				<?php	
			}
			if ($update == 2) {
				?>
				<script>
				var id = '<?php echo $aj['id'] ?>';				
				$.ajax({			
					type: "POST",			
					url: "eupd/rsa_fio.php",
					data: {
						'rsa_fio': id
					} 
				});
				</script>
				<?php	
			}
			
			echo "<td align=center>".$i++."</a></td>";

			if (mb_strlen($ip) < 5) {
				echo "<td align=center style='background-color: #fad4ff;'>";
			} else { echo "<td align=center>"; }
			
			echo "<a href='card.php?id=".$id_object."' title='".$selectJacartaInObject['address']."'>";
			if ($selectJacartaInObject['token'] == 'rutoken') {
				echo "<img src='img/rutoken.png' style='width:13px' alt='rutoken' title='rutoken'> ";
			}	
			echo $name."</a></td>";
//////////    №    ////////////////////////////////////////// 
			/*					
				if ((isset($rsa)) and ($rsa != 0)) {
					echo "<td align=center>".$rsa."</td>";
				} else {
					echo "<td align=center style='color:red;'>нет данных</td>";
				}
			*/
//////////   ДАТА ОКОНЧАНИЯ СРОКА КЛЮЧА RSA //////////////////////////////////////////	
			if ($date_rsa == '0000-00-00') {
				echo "<td align=center style='color:red;'><div hidden='true'>0000</div>нет данных</td>"; 
			} else {
				$drsort = date ("Ymd", strtotime($date_rsa));
				$dr = date ("d.m.Y", strtotime($date_rsa));
				if (strtotime("+18 days") > strtotime($date_rsa)) {
					echo "<td align=center style='color:red;'><div hidden='true'>".$drsort."</div>".$dr."</td>";
				} else {
					echo "<td align=center><div hidden='true'>".$drsort."</div>".$dr."</td>";
				}
			}	
//////////   ДАТА ОКОНЧАНИЯ СРОКА КЛЮЧА GOST //////////////////////////////////////////	
			if ($date_gost == '0000-00-00') {
				echo "<td align=center style='color:red;'><div hidden='true'>0000</div>нет данных</td>"; 
			} else {			
				$drsort = date ("Ymd", strtotime($date_gost));
				$dr = date ("d.m.Y", strtotime($date_gost));
				if (strtotime("+18 days") > strtotime($date_gost)) {
					echo "<td align=center style='color:red;'><div hidden='true'>".$drsort."</div>".$dr."</td>";
				} else {
					echo "<td align=center><div hidden='true'>".$drsort."</div>".$dr."</td>";
				}
			}			
//////////   КПП   //////////////////////////////////////////				
			if ((!empty($kpp)) and ($kpp != 0)) {
				echo "<td align=center>".$kpp."</td>";
			} else {
				echo "<td align=center style='color:red;'>нет данных</td>";
			}
//////////   FIO   //////////////////////////////////////////				
			if ((!empty($fio)) and ($fio != '0')) {
				echo "<td>".$fio."</td>";
			} else {
				echo "<td align=center style='color:red;'>нет данных</td>";
			}
//////////   FIO SHOP  //////////////////////////////////////
			$selectCod1C = selectCod1C ($dbcnx, $id_object);
			if (!empty($selectCod1C['fio'])) {
				if ($selectCod1C['fio'] == $fio) {
					echo "<td align=center style='color:green;'>".$selectCod1C['fio']."</td>";
				} else {
					echo "<td align=center style='color:red;'>".$selectCod1C['fio']."</td>";
				}	
			} else {
				echo "<td align=center style='color:red;'>нет данных</td>";
			}
//////////   IP  //////////////////////////////////////
			$selectAtolInObject = selectAtolInObject ($dbcnx, $id_object);
			$ipAtolInObject = trim($selectAtolInObject['ip']);
				echo "<td align=center>".$ipAtolInObject."</td>";		
//////////   buildNumber   //////////////////////////////////////////
			if ((isset($bild_number)) and ($bild_number != 0)) {
				echo "<td>".$bild_number."</td>";
			} else {
				echo "<td style='color:red;'> -- </td>";
			}
			echo "</tr>";
		}
		echo "</tbody></table></div>";		
	}
	echo "<p id='back-top'><a href='#top'><span></span></a></p><br><br>";
}	

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ 