<?php

    // This program will return the divelogprefs data in XML format.
    //
    // If no joining divelogprefs record is found, then one is created
    // with default values and the XML is returned.

    require_once 'config.php';

    $email = (isset($_REQUEST['email']) && $_REQUEST['email'] != '') ? $_REQUEST['email'] : '';
    if($email == '') { print "ERROR: email is empty"; exit(); }

    $sql  = "SELECT divelogprefs.id AS prefs_id, ";
    $sql .= "       divelogprefs.user_id AS prefs_user_id, ";
    $sql .= "       divelogprefs.distance AS prefs_distance, ";
    $sql .= "       divelogprefs.weight AS prefs_weight, ";
    $sql .= "       divelogprefs.temperature AS prefs_temp, ";
    $sql .= "       divelogprefs.pressure AS prefs_pressure, ";
    $sql .= "       divelogprefs.cert_level AS prefs_cert_level, ";
    $sql .= "       divelogprefs.cert_agency AS prefs_cert_agency, ";
    $sql .= "       users.id AS user_id, ";
    $sql .= "       users.fname AS user_fname, ";
    $sql .= "       users.lname AS user_lname, ";
    $sql .= "       users.email AS user_email ";
    $sql .= "FROM divelogprefs ";
    $sql .= "JOIN users ON divelogprefs.user_id=users.id ";
    $sql .= "WHERE users.email='" . $dbconn->real_escape_string($email) . "'";
    $res = $dbconn->query($sql);
    if(!$res) { print "ERROR: 1: Query Failed: error-> " . $dbconn->error; exit(); }

    if($res->num_rows < 1) {
        // No record was found so let's create an initial record.
        $sqlnew  = "SELECT id FROM users WHERE email='" . $dbconn->real_escape_string($email) . "'";
        $res = $dbconn->query($sqlnew);
        if($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $user_id = $row['id'];
            $sqlnew  = "INSERT INTO divelogprefs (user_id) VALUES ($user_id)";
            $res = $dbconn->query($sqlnew);
            if(!$res) { print "ERROR: Insert Failed: error-> " . $dbconn->error; exit(); }
        }
        // Now re-query the table to get the data so we can build the xml.
        $res = $dbconn->query($sql);
        if(!$res) { print "ERROR: 2: Query Failed: error-> " . $dbconn->error; exit(); }
    }

    if($res->num_rows < 1) { print "ERROR: No divelogprefs records found for email='$email'"; exit(); }
    $row = $res->fetch_assoc();

    $xmldata  = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
    $xmldata .= "<divelogprefs>\n";
    $xmldata .= "    <email>" . $row['user_email'] . "</email>\n";
    $xmldata .= "    <fname>" . $row['user_fname'] . "</fname>\n";
    $xmldata .= "    <lname>" . $row['user_lname'] . "</lname>\n";
    $xmldata .= "    <cert_level>" . $row['prefs_cert_level'] . "</cert_level>\n";
    $xmldata .= "    <cert_agency>" . $row['prefs_cert_agency'] . "</cert_agency>\n";
    $xmldata .= "    <distance>" . $row['prefs_distance'] . "</distance>\n";
    $xmldata .= "    <weight>" . $row['prefs_weight'] . "</weight>\n";
    $xmldata .= "    <temperature>" . $row['prefs_temp'] . "</temperature>\n";
    $xmldata .= "    <pressure>" . $row['prefs_pressure'] . "</pressure>\n";
    $xmldata .= "</divelogprefs>\n";


    print $xmldata;

?>
