<?php

require_once('config.php');
require_once('classes/db_helper.php');

$email = (isset($_REQUEST['email']) && $_REQUEST['email'] != '') ? $_REQUEST['email'] : '';
if($email == '') { echo "ERROR: email is empty"; exit(); }

$db_helper = new DBHelper($dbconn);
$sql  = "SELECT dive_no FROM divelog WHERE email=? ";
$sql .= "AND deleted='N' ORDER BY dive_no DESC LIMIT 1";
$sql = $db_helper->construct_secure_query($sql, $email);
$res = $dbconn->query($sql);
if(!$res) {
    echo "ERROR: Query Failed: errno: " . $dbconn->errno . " error: " . $dbconn->error;
    exit();
}

$row_cnt = $res->num_rows;

if($row_cnt < 1) { echo "1"; exit(); }

$row = $res->fetch_assoc();
$next_diveno = $row['dive_no'] +1;
echo $next_diveno;
exit(0);

?>
