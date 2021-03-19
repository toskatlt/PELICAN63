<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/function/function_domain_user.php");
include "test/vendor/autoload.php";

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['access'] > 6) {
				
		# Удаление пользователя
		if (isset($_POST['del_domain_user_id'])) {
			$id_domain_user = $_POST['del_domain_user_id'];
			$selectDomainUser = selectDomainUser ($dbcnx, $id_domain_user);
			
			# Удаление пользователя с 10.0.0.242
			mysql_query("DELETE FROM `domain_user` WHERE `id`='".$id_domain_user."'", $dbcnx);
			mysql_query("DELETE FROM `email` WHERE `id_domain_user`='".$id_domain_user."'", $dbcnx);			
			//echo "DELETE FROM `domain_user` WHERE `id`='".$id_domain_user."'<br>";
			//echo "DELETE FROM `email` WHERE `id_domain_user`='".$id_domain_user."'<br><br>";
			
			if (!empty($selectDomainUser['username'])) {
				# Удаление почты пользователя с 192.168.0.4
				mysql_query("DELETE FROM `mailbox` WHERE `username`='".$selectDomainUser['username']."@neo63.ru'", $dbcnx_pf);		
					//echo "DELETE FROM `mailbox` WHERE `username`='".$selectDomainUser['username']."@neo63.ru'<br><br>";
				
				# Удаление доступа в интернет пользователя с 192.168.0.5
				mysql_query("DELETE FROM `mailbox` WHERE `username`='".$selectDomainUser['username']."@neo63.ru'", $dbcnx05);
					//echo "DELETE FROM `passwd` WHERE `user`='".$selectDomainUser['username']."'<br><br>";
				
				# Удаление доступа в интернет пользователя с 192.168.0.2
				mysql_query("DELETE FROM `ofUser` WHERE `username`='".$selectDomainUser['username']."'", $dbcnx_j);
					//echo "DELETE FROM `ofUser` WHERE `username`='".$selectDomainUser['username']."'<br><br>";

				exec("sudo ssh root@192.168.0.107 dcedit delete ".$selectDomainUser['username'], $output);
					//echo "sudo ssh root@192.168.0.107 dcedit delete ".$selectDomainUser['username']."<br><br>";
			}
			header("location:" . __FILE__);
		}
		
		$sumAllDomainUserRunActive = sumAllDomainUserRunActive ($dbcnx);
		echo "<div class='title_term'><span id='all' style='cursor:pointer'>ПОЛЬЗОВАТЕЛИ</span> | АКТИВНЫХ [ ".$sumAllDomainUserRunActive." ] | <span id='stuff' style='cursor:pointer'>ОФИС</span> :: <span id='shop' style='cursor:pointer'>МАГАЗИНЫ</span> | + <a href='editpers.php?id=n'>НОВЫЙ ПОЛЬЗОВАТЕЛЬ</a> +</div><br>";
		echo "<div class='find'>ПОИСК: <input type='text' id='searchUserId' oninput=\"searchUser()\" size='20'></div>";
		echo "<div id='outputUser'></div>";
		
	echo "<p id='back-top'><a href='#top'><span></span></a></p><br><br>";	
	}
}
	
include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");

?>
<script>
$(document).ready(function() { 
    $("#myTable").tablesorter({sortList: [[0,0],[2,0]]}); 
} );

$(document).ready(function() {
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

let $divTitleTerm = document.querySelector('.title_term');
	$divTitleTerm.addEventListener('click', handleDivClick);

function handleDivClick (event) {
	selectUser(event.target.id)
}	

function selectUser(change) {
	$.ajax({
		type: "POST",			
		url: "selectUser.php",
		data: {
			'change': change
		},
		success: function(html){  
			$("#outputUser").html(html);  
		}
	});
}

selectUser('all');


function searchUser() {
	let searchUserId = document.getElementById('searchUserId').value;
	$.ajax({			
		type: "POST",			
		url: "selectUser.php",
		data: {
			'searchUser': searchUserId
		},		
		success: function(html){  
			$("#outputUser").html(html);  
		}  
	});
}
</script>