<?php
include ($_SERVER["DOCUMENT_ROOT"]."section/header.php"); /* HEADER */
include ($_SERVER["DOCUMENT_ROOT"]."section/menu.php"); /* MENU */

include 'XMPPHP/XMPP.php';

$date = date("d.m.Y");
$jabber_logs = jabber_logs ($dbcnx_j);
$duty_admin = duty_admin ($dbcnx);

if (isset($duty_admin[0]['date'])) {
	$date_duty = date('d.m.Y', strtotime($duty_admin[0]['date']));
}

echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><center>";
// ПРОВЕРКА СООБЩЕНИЙ НА СООТВЕТСТВИЕ

//var_dump($jabber_logs);
if (isset($jabber_logs[0]['body'])) {
	foreach ($jabber_logs as $jl) {
		
		echo "1<br>";	
		$question = $jl['body'];
		$fromJID = $jl['fromJID'];
		$conversationID = $jl['conversationID'];

		if (isset($question)) {
///////////////   ВОПРОС 1   ////////////////////////////////////////////////////////			
			$stristr_d = stristr($question, '!дежурный');
			$stristr_k = stristr($question, '!код');	
			$stristr_v = stristr($question, '!время');
			$stristr_p = stristr($question, '!погода');				
			if ($stristr_d == '!дежурный') {
				// ПОКДЛЮЧЕНИЕ bot К ДЖАБЕР СЕРВЕРУ
				$conn = new XMPPHP_XMPP('', 5222, 'bot', '1', 'xmpphp', '', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
				//$conn->autoSubscribe();
				try {
				$conn->connect();
				$conn->processUntil('session_start');
				$conn->presence();	
				
				if($date_duty == $date){
					//echo "вопрос был получен от пользователя ".$fromJID."<br>";
					$conn->message($fromJID, "Сегодня дежурный системный администратор: " .$duty_admin[0]['name']. " его номер телефона: ".$duty_admin[0]['telephone'] );
				}
				else {
					$conn->message($fromJID, "Сегодня дежурных нет" );
				}
				delete_conversationID ($dbcnx_j, $conversationID);
				
				$conn->disconnect();
				} catch(XMPPHP_Exception $e) {
					die($e->getMessage());
				}
			}
///////////////   ВОПРОС 2   ////////////////////////////////////////////////////////							
			elseif ($stristr_k == '!код') {
				$day_today = date("d");
				$month_today = date("m");
			
				$kod = $day_today * $month_today;
				$rest = substr($kod, 0, 4);
				$strlen = strlen($rest);
				if ($strlen == 1){
					$kod_inst = "77070".$rest."070700";
				}
				else {
					$kod_inst = "7707".$rest."070700";
				}
				
				// ПОКДЛЮЧЕНИЕ bot К ДЖАБЕР СЕРВЕРУ
				$conn = new XMPPHP_XMPP('', 5222, 'bot', '1', 'xmpphp', '', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
				//$conn->autoSubscribe();
				try {
				$conn->connect();
				$conn->processUntil('session_start');
				$conn->presence();	
				
				$conn->message($fromJID, "Код инсталяции на сегодня: ".$kod_inst." инсталятор: 006" );
				delete_conversationID ($dbcnx_j, $conversationID);
				
				$conn->disconnect();
				} catch(XMPPHP_Exception $e) {
					die($e->getMessage());
				}
			}
///////////////   ВОПРОС 3   ////////////////////////////////////////////////////////	
			elseif ($stristr_v == '!время') {
				// ПОКДЛЮЧЕНИЕ bot К ДЖАБЕР СЕРВЕРУ
				$conn = new XMPPHP_XMPP('', 5222, 'bot', '1', 'xmpphp', '', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
				//$conn->autoSubscribe();
				try {
				$conn->connect();
				$conn->processUntil('session_start');
				$conn->presence();	
				
				$date_today = date( " Текущее время - ".date("H:i:s") );
				$conn->message($fromJID, $date_today );
				delete_conversationID ($dbcnx_j, $conversationID);
				
				$conn->disconnect();
				} catch(XMPPHP_Exception $e) {
					die($e->getMessage());
				}
			}
///////////////   ВОПРОС 4   ////////////////////////////////////////////////////////		
			elseif ($stristr_p == '!погода') {
				$select = "SELECT * FROM `weather` WHERE `id`='1'";
				$query = mysql_query($select,$dbcnx_s);
				$result = mysql_fetch_assoc($query);
				
				$sun = $result['sun'];
				$temperature = $result['temperature'];
				
				// ПОКДЛЮЧЕНИЕ bot К ДЖАБЕР СЕРВЕРУ
				$conn = new XMPPHP_XMPP('', 5222, 'bot', '1', 'xmpphp', '', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
				//$conn->autoSubscribe();
				try {
					$conn->connect();
					$conn->processUntil('session_start');
					$conn->presence();	
					
					$conn->message($fromJID, "На улице ".$sun );
					$conn->message($fromJID, $temperature."°C воздуха" );

					//$conn->message($fromJID, "На улице ".$atmosphere." \r\n ".$temperature."°C воздуха \r\n ".$direction." ".$wind." м/с" );
					delete_conversationID ($dbcnx_j, $conversationID);
					
					$conn->disconnect();
				} catch(XMPPHP_Exception $e) {
					die($e->getMessage());
				}
			}		
///////////////   ВОПРОС Х   ////////////////////////////////////////////////////////			
			//$stristr = stristr($question, '');		
			else {
				// ПОКДЛЮЧЕНИЕ bot К ДЖАБЕР СЕРВЕРУ
				$conn = new XMPPHP_XMPP('', 5222, 'bot', '1', 'xmpphp', '', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
				try {
					$conn->connect();
					$conn->processUntil('session_start');
					$conn->presence();	
					
					$conn->message($fromJID, "любой символ не являющийся командой - вывод списка доступных команд ");
					$conn->message($fromJID, "Команда ' !код ' - выводит актуальныый код инсталяции для SOUTH ");
					$conn->message($fromJID, "Команда ' !дежурный ' - Фамилия Имя и телефон дежурного системного администратора " );
					$conn->message($fromJID, "Команда ' !время ' - Точное время " );
					$conn->message($fromJID, "Команда ' !погода ' - Погода в Тольятти на текущий час " );
					delete_conversationID ($dbcnx_j, $conversationID);
					
					$conn->disconnect();
				} catch(XMPPHP_Exception $e) {
					die($e->getMessage());
				}
			}
///////   ЗАПИСЬ ОБРАЩЕНИЙ К JABBER БОТУ   ///////		
			jabber_logs_service ($dbcnx_s, $fromJID, $question);
///////////////    КОНЕЦ    ////////////////////////////////////////////////////////	
		}
	}
}
if (isset($duty_admin[0]['name'])) {
	echo "Имя ближайшего дежурного: ".$duty_admin[0]['name']."<br>";
}
if (isset($duty_admin[0]['telephone'])) {
	echo "Телефон ближайшего дежурного: ".$duty_admin[0]['telephone']."<br>";
}
if (isset($duty_admin[0]['date'])) {
	echo "Дата ближайшего дежурства: ".$duty_admin[0]['date']."<br>";
}
	
echo "<br><br><br><br>";
echo "</center>";

include ($_SERVER["DOCUMENT_ROOT"]."/section/footer.php");