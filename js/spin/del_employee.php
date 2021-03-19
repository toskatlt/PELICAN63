<?php

include "config.php";

$employee_id = $_POST['employee_id'];
mysql_query("DELETE FROM `users` WHERE `id`='".$employee_id."'",$dbcnx);
echo "удалено";
?>