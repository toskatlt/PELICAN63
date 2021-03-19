<?php
if (isset($_POST['exit_cookies'])){
	setcookie("id", '');
	setcookie("hash", '');
	header("Refresh:1"); 
}
?>
<nav class="navigation">
	<ul class='menu'>
		<li class="menu-list"><a href='/' class="menu-link">Главная</a></li>
		<li class="menu-list"><a href='/promo/pepsi/' class="menu-link">Акции</a></li>
		<li class="menu-list"><a href='/address' class="menu-link">Адреса магазинов</a></li>
		<li class="menu-list"><a href='/partneram' class="menu-link">Партнерам</a></li>
		
<?php	
if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) { 
	$userdata = authorization_lite ($dbcnx, $_COOKIE['id']);
	if($userdata['id'] == $_COOKIE['id']) {
			if  ($userdata['access'] > "2") {
				?>
				<li class='menu-list'><p href='check.php' class='menu-link'>Админ панель</p>
					<div class="drop-box">
						<div class="drop-flex">
							<ul class="drop-ul">
								<li class="drop-li"><a href='/ip_pelican' class="drop-link">IP PELICAN</a></li>
								<li class="drop-li"><a href='/ip_office' class="drop-link">IP OFFICE</a></li>
								<li class="drop-li"><a href='/ip_mindal' class='drop-link'>IP MINDAL</a></li>
								<li class="drop-li"><a href='/email' class='drop-link'>Почтовые адреса</a></li>
								<?php if  ($userdata['access'] > "4") { ?>
									<li class="drop-li"><a href='/domain_user' class='drop-link'>Пользователи</a></li>		
									<li class="drop-li"><a href='/rsa' class='drop-link'>RSA</a></li>
									<li class="drop-li"><a href='/jabber' class='drop-link'>Jabber</a></li>
									<li class="drop-li"><a href='http://192.168.0.2:9090/' target='_blank' class='drop-link'>OpenFire</a></li>
									<li class="drop-li"><a href='http://forum.neo63.ru/' target='_blank' class='drop-link'>Форум</a></li>
									<li class="drop-li"><a href='/duty_admin' target='_blank' class='drop-link'>Дежурство</a></li>
									<li class="drop-li"><a href='/terminals' class='drop-link'>Терминалы</a></li>
									<li class="drop-li"><a href='/mpdf/index?id=<?=$userdata['id']?>' class='drop-link'>Накладная</a></li>
									<li class="drop-li"><a href='/checkbd' class='drop-link'>Проверки БД</a></li>
									<li class="drop-li"><a href='/rsa_all_stat' class='drop-link'>OFFLINE EGAIS</a></li>									
								<?php } 
								if ($userdata['access'] > "6") { ?>
									<li class="drop-li"><a href='/south_group.php' class='drop-link'>South Group</a></li>
									<li class="drop-li"><a href='/scan.php' class='drop-link'>Сканы</a></li>
								<?php } ?>
							</ul>
						</div>						
					</div>
			<?php } ?>
			<li class='menu-list'><a href='/neocam' class='menu-link'>NEO CAM</a></li>
			<?php if  ($userdata['access'] > "4") { ?>
				<li class='menu-list'><a href='/shares.php' class='menu-link'>Акции</a></li>
			<?php } ?>
			<li class='menu-list'><a href='/radio' class='menu-link'>Радио</a></li>
		<?php		
	}
}
?> 		
	</ul>
</nav>			
<?php	
if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {		
	if ($userdata['access'] > "4") { ?>
			<div class='block_left_menu'>
				<?php if ($userdata['access'] > 8) { ?>
					<div class='left_menu bg_lm'><p id='pass' class='pass'></p></div>
				<?php } ?>			
				<div class='left_menu bg_lm2'><a href='/ip_pelican'><img class="left_menu_img" src='/img/ip.png' title='IP Пеликан'></a></div>
				<div class='left_menu bg_lm3'><a href='http://support.neo63.ru/scp/'><img class="left_menu_img" src='/img/scp.png' title='ТехПоддержка'></a></div>
				<div class='left_menu bg_lm4'><a href='/jabber'><img class="left_menu_img"  src='/img/jabber.png' title='Jabber'></a></div>
				<div class='left_menu bg_lm5'><a href='http://forum.neo63.ru/'><img class="left_menu_img" src='/img/forum.png' title='Форум Пеликан'></a></div>
				<div class='left_menu bg_lm6'><a href='/terminals'><img class="left_menu_img" src='/img/terminal.png' title='Терминалы'></a></div>
				<?php	$sumAllJacarta = sumAllJacarta ($dbcnx);
				if ($sumAllJacarta > 0) { 
					echo '<div class="left_menu bg_lm7"><a href="/rsa" class="rsa left_menu_flex">';

					if ($userdata['username'] == "it") { 
						echo '<p class="sum_rsa_it">'.$sumAllJacarta.'</p></a></div>';
					 } else { 
						echo '<p class="sum_rsa">'.$sumAllJacarta.'</p></a></div>'; 
					 }
			    } ?>
				<div class='left_menu bg_lm8'><a href='/zapravka'><img class="left_menu_img" src='/img/zapravka.png' title='Заправка картриджей'></a></div>
			</div>
		<?php
	}	
}
?>

<script>
function pass() {
	var date = new Date();
	var month = date.getMonth();
	var month = ++month;
	var day = date.getDate();
	var hour = date.getHours();
	//var hour = date.getSeconds();
	var pass = ((400 * day) + (day * month) + (33 - hour));
	document.getElementById('pass').innerHTML = pass;
	window.setTimeout(arguments.callee, 1000);
}
window.onload = pass;
</script>