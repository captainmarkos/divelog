<?php

date_default_timezone_set('America/New_York');

// db properties
$dbhost = 'localhost';
$dbuser = 'xxxxx';
$dbpass = 'xxxxxx';
$dbname = 'xxxxxx';

// Using Mysqli - make a connection to the mysql database here.
$dbconn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if($dbconn->connect_errno) {
    print "ERROR: Failed to connect to MySQL: (" . $dbconn->connect_errno . ") " . $dbconn->connect_error;
    exit();
}

?>
