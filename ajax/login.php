<?php

require_once('config.php');
require_once('../classes/db_helper.php');

$email = (isset($_REQUEST['email']) && $_REQUEST['email'] != '') ? $_REQUEST['email'] : '';
$passwd = isset($_REQUEST['passwd']) && ($_REQUEST['passwd'] != '') ? $_REQUEST['passwd'] : '';

if(!$email || !$passwd) {
    echo "ERROR: email is missing - divelog_delete.php"; exit();
}

$db_helper = new DBHelper($dbconn);

$params = array($email, $passwd);
$sql  = "SELECT email FROM users WHERE email=? ";
$sql .= "AND passwd=PASSWORD(?) AND deleted='N'";

$sql = $db_helper->construct_secure_query($sql, $params);
$res = $dbconn->query($sql);
if($res->num_rows < 1) {
    if(!good_email($dbconn, $email)) {
        echo "ERROR: email does not exist";
    }
    elseif(!good_passwd($dbconn, $email, $passwd)) {
        echo "ERROR: bad password";
    }
    else {
        echo "ERROR: Invalid login";
    }
    exit();
}

echo "SUCCESS: " . $email;
session_start();
$_SESSION['email'] = $email;
exit();


function good_email($dbconn, $email) {
    $db_helper = new DBHelper($dbconn);
    $sql ="SELECT id FROM users WHERE email=? AND deleted='N'";
    $sql = $db_helper->construct_secure_query($sql, $email);
    $res = $dbconn->query($sql);
    if($res->num_rows < 1) { return(false); }
    return(true);
}

function good_passwd($dbconn, $email, $passwd) {
    // To use this function properly we have to assume that the email
    // address does exists in the table.  Therefore this function should
    // be called after the good_email() function.
    $db_helper = new DBHelper($dbconn);
    $sql  ="SELECT id FROM users WHERE email=? ";
    $sql .= "AND passwd=PASSWORD(?) AND deleted='N'";

    $sql = $db_helper->construct_secure_query($sql, array($email, $passwd));
    $res = $dbconn->query($sql);
    if($res->num_rows < 1) { return(false); }
    return(true);
}

?>
