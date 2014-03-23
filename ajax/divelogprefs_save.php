<?php

require_once('config.php');
require_once('classes/db_helper.php');

// Attempt to save a new dive into the divelog table.  The complete XML
// of the dive to be logged is passed in and is base64 encoded.

$xmldata = isset($_REQUEST['xmldata']) ? $_REQUEST['xmldata'] : '';
if(!$xmldata) { echo "ERROR: xmldata is empty - divelogprefs_save.php"; exit(); }

$xmlfile = 'divelogprefs_' . date("Y-m-d_H-i-s") . '.xml';
if(!($fp = fopen($xmlfile, "a"))) {
    echo "ERROR: unable to open file \"$xmlfile\"\n"; exit(); }
fwrite($fp, base64_decode($xmldata));
fclose($fp);

$diveprefs = array('id' => '',
                   'email' => '',
                   'fname' => '',
                   'lname' => '',
                   'cert_level' => '',
                   'cert_agency' => '',
                   'distance' => '',
                   'weight' => '',
                   'temperature' => '',
                   'pressure' => '');

parse_xml($xmlfile);

unlink($xmlfile);

$db_helper = new DBHelper($dbconn);
$sql  = "SELECT dp.id AS id, dp.deleted FROM divelogprefs AS dp ";
$sql .= "JOIN users AS u ON dp.user_id=u.id ";
$sql .= "WHERE u.email=?";
$sql = $db_helper->construct_secure_query($sql, $diveprefs['email']);
$res = $dbconn->query($sql);
if(!$res) {
    echo "ERROR: 1: Query Failed: " . $dbconn->error . "\nSQL: $sql";
    exit();
}

$row_cnt = $res->num_rows;
if($row_cnt >= 1) {
    // There are preferences for this user.  Maybe it has deleted == 'Y'?
    $row = $res->fetch_assoc();
    $diveprefs['id'] = $row['id'];

    if($row['deleted'] == 'Y') {
        $sql = "DELETE FROM divelogprefs WHERE id=?";
        $sql = $db_helper->construct_secure_query($sql, $row['id']);
        $res = $dbconn->query($sql);
        if(!$res) {
            echo "2: ERROR: Delete Failed: " . $dbconn->error;
            exit();
        }
        execute_insert($dbconn, $diveprefs);  // Insert a default
        echo "Preferences saved.";  // for " . $diveprefs['email'];
        exit();
    }
    else {
        execute_update($dbconn, $diveprefs);
        echo "Preferences updated."; // for " . $diveprefs['email'];
        exit();
    }
}
else {
    execute_insert($dbconn, $diveprefs);
    echo "Preferences saved.";  // for " . $diveprefs['email'];
    exit();
}

// Terminating program successfully.
echo "Why did we get here?\n\nDive# " . $diveprefs['dive_no'];
exit();


function execute_update($dbconn, $diveprefs) {
    // This is an update to an existing logged dive.
    $db_helper = new DBHelper($dbconn);
    $sql  = "UPDATE divelogprefs SET ";
    $sql .= "distance=?, weight=?, temperature=?, pressure=?, cert_level=?, ";
    $sql .= "cert_agency=? WHERE id=?";

    $params = array($diveprefs['distance'], $diveprefs['weight'],
                    $diveprefs['temperature'], $diveprefs['pressure'],
                    $diveprefs['cert_level'], $diveprefs['cert_agency'],
                    $diveprefs['id']);
    $sql = $db_helper->construct_secure_query($sql, $params);
    $res = $dbconn->query($sql);
    if(!$res) {
        echo "ERROR: Update failed: errno: " . $dbconn->error;
        echo "\n\n$sql\n";
        exit();
    }
}


function execute_insert($dbconn, $diveprefs) {
    // This is a new dive being logged, save a new record.
    $db_helper = new DBHelper($dbconn);
    $sql  = "INSERT INTO divelogprefs (user_id, distance, weight, temperature, pressure, ";
    $sql .= "cert_level, cert_agency) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params = array($diveprefs['user_id']$diveprefs['distance'],
                    $diveprefs['weight'], $diveprefs['temperature'],
                    $diveprefs['pressure'], $diveprefs['cert_level'],
                    $diveprefs['cert_agency']);
    $sql = $db_helper->construct_secure_query($sql, $params);
    $res = $dbconn->query($sql);
    if(!$res) {
        echo "ERROR: Insert failed: " . $dbconn->error;
        echo "\n\n$sql\n";
        exit();
    }
}


function parse_xml($infile)
{
    $xml_parser = xml_parser_create();
    xml_set_element_handler($xml_parser, "startElement", "endElement");
    xml_set_character_data_handler($xml_parser, "characterData");

    if(!($fp = fopen($infile, "r")))
    {
        echo "ERROR: parse_xml(): could not open xml file: $infile";
        exit();
    }

    while($data = fread($fp, 8192))
    {
        if(!xml_parse($xml_parser, $data, feof($fp)))
        {
            echo sprintf("ERROR: XML error: %s at line %d",
			  xml_error_string(xml_get_error_code($xml_parser)), 
			  xml_get_current_line_number($xml_parser));
            exit();
        }
    }
    fclose($fp);
    xml_parser_free($xml_parser);
}


function startElement($parser, $name, $attrs)
{
    global $curTag;
    $curTag .= "^$name";
}


function endElement($parser, $name)
{
    global $curTag;
    $caret_pos = strrpos($curTag, '^');
    $curTag = substr($curTag, 0, $caret_pos);
}


function characterData($parser, $data) 
{ 
    global $curTag; // get the seaxp_reservation_data
    global $diveprefs;
    $emailKey = "^divelogprefs^email";
    $fnameKey = "^divelogprefs^fname";
    $lnameKey = "^divelogprefs^lname";
    $weightKey = "^divelogprefs^weight";
    $distanceKey = "^divelogprefs^distance";
    $temperatureKey = "^divelogprefs^temperature";
    $pressureKey = "^divelogprefs^pressure";
    $cert_levelKey = "^divelogprefs^cert_level";
    $cert_agencyKey = "^divelogprefs^cert_agency";

    $curTag = strtolower($curTag);
    //echo "curTag = $curTag\r\n    data = $data\r\n";

    if($curTag == $emailKey)           { $diveprefs['email'] = $data; }
    elseif($curTag == $fnameKey)       { $diveprefs['fname'] = $data; }
    elseif($curTag == $lnameKey)       { $diveprefs['lname'] = $data; }
    elseif($curTag == $weightKey)      { $diveprefs['weight'] = $data; }
    elseif($curTag == $distanceKey)    { $diveprefs['distance'] = $data; }
    elseif($curTag == $temperatureKey) { $diveprefs['temperature'] = $data; }
    elseif($curTag == $pressureKey)    { $diveprefs['pressure'] = $data; }
    elseif($curTag == $cert_levelKey)  { $diveprefs['cert_level'] = $data; }
    elseif($curTag == $cert_agencyKey) { $diveprefs['cert_agency'] = $data; }
} 

?>
