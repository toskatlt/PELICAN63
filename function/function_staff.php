<?php
//include $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$date_time = date("Y-m-d H:i:s");
$timestamp = date('Y-m-d G:i:s');

function last_ticket ($dbcnx04, $shop_email) {
	// ЗАЯВКИ БЕЗ ФАМИЛИЙ ОТВЕТСТВЕННОГО
	$query = "SELECT ost_ticket.ticketID, ost_ticket.subject, ost_ticket.status FROM ost_ticket, ost_staff WHERE ost_ticket.email='".$shop_email."' GROUP BY ost_ticket.ticketID ORDER BY ost_ticket.created DESC LIMIT 5";
	// ВЫБОР ЗАЯВОК У КОТОРЫХ НАЗНАЧЕН ОТВЕТСТВЕННОЕ ЛИЦО
	//$query = "SELECT ost_staff.lastname, ost_staff.firstname, ost_ticket.ticketID, ost_ticket.subject, ost_ticket.status FROM ost_ticket, ost_staff WHERE ost_ticket.email='".$shop_email."' and ost_staff.staff_id = ost_ticket.staff_id ORDER BY ost_ticket.created DESC LIMIT 5";
	$result = mysql_query($query,$dbcnx04);
	
	if (!$result)
		die(mysql_error($dbcnx04));
	
	$n = mysql_num_rows($result);
	$last_ticket = array();
	
	for ($i = 0; $i < $n; $i++)
	{
		$row = mysql_fetch_assoc($result);
		$last_ticket[] = $row;
	}
	
	return $last_ticket;
}

// КОЛИЧЕСТВО ВЫПОЛНЕННЫХ ЗАЯВОК ВСЕМИ АДМИНИСТРАТОРАМИ
function count_staff ($dbcnx04, $time) {
	$query = mysql_query("SELECT ost_staff.lastname, ost_staff.firstname, COUNT(ost_ticket.staff_id) FROM ost_ticket, ost_staff WHERE ost_ticket.status='closed' and ost_staff.staff_id=ost_ticket.staff_id and ost_ticket.closed >= DATE_SUB(CURRENT_DATE, INTERVAL ".$time." DAY) GROUP BY ost_ticket.staff_id ORDER BY COUNT(ost_ticket.staff_id) DESC", $dbcnx04);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// СУММА ВЫПОЛНЕНЫХ ЗАЯВОК ВЫБРАННЫМ ПОЛЬЗОВАТЕЛЕМ ПО МАГАЗИНАМ ТОП10
function closeStaffTop ($dbcnx04, $id_user) {
	$query = mysql_query("SELECT ost_staff.lastname, ost_staff.firstname, ost_ticket.name, COUNT(ost_ticket.name) FROM ost_ticket, ost_staff WHERE ost_ticket.status='closed' and ost_staff.id_domain_user='".$id_user."' and ost_staff.staff_id=ost_ticket.staff_id GROUP BY ost_ticket.name ASC ORDER BY COUNT(ost_ticket.name) DESC LIMIT 10", $dbcnx04);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

// КОЛИЧЕСТВО ОТКРЫТЫХ ЗАЯВОК
function countOpenTicket ($dbcnx04) {
	$query = "SELECT count(*)  FROM `ost_ticket` WHERE `status` = 'open' and `staff_id` = 0 and `dept_id` = 1";
	$result = mysql_query($query,$dbcnx04);	

	$row = mysql_fetch_assoc($result);
	$countOpenTicket = $row['count(*)'];
	
	return $countOpenTicket;
}