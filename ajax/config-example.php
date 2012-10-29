<?php

    // db properties
    $dbhost = 'DB_HOSTNAME';
    $dbuser = 'DB_USERNAME';
    $dbpass = 'DB_PASSWORD';
    $dbname = 'DB_NAME';

    // Using Mysqli - make a connection to the mysql database here.
    $dbconn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if($dbconn->connect_errno) {
        print "ERROR: Failed to connect to MySQL: (" . $dbconn->connect_errno . ") " . $dbconn->connect_error;
        exit();
    }

?>
