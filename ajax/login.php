<?php

    require_once 'config.php';

    // Attempt to delete an existing dive.
    //
    $email = (isset($_REQUEST['email']) && $_REQUEST['email'] != '') ? $_REQUEST['email'] : '';
    $passwd = isset($_POST['passwd']) && ($_POST['passwd'] != '') ? $_POST['passwd'] : '';

//$email = 'captainmarkos@gmail.com'; $passwd = 'mdbYkcul96';

    if($email == '')  { print "ERROR: email is missing - divelog_delete.php"; exit(); }
    if($passwd == '') { print "ERROR: passwd is missing - login.php"; exit(); }

    $sql  = "SELECT email FROM users WHERE email='" . $dbconn->real_escape_string($email) . "' ";
    $sql .= "AND passwd=PASSWORD('" . $dbconn->real_escape_string($passwd) . "') ";
    $sql .= "AND deleted='N'";
    $res = $dbconn->query($sql);
    if(!$res) {
        print "ERROR: Query failed: " . $dbconn->error;
        print "\n\n$sql\n";
        exit();
    }

    if($res->num_rows <= 0) {
        if(!good_email($dbconn, $email)) {
            print "ERROR: email does not exist";
        }
        elseif(!good_passwd($dbconn, $email, $passwd)) {
            print "ERROR: bad password";
	}
	else {
            print "ERROR: Invalid login";
	}
        exit();
    }

    print "SUCCESS: " . $email;
    exit();


function good_email($dbconn, $email) {
    $sql  ="SELECT id FROM users WHERE email='" . $dbconn->real_escape_string($email);
    $sql .= "' AND deleted='N'";
    $res = $dbconn->query($sql);
    if(!$res) { return(false); }
    if($res->num_rows <= 0) { return(false); }
    return(true);
}


function good_passwd($dbconn, $email, $passwd) {
    // To use this function properly we have to assume that the email
    // address does exists in the table.  Therefore this function should
    // be called after the good_email() function.

    $sql  ="SELECT id FROM users WHERE email='" . $dbconn->real_escape_string($email) . "' ";
    $sql .= "AND passwd=PASSWORD('" . $dbconn->real_escape_string($passwd) . "') ";
    $sql .= "AND deleted='N'";
    $res = $dbconn->query($sql);
    if(!$res) { return(false); }
    if($res->num_rows <= 0) { return(false); }
    return(true);
}

?>

