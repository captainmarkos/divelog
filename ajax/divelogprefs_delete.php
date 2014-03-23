<?php

require_once('config.php');
require_once('classes/db_helper.php');

$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
$dive_no = isset($_REQUEST['dive_no']) ? $_REQUEST['dive_no'] : '';
if(!$email) { echo "ERROR: email is empty - divelog_delete.php"; exit(); }
if(!$dive_no) { echo "ERROR: dive_no is empty - divelog_delete.php"; exit(); }

$db_helper = new DBHelper($dbconn);
$sql  = "UPDATE divelog SET deleted='Y' WHERE email=? ";
$sql .= "AND dive_no=?";
$sql = $db_helper->construct_secure_query($sql, array($email, $dive_no));
$res = $dbconn->query($sql);
if(!$res) {
    echo "ERROR: Update failed: " . $dbconn->error;
    echo "\n\n$sql\n";
    exit();
}

echo "Dive# $dive_no has been deleted";
exit();

?>

