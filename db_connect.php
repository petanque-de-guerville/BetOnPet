<?php

$db_user = 'tietokone.haarukka';
$db_pass = 'timinou';
$db_host = 'sql.free.fr';
$db_name = 'tietokone_haarukka';


mysql_connect($db_host, $db_user, $db_pass);
mysql_query("SET NAMES 'UTF8'");
mysql_select_db($db_name);

// we write this later on, ignore for now.

include('check_login.php');

?>