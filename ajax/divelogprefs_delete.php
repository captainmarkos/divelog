<?php

    require_once 'config.php';

    // Attempt to delete an existing dive.
    //
    $email = (isset($_REQUEST['email']) && $_REQUEST['email'] != '') ? $_REQUEST['email'] : '';
    $dive_no = (isset($_REQUEST['dive_no']) && $_REQUEST['dive_no'] != '') ? $_REQUEST['dive_no'] : '';
    if($email == '') { print "ERROR: email is empty - divelog_delete.php"; exit(); }
    if($dive_no == '') { print "ERROR: dive_no is empty - divelog_delete.php"; exit(); }

    $sql  = "UPDATE divelog SET deleted='Y' WHERE email='";
    $sql .= $dbconn->real_escape_string($email) . "' AND dive_no=" . $dbconn->real_escape_string($dive_no);

    $res = $dbconn->query($sql);
    if(!$res) {
        print "ERROR: Update failed: " . $dbconn->error;
        print "\n\n$sql\n";
        exit();
    }

    print "Dive# $dive_no has been deleted";
    exit();

?>

