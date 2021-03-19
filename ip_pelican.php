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

window.setTimeout(onChange, 0);

function onChange() {
	let change = document.getElementById('select').value;
	$.ajax({			
		type: "POST",			
		url: "selectObject.php",
		data: {
			'change': change
		},
		success: function(html){  
			$("#outputObject").html(html);  
		}
	});
}

function selectObject() {
	let searchObject = document.getElementById('selectFindObject').value;
	let change = document.getElementById('select').value;
	$.ajax({			
		type: "POST",			
		url: "selectObject.php",
		data: {
			'search': searchObject,
			'change': change
		},		
		success: function(html){  
			$("#outputObject").html(html);  
		}  
	});
}
</script> 
<?php

if (isset($_GET['obl'])) { $shop_obl = $_GET['obl']; }
if (isset($_GET['find'])) { $find_object = $_GET['find']; } else $find_object = '';
if (isset($_POST['find'])) { $find_object = $_POST['find']; }

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['access'] > 2) {
		$num_list = 1;
		$selectAllPelicanCountObject = selectAllPelicanCountObject ($dbcnx); ?>
		
		<div class='pelican_title'>МАГАЗИНЫ ПЕЛИКАН | <a href='ip_pelican'>РАБОЧИХ [ <?=$selectAllPelicanCountObject?> ]</a>
		
		<?php $selectAllArea = selectAllArea ($dbcnx);	
		$area = [
		        "ТОЛЬЯТТИ" => ["TLT"],
                "САМАРСКАЯ ОБЛАСТЬ" => ["SMR_OBL"],
                "САМАРА" => ["SMR"],
                "ЖИГУЛЕВСК" => ["ZHG"],
                "НОВОКУЙБЫШЕВСК" => ["NKS"],
                "КИНЕЛЬ" => ["KNL"],
                "СЫЗРАНЬ" => ["SZN"],
                "ПЕЛИКАФЕ" => ["PLC"],
                "ПЕКАРНЯ" => ["PEK"],
                "ПРОИЗВОДСТВО" => ["PRO"],
                "ЗАКРЫТЫЕ" => ["CLS"]
            ];

		$curpostype = ""; ?>
			<select size='1' name='select' onchange='onChange()' id='select'><option value='all' SELECTED>ВСЕ РАЙОНЫ</option>
			<?php foreach ($selectAllArea as $a) {
					echo "<option value='".$a['area']."'>";
					foreach($area as $postype=>$postypes) {
						if( in_array($a['area'], $postypes) ) {
							echo $postype;
						}
					}
					echo "</option>";
				} ?>
				<option value='cls'>ЗАКРЫТЫЕ</option>
				<option value='nob'>НЕ ПРИСВОЕННЫЕ</option>		
			</select>
		</div>
		<div class='find'>ПОИСК: <input type='text' id='selectFindObject' oninput='selectObject()' size='20'></div>
		<div id='outputObject'></div>
	<p id='back-top'><a href='#top'><span></span></a></p>
	<?php
	}
}


include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php"); /* FOOTER */ 	
?>