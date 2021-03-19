<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

if (isset($_GET['inf'])){ 
	$inf_term = $_GET['inf'];
}
echo "<br>";

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$userdata = authorization ($dbcnx, $_COOKIE['id']);
	if($userdata['id_group'] == "1") {
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
		# Выгнать из SOUTH выбранного юзера
		if (isset($_POST['killDomainUser'])) {
			mysql_query("INSERT INTO kill_list (`id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run`) SELECT `id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run` FROM user_list WHERE user_list.name_user='".$_POST['domain_user']."'", $dbcnx);
			////   LOG   ///////////////////////////////	
			$update_log = "KILL_USER: пользователь ".$_POST['domain_user']."";
			mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$userdata['username']."', '".$date_time."', 'kill_user', ' - ', '".$update_log."')", $dbcnx);
		}

		# Выгнать из SOUTH всех с выбранного терминала
		if (isset($_POST['killTerm'])) {
			mysql_query("INSERT INTO kill_list (`id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run`) SELECT `id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run` FROM USER_LIST WHERE USER_LIST.IP='".$_POST['terminal_ip']."'", $dbcnx);
			////   LOG   ///////////////////////////////	
			$update_log = "KILL_USER_TERM: пользователи с терминала ".$_POST['terminal_ip']."";
			mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$userdata['username']."', '".$date_time."', 'kill_user', ' - ', '".$update_log."')", $dbcnx);
		}
		
		# Выгнать ВСЕХ из SOUTH +
		if (isset($_POST['killAllTerm'])) {
			mysql_query("INSERT INTO kill_list (`id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run`) SELECT `id`, `ip`, `datetime`, `name_comp`, `name_user`, `domain_name`, `password`, `run` FROM user_list", $dbcnx);
			////   LOG   ///////////////////////////////	
			$update_log = "KILL_USER_ALL: все пользователи";
			mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$userdata['username']."', '".$date_time."', 'kill_user', ' - ', '".$update_log."')", $dbcnx);		
		}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		# Закрыть доступ всем пользователям в SOUTH +
		if (isset($_POST['StopAccessSouth_x'])) {
			mysql_query("UPDATE `expel_all` SET `access`='0'", $dbcnx);
			////   LOG   ///////////////////////////////	
			$update_log = "STOP_ACCESS_SOUTH: все пользователи";
			mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$userdata['username']."', '".$date_time."', 'expel_all', ' - ', '".$update_log."')", $dbcnx);
		}
		
		# Открыть доступ всем пользователям в SOUTH +
		if (isset($_POST['StartAccessSouth_x'])) {
			mysql_query("UPDATE `expel_all` SET `access`='1'", $dbcnx);
			////   LOG   ///////////////////////////////	
			$update_log = "START_ACCESS_SOUTH: все пользователи";
			mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$userdata['username']."', '".$date_time."', 'expel_all', ' - ', '".$update_log."')", $dbcnx);
		}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		if (isset($_POST['blockTerm'])) {
			mysql_query("UPDATE `terminal` SET `run`='0' WHERE `ip`='".$_POST['blockTerm']."'", $dbcnx);
			////   LOG   ///////////////////////////////	
			$update_log = "BLOCK_TERM: терминал ".$_POST['blockTerm']." ";
			mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$userdata['username']."', '".$date_time."', 'kill_user', ' - ', '".$update_log."')", $dbcnx);		
		}
		
		if (isset($_POST['unblockTerm'])) {	
			mysql_query("UPDATE `terminal` SET `run`='1' WHERE `ip`='".$_POST['unblockTerm']."'", $dbcnx);
			////   LOG   ///////////////////////////////	
			$update_log = "UNBLOCK_TERM: терминал ".$_POST['unblockTerm']." ";
			mysql_query("INSERT INTO `log` (`id_domain_user`, `date`, `table`, `id_object`, `inquiry`) VALUES ('".$userdata['username']."', '".$date_time."', 'kill_user', ' - ', '".$update_log."')", $dbcnx);		
		}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
		if (isset($_GET['inf'])) {
			echo "<br><br><br>";
			//echo "<div class='title_term'>ТЕРМИНАЛЫ СЕТИ НЕОТРЕЙД | ВСЕГО ОНЛАЙН [ ".$session_online_all." ]</div>";
			echo "<div id='card_body'>";
			echo "<div class='link'><a href='terminals.php' title='Магазины'><img width='20px' src='img/back_arrow.png'></a></div>";
			echo "<div class='card'>";
			
			$inf_term = $_GET['inf'];
			$terminal_select = terminal_select ($dbcnx, $inf_term);
			foreach ($terminal_select as $ts) {
				$t_ip = $ts['ip'];
				$t_core = $ts['core'];
				$t_name = $ts['name'];
				
				echo "<div class='term'><p>";
				echo "<center style='font-size: 16px;color: #666;'><b>Терминал ".$inf_term.". '".$t_ip."'</b></center></p><br>";
				
				echo "Количество пользователей онлайн: ";
				$domain_users_terminal_online = domain_users_terminal_online ($t_ip, $dbcnx);
				echo "[ ".$domain_users_terminal_online." ] <br>";
				
				echo "IP адресс терминала: ".$t_ip."<br>";
				echo "Количество ядер: ".$t_core."<br>";
				echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
			
				echo "Выбор свободного ядра:<br>";
				for ($i=1;$i<=$t_core;$i++){	
					$scc = session_count_core ($dbcnx, $i, $t_ip);	
					$user_core[$i] = $scc;
				}
				echo "<br>";
				if(isset($user_core)){				
					$min = array_keys($user_core, min($user_core))[0];
					for ($i=1;$i<=$t_core;$i++) {
						if ($i == $min) echo " ".$i." ядро: <span style='color:#ff0000'><b>".$user_core[$i]."</b></span><br>";
						else echo " ".$i." ядро: <b>".$user_core[$i]."</b><br>";
					}
					echo "<br>";
					echo $min." - первое менее нагруженное ядро";	
				}
				echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
			
				echo "<center><b>ДОМЕННЫЕ ПОЛЬЗОВАТЕЛЕЙ ОНЛАЙН НА ТЕРМИНАЛЕ </b>";
				if ($userdata['access'] > 7) {	
					echo "<form name='domain' method='POST' action='terminals.php?inf=".$inf_term."' style='DISPLAY: inline'><input name='domain_user' type='hidden' size='25' value='".$t_ip."'>";
					echo "<input name='terminal_ip' type='hidden' size='25' value='".$t_ip."'><input type='submit' name='killTerm' value=' X ВСЕХ ' class='xbutton' title='Выгнать всех пользователей с терминала' onclick=\"return confirmDelete_term();\"/></form>";
					echo "<form name='domain' method='POST' action='terminals.php?inf=".$inf_term."' style='DISPLAY: inline'>";
					$terminal_select = terminal_select ($dbcnx, $inf_term);
					$dus_rus = $terminal_select[0]['run'];
					if ($dus_rus == '1') {
						echo "<input type='image' name='blockTerm' class='image' width='12px' value='".$t_ip."' src='img/open.png' onclick=\"return confirmBlock_term1();\"/>";
					} else {
						echo "<input type='image' name='unblockTerm' class='image' width='12px' value='".$t_ip."' src='img/close.png' onclick=\"return confirmBlock_term0();\"/>";
					}
					echo "</form>";
				}
				echo "</center><br><br>";

				for ($i=1; $i<=$t_core; $i++) {
					$s=1;
					$session_count_core = session_count_core ($dbcnx, $i, $t_ip);
					if ($session_count_core > 0) {
						echo "<b>На ядро ".$i." подключены пользователи:</b><br><br>";	
						echo "<table><tr>";
						$session_online_terminal = session_online_terminal ($dbcnx, $i, $t_ip);	
						foreach ($session_online_terminal as $sot) {
							$to_core = $sot['core'];
							$to_name = $sot['name_user'];
							echo "<tr><td style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #000;text-align:left; width: 200px;'>";
							echo $s++.". <b>";
							
							$selectDomainUserTerminal = selectDomainUserTerminal ($dbcnx, $to_name);
							if (isset($selectDomainUserTerminal[0]['id_object'])) {
								echo "<a href='card?id=".$selectDomainUserTerminal[0]['id_object']."'>".$to_name."</a>";
							} else {
								echo $to_name;
							}	
							echo "</b></td><td style='font-weight:bolt;font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;font-size: 14px;color: #666;text-align:left;'> на ядре <b>".$i."</b></td><td>"; 
							if ($userdata['access'] > 7) {
								echo "<form name='domain' method='POST' action='terminals.php?inf=".$inf_term."' style='DISPLAY: inline'>";
								echo "<input name='domain_user' type='hidden' size='25' value='".$to_name."'>";
								echo "<input type='submit' name='killDomainUser' value=' X ' class='xbutton' onclick=\"return confirmDelete();\"/>";
								echo "</form>";
							}	
							echo "<br></td></tr>";
						}
					echo "</tr></table>";	
					echo "<br>";	
					}
				}
				echo "<br>";	

			}	
			echo "</div>";
			echo "</div></div><br><br><br>";		
		}
