<?php
//include $_SERVER["DOCUMENT_ROOT"]."/config.php";

$date = date("d.m.Y");
$date_time = date("Y-m-d H:i:s");
$timestamp = date('Y-m-d G:i:s');


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////   JABBER    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function jabber_logs ($dbcnx_j) {
	$query = mysql_query("SELECT conversationID, fromJID, sentDate, body, messageID FROM `ofMessageArchive` WHERE `toJID` LIKE 'bot@neo63.ru' and fromJID != 'bot@neo63.ru'", $dbcnx_j);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectJabberUser ($dbcnx_j, $username) {	
	$query = mysql_query("SELECT username FROM `ofUser` WHERE `username` = '".$username."' ORDER BY `username` DESC", $dbcnx_j);
	$result = mysql_fetch_array($query);	
	return $result;
}

function lastIpAddress ($dbcnx_j, $username) {	
	$query = mysql_query("SELECT lastIpAddress FROM userStatus WHERE userStatus.username = '".$username."' ORDER BY `lastLogoffDate` DESC LIMIT 1", $dbcnx_j);
	$result = mysql_fetch_array($query);	
	return $result['lastIpAddress'];
}


// ВЫВОД ВСЕХ ГРУПП ПОЛЬЗОВАТЕЛЕЙ JABBER
function selectAllOfGroup ($dbcnx_j) {
	$query = mysql_query("SELECT * FROM `ofGroup` WHERE 1", $dbcnx_j);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function delete_conversationID ($dbcnx_j, $conversationID) {
	mysql_query("DELETE FROM `ofMessageArchive` WHERE `conversationID`='".$conversationID."'", $dbcnx_j);
}

function jabber_logs_service ($dbcnx_s, $fromJID, $question) {
	mysql_query("INSERT INTO `jabber_bot_logs`(`from`, `text`, `date`) VALUES ('".$fromJID."','".$question."',now())", $dbcnx_s);
}

function jabber_office_user ($dbcnx_j) {
	$query = mysql_query("(SELECT userStatus.resource, userStatus.username, userStatus.online, userStatus.lastIpAddress, userStatus.lastLogoffDate, ofUser.name FROM userStatus, ofUser WHERE userStatus.username=ofUser.username and userStatus.lastIpAddress LIKE '%192.168.0%') UNION ALL (SELECT userStatus.resource, userStatus.username, userStatus.online, userStatus.lastIpAddress, userStatus.lastLogoffDate, ofUser.name FROM userStatus, ofUser WHERE userStatus.username=ofUser.username and userStatus.lastIpAddress LIKE '%192.168.254%') UNION ALL (SELECT userStatus.resource, userStatus.username, userStatus.online, userStatus.lastIpAddress, userStatus.lastLogoffDate, ofUser.name FROM userStatus, ofUser WHERE userStatus.username=ofUser.username and userStatus.lastIpAddress LIKE '%192.168.150%') UNION ALL (SELECT userStatus.resource, userStatus.username, userStatus.online, userStatus.lastIpAddress, userStatus.lastLogoffDate, ofUser.name FROM userStatus, ofUser WHERE userStatus.username=ofUser.username and userStatus.lastIpAddress LIKE '%192.168.252%') UNION ALL (SELECT userStatus.resource, userStatus.username, userStatus.online, userStatus.lastIpAddress, userStatus.lastLogoffDate, ofUser.name FROM userStatus, ofUser WHERE userStatus.username=ofUser.username and userStatus.lastIpAddress LIKE '%192.168.253%')",$dbcnx_j);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function jabberStats ($dbcnx_j) {
	$time = time() - (4 * 7 * 24 * 3600);
	$time = "00".$time."000";
	$query = mysql_query("SELECT `ofUser`.name as 'username', `userStatus`.lastIpAddress as 'ip', `ofUser`.username as 'FIO', `userStatus`.lastLoginDate as 'Data Logon', `userStatus`.lastLogoffDate as 'Data logoff', `ofUser`.email as 'Email', `userStatus`.resource as 'Client', `userStatus`.presence as 'Status' FROM `userStatus`, `ofUser` WHERE `ofUser`.username=`userStatus`.username and `userStatus`.lastLogoffDate > '".$time."'", $dbcnx_j);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectJabberStats ($dbcnx_j, $ip) {
	$query = mysql_query("SELECT `ofUser`.name as 'username', `userStatus`.lastIpAddress as 'ip', `ofUser`.username as 'FIO', `userStatus`.lastLoginDate as 'Data Logon', `userStatus`.lastLogoffDate as 'Data logoff', `ofUser`.email as 'Email', `userStatus`.resource as 'Client', `userStatus`.presence as 'Status' FROM `userStatus`, `ofUser` WHERE `ofUser`.username=`userStatus`.username and `userStatus`.lastIpAddress = '".$ip."'", $dbcnx_j);
	$n = mysql_num_rows($query);
	for ($i = 0; $i < $n; $i++) {
		$result[] = mysql_fetch_assoc($query);
	}
	return $result;
}

function selectJabber ($dbcnx, $id) {	
	$query = mysql_query("SELECT * FROM `jabber` WHERE id_domain_user='".$id."'", $dbcnx);
	$result = mysql_fetch_array($query);	
	return $result['jabber'];
}