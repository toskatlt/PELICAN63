<?php
/* HEADER */ include ($_SERVER["DOCUMENT_ROOT"]."/section/header.php");
/* MENU */  include ($_SERVER["DOCUMENT_ROOT"]."/section/menu.php");

include 'XMPPHP/XMPP.php';
$duty = duty_admin ($dbcnx);

$select_j = "SELECT * FROM `ofGroupUser` WHERE groupName = '01.Pel_TLT' or groupName ='02.Pel_SAM' or groupName ='03.Pel_SZN' or groupName ='04.Oper_TLT' or groupName ='05.Oper_SMR'";
$query_j = mysql_query($select_j,$dbcnx_j);	
$n = mysql_num_rows($query_j);
for ($i = 0; $i < $n; $i++) {
	$jab[] = mysql_fetch_assoc($query_j);
}

echo "<br><br><br><br><br><br><br><br><br><br><br><br><center>"; 
$date = date("d.m.Y");
	
echo "Имя ближайшего дежурного: ".$duty[0]['fio']."<br>";
echo "Телефон ближайшего дежурного: ".$duty[0]['phone']."<br>";
echo "Дата ближайшего дежурства: ".$duty[0]['date']."<br>";
$date_duty = date('d.m.Y', strtotime($duty[0]['date']));
echo $date." - дата<br>";
echo $date_duty." - дата оповещения<br><br>";

$duty_unix = strtotime($duty[0]['date']);
$today_unix = strtotime("today");

if ($duty_unix == $today_unix) {
	echo "дата равна<br><br>";
}
else {
	echo "дата не равна<br><br>";
}

echo "<br><br><br><br></center>";
if ($duty_unix == $today_unix){
	$conn = new XMPPHP_XMPP('', 5222, 'bot', '1', 'xmpphp', '', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
	try {
	$conn->connect();
	$conn->processUntil('session_start');
	$conn->presence();				
		foreach ($jab as $value) {
			$jab1 = $value['username'];
		//	$conn->message($jab1.'@neo63.ru', "Добрый вечер, это тестовое сообщение!! Проверка работоспособности!! Если вы увидели это сообщение, просьба написать что нибудь в ответ. Спасибо." );				
			$conn->message($jab1.'@neo63.ru', "Сегодня дежурный системный администратор: " .$duty[0]['fio']. " его номер телефона: ".$duty[0]['phone'] );
		}
	$conn->disconnect();
	} catch(XMPPHP_Exception $e) {
		die($e->getMessage());
	}
}