//////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////
		else {
			echo "<br><br><br>";
			$countTerminal = countTerminal ($dbcnx);
			$session_online_all = session_online_all ($dbcnx);
			echo "<div class='title_term'>ТЕРМИНАЛЫ СЕТИ НЕОТРЕЙД [ ".$countTerminal." ] | ВСЕГО ОНЛАЙН [ ".$session_online_all." ] | "; 
			if ($userdata['access'] > 6) {
				echo " <form method='POST' action='terminals.php' style='DISPLAY: inline'><input type='submit' name='killAllTerm' value=' ВЫГНАТЬ ВСЕХ ' class='xbutton' onclick=\"return confirmDelete_term_all();\"/></form>";
				
				echo " <form method='POST' action='terminals.php' style='DISPLAY: inline'>";
				$result_expel = mysql_fetch_assoc(mysql_query("SELECT access FROM expel_all", $dbcnx));
				if ($result_expel['access'] == 1) {
					echo " <input type='image' src='img/red.png' name='StopAccessSouth' value='".$to_name."' style='vertical-align:bottom' title='ЗАКРЫТЬ ДОСТУП В SOUTH' width='20px' onclick=\"return confirmsubmitStopAccessSouth();\">";					
				} else {
					echo " <input type='image' src='img/green.png' name='StartAccessSouth' value='".$to_name."' style='vertical-align:bottom' title='ОТКРЫТЬ ДОСТУП В SOUTH' width='20px' onclick=\"return confirmsubmitStartAccessSouth();\">";					
				}
				echo "</form>";
			}
			echo "</div><br><br>";
			echo "<div id='card_body'>";
			echo "<div class='card'>";
			
			$s=1;
			$terminal_all = terminal_all($dbcnx);
			foreach ($terminal_all as $tr) {
				$max=null; $user_core=null;	
				$t_id = $tr['id'];
				$t_ip = $tr['ip'];
				$t_core = $tr['core'];
				$t_lcore = $tr['last_core'];
				
				echo "<div class='term'>";
				echo "<b>".$s++.". <a href='?inf=".$t_id."'>".$t_ip."</a>";
				///////   БЛОКИРОВКА ТЕРМИНАЛА   ////////////////////////////			
				if ($userdata['access'] > 6) {
					echo " <form name='domain' method='POST' action='terminals' style='DISPLAY: inline'>";
					if ($result_expel['access'] == 0) {
						echo " <input type='image' class='image' style='DISPLAY: inline' width='12px' value='".$t_ip."' src='img/close.png' title='Доступ закрыт для ВСЕХ пользователей'/>";
					} elseif (($result_expel['access'] == 1) and (($tr['run'] == '0'))) {
						echo " <input type='image' name='unblockTerm' class='image' style='DISPLAY: inline' width='12px' value='".$t_ip."' src='img/close.png' onclick=\"return confirmBlock_term0();\" title='Открыть доступ всем пользователям терминала ".$t_ip."'/>";							
					} else {
						echo " <input type='image' name='blockTerm' class='image' style='DISPLAY: inline' width='12px' value='".$t_ip."' src='img/open.png' onclick=\"return confirmBlock_term1();\" title='Закрыть доступ всем пользователям терминала ".$t_ip."'/>";
					}
					echo "</b></form>";
				}	
				echo "<br><br><br>";
				echo "IP адресс: ".$t_ip."<br>";
				echo "Количество ядер: ".$t_core."<br><br>";
				echo "Количество пользователей онлайн: ";
					$domain_users_terminal_online = domain_users_terminal_online ($t_ip, $dbcnx);
				echo "[ ".$domain_users_terminal_online." ] <br>";
				echo "Пользователей NEODC работающих на терминале: ";
					$domain_users_terminal = domain_users_terminal ($t_ip, $dbcnx);
				echo "[ ".$domain_users_terminal." ] <br>";
				///////////////////////////////////////////////////////////////////////////////////////
				####### РАСЧЕТ РАСПРЕДЕЛЕНИЯ ПОДКЛЮЧЕННЫХ ПОЛЬЗОВАТЕЛЕЙ К ЯДРАМ ТЕРМИНАЛА  ############
				$medium = ($domain_users_terminal_online/$t_core);
				$ceil = (ceil($medium)+1);
				
				for ($i=1;$i<=$t_core;$i++) {
					$scc = session_count_core ($dbcnx, $i, $t_ip);
					if($scc > 0) {	
						$user_core[$i] = $scc;
					}
				}
				echo "<br>";
				if (isset($user_core)) {
					$max = max($user_core);
					$min = min($user_core);					
				} else {
					$max = 0; $min = 0;
				}
				if (($max - $min) > 1 && ($max - $min) <= 3) { echo "<b><p style='color:red;'>Нагрузка на ядра не сбалансирована, не критично</p></b>"; }
				elseif  (($max - $min) > 3) {  
				
					echo "<b><p style='color:red;'>!! Нагрузка на ядра не сбалансирована, критично !!</p></b>"; 
					
					$addr = 'support@neo63.ru';   
					$mtext = 'Нагрузка на ядра терминала '.$t_ip.' не сбалансирована. Обратить внимание, устранить причину!!';   
					$headers =  "Content-Type: text/html; charset=windows-1251\r\n".   
								   "From: site <autobot@neo63.ru>\r\n".
								   "MIME-Version: 1.0";
					$subject = "Терминал ".$t_ip;

					mail($addr, $subject, $mtext, $headers);
				
				}
				
				#######################################################################################
				///////////////////////////////////////////////////////////////////////////////////////
				
				echo "<br><tr><td><div class='lenta_hr_news'><hr></div></td></tr><br>";
				echo "</div>";		
			}
			echo "</div></div><br><br><br>";
		}
//////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////	
		include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");
	}
}