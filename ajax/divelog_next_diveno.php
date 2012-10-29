<?php

    require_once 'config.php';


    $email = (isset($_REQUEST['email']) && $_REQUEST['email'] != '') ? $_REQUEST['email'] : '';
    if($email == '') { print "ERROR: email is empty"; exit(); }

    $sql  = "SELECT dive_no FROM divelog WHERE email='"; 
    $sql .= $dbconn->real_escape_string($email) . "' AND deleted='N' ORDER BY dive_no DESC LIMIT 1";
    $res = $dbconn->query($sql);
    if(!$res) {
        print "ERROR: Query Failed: errno: " . $dbconn->errno . " error: " . $dbconn->error;
        exit();
    }

    $row_cnt = $res->num_rows;

    if($row_cnt < 1) { print "1"; exit(); }

    $row = $res->fetch_assoc();
    $next_diveno = $row['dive_no'] +1;
    print $next_diveno;
    exit(0);

?>
