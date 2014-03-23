<?php

require_once('config.php');
require_once('../classes/db_helper.php');

// Attempt to save a new dive into the divelog table.  The complete XML
// of the dive to be logged is passed in and is base64 encoded.

$xmldata = isset($_REQUEST['xmldata']) ? $_REQUEST['xmldata'] : '';
if(!$xmldata) { echo "ERROR: xmldata is empty - divelog_save.php"; exit(); }


$xmlfile = 'divelog_' . date("Y-m-d_H-i-s") . '.xml';
if(!($fp = fopen($xmlfile, "a"))) {
    echo "ERROR: unable to open file \"$xmlfile\"\n"; exit(); }
fwrite($fp, base64_decode($xmldata));
fclose($fp);

$divedata = array('email' => '',
                  'dive_no' => '',
                  'dive_date' => '',
                  'location' => '',
                  'site_name' => '',
                  'time_in' => '',
                  'time_out' => '',
                  'air_temp' => '',
                  'bottom_temp' => '',
                  'begin_psi' => '',
                  'end_psi' => '',
                  'viz' => '',
                  'weight' => '',
                  'salt' => '',
                  'fresh' => '',
                  'boat' => '',
                  'shore' => '',
                  'surge' => '',
                  'waves' => '',
                  'wetsuit' => '',
                  'drysuit' => '',
                  'hood' => '',
                  'gloves' => '',
                  'boots' => '',
                  'vest' => '',
                  'computer' => '',
                  'computer_desc' => '',
                  'eanx' => '',
                  'eanx_percent' => '',
                  'rnt' => '',
                  'abt' => '',
                  'tbt' => '',
                  'si' => '',
                  'begin_pg' => '',
                  'end_pg' => '',
                  'depth' => '',
                  'safety_stop' => '',
                  'bottom_time' => '',
                  'comments' => '',
                  'timestamp' => '');

parse_xml($xmlfile);

unlink($xmlfile);
$db_helper = new DBHelper($dbconn);

$sql  = "SELECT * FROM divelog WHERE email=? AND dive_no=?";
$params = array($divedata['email'], $divedata['dive_no']);
$sql = $db_helper->construct_secure_query($sql, $params);
$res = $dbconn->query($sql);
if(!$res) {
    echo "ERROR: Query Failed: errno: " . $dbconn->errno . " error: " . $dbconn->error;
    exit();
}

$row_cnt = $res->num_rows;
if($row_cnt >= 1) {
    // There is a dive logged for this email address and dive_no.  Maybe it has deleted == 'Y'?
    $row = $res->fetch_assoc();
    if($row['deleted'] == 'Y') {
        $sql = "DELETE FROM divelog WHERE id=?";
        $sql = $db_helper->construct_secure_query($sql, $row['id']);
        $res = $dbconn->query($sql);
        if(!$res) {
            echo "ERROR: Delete Failed: " . $dbconn->error;
            exit();
        }
        execute_insert($dbconn, $divedata);
        echo "Saved new dive# " . $divedata['dive_no'] .
              "\nLocation: " . $divedata['location'] .
              "\nSite Name: " . $divedata['site_name'];  // NOTE: This text is parsed in divelog.js.
        exit();
    }
    else {
        execute_update($dbconn, $divedata);
        echo "Updated dive# " . $divedata['dive_no'] .
              "\nLocation: " . $divedata['location'] .
              "\nSite Name: " . $divedata['site_name'];  // NOTE: This text is parsed in divelog.js.
        exit();
    }
}
else {
    execute_insert($dbconn, $divedata);
    echo "Saved new dive# " . $divedata['dive_no'] .
         "\nLocation: " . $divedata['location'] .
         "\nSite Name: " . $divedata['site_name'];  // NOTE: This text is parsed in divelog.js.
    exit();
}

// Terminating program successfully.
echo "Why did we get here?\n\nDive# " . $divedata['dive_no'];
exit();


function execute_update($dbconn, $divedata) {
    // This is an update to an existing logged dive.
    $db_helper = new DBHelper($dbconn);
    $params = array($divedata['dive_date'], $divedata['time_in'],
                    $divedata['time_out'], $divedata['rnt'], $divedata['abt'],
                    $divedata['tbt'], $divedata['air_temp'],
                    $divedata['bottom_temp'], $divedata['begin_psi'],
                    $divedata['end_psi'], $divedata['viz'], $divedata['weight'],
                    $divedata['salt'], $divedata['fresh'], $divedata['shore'],
                    $divedata['boat'], $divedata['waves'], $divedata['wetsuit'],
                    $divedata['drysuit'], $divedata['hood'], $divedata['gloves'],
                    $divedata['boots'], $divedata['surge'], $divedata['vest'],
                    $divedata['location'], $divedata['site_name'], $divedata['si'],
                    $divedata['begin_pg'], $divedata['end_pg'], $divedata['depth'],
                    $divedata['safety_stop'], $divedata['bottom_time'],
                    $divedata['computer'], $divedata['computer_desc'],
                    $divedata['eanx'], $divedata['eanx_percent'],
                    $divedata['comments'], $divedata['email'], $divedata['dive_no']);

    $sql  = "UPDATE divelog SET ";
    $sql .= "dive_date=?, time_in=?, time_out=?, rnt=?, abt=?, tbt=?, air_temp=?, ";
    $sql .= "bottom_temp=?,  begin_psi=?, end_psi=?, viz=?, weight=?, salt=?, ";
    $sql .= "fresh=?, shore=?, boat=?, waves=?, wetsuit=?, drysuit=?, hood=?, ";
    $sql .= "gloves=?, boots=?, surge=?, vest=?, location=?, site_name=?, ";
    $sql .= "si=?, begin_pg=?, end_pg=?, depth=?, safety_stop=?, bottom_time=?, ";
    $sql .= "computer=?, computer_desc=?, eanx=?, eanx_percent=?, comments=? ";
    $sql .= "WHERE email=? AND dive_no=?";

    $sql = $db_helper->construct_secure_query($sql, $params);
    $res = $dbconn->query($sql);
    if(!$res) {
        echo "ERROR: Update failed: errno: " . $dbconn->error;
        echo "\n\n$sql\n";
        exit();
    }
}


function execute_insert($dbconn, $divedata) {
    // This is a new dive being logged, save a new record.
    $db_helper = new DBHelper($dbconn);

    $sql  = "INSERT INTO divelog (email, dive_no, dive_date, time_in, time_out, rnt, abt, tbt, ";
    $sql .= "air_temp, bottom_temp, begin_psi, end_psi, viz, weight, salt, fresh, shore, boat, ";
    $sql .= "waves, wetsuit, drysuit, hood, gloves, boots, surge, vest, location, site_name, ";
    $sql .= "si, begin_pg, end_pg, depth, safety_stop, bottom_time, computer, computer_desc, ";
    $sql .= "eanx, eanx_percent, comments) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ";
    $sql .= "        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ";
    $sql .= "        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ";
    $sql .= "        ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = array($divedata['email'], $divedata['dive_no'], $divedata['dive_date'],
                    $divedata['time_in'], $divedata['time_out'],
                    $divedata['rnt'], $divedata['abt'], $divedata['tbt'],
                    $divedata['air_temp'], $divedata['bottom_temp'],
                    $divedata['begin_psi'], $divedata['end_psi'], $divedata['viz'],
                    $divedata['weight'], $divedata['salt'], $divedata['fresh'],
                    $divedata['shore'], $divedata['boat'], $divedata['waves'],
                    $divedata['wetsuit'], $divedata['drysuit'], $divedata['hood'],
                    $divedata['gloves'], $divedata['boots'], $divedata['surge'],
                    $divedata['vest'], $divedata['location'], $divedata['site_name'],
                    $divedata['si'], $divedata['begin_pg'], $divedata['end_pg'],
                    $divedata['depth'], $divedata['safety_stop'],
                    $divedata['bottom_time'], $divedata['computer'],
                    $divedata['computer_desc'], $divedata['eanx'],
                    $divedata['eanx_percent'], $divedata['comments']);
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
    global $divedata;
    $emailKey = "^divelog^email";
    $dive_noKey = "^divelog^dive^dive_no";
    $dive_dateKey = "^divelog^dive^dive_date";
    $locationKey = "^divelog^dive^location";
    $site_nameKey = "^divelog^dive^site_name";
    $time_inKey = "^divelog^dive^time_in";
    $time_outKey = "^divelog^dive^time_out";
    $air_tempKey = "^divelog^dive^air_temp";
    $bottom_tempKey = "^divelog^dive^bottom_temp";
    $begin_psiKey = "^divelog^dive^begin_psi";
    $end_psiKey = "^divelog^dive^end_psi";
    $vizKey = "^divelog^dive^viz";
    $weightKey = "^divelog^dive^weight";
    $saltKey = "^divelog^dive^salt";
    $freshKey = "^divelog^dive^fresh";
    $boatKey = "^divelog^dive^boat";
    $shoreKey = "^divelog^dive^shore";
    $surgeKey = "^divelog^dive^surge";
    $wavesKey = "^divelog^dive^waves";
    $wetsuitKey = "^divelog^dive^wetsuit";
    $drysuitKey = "^divelog^dive^drysuit";
    $hoodKey = "^divelog^dive^hood";
    $glovesKey = "^divelog^dive^gloves";
    $bootsKey = "^divelog^dive^boots";
    $vestKey = "^divelog^dive^vest";
    $computerKey = "^divelog^dive^computer";
    $computer_descKey = "^divelog^dive^computer_desc";
    $eanxKey = "^divelog^dive^eanx";
    $eanx_percentKey = "^divelog^dive^eanx_percent";
    $rntKey = "^divelog^dive^rnt";
    $abtKey = "^divelog^dive^abt";
    $tbtKey = "^divelog^dive^tbt";
    $siKey = "^divelog^dive^si";
    $begin_pgKey = "^divelog^dive^begin_pg";
    $end_pgKey = "^divelog^dive^end_pg";
    $depthKey = "^divelog^dive^depth";
    $safety_stopKey = "^divelog^dive^safety_stop";
    $bottom_timeKey = "^divelog^dive^bottom_time";
    $commentsKey = "^divelog^dive^comments";
    $timestampKey = "^divelog^dive^timestamp";

    $curTag = strtolower($curTag);
    //echo "curTag = $curTag\r\n    data = $data\r\n";

    if($curTag == $emailKey)             { $divedata['email'] = $data; }
    elseif($curTag == $dive_noKey)       { $divedata['dive_no'] = $data; }
    elseif($curTag == $dive_dateKey)     { $divedata['dive_date'] = $data; }
    elseif($curTag == $locationKey)      { $divedata['location'] = $data; }
    elseif($curTag == $site_nameKey)     { $divedata['site_name'] = $data; }
    elseif($curTag == $time_inKey)       { $divedata['time_in'] = $data; }
    elseif($curTag == $time_outKey)      { $divedata['time_out'] = $data; }
    elseif($curTag == $air_tempKey)      { $divedata['air_temp'] = $data; }
    elseif($curTag == $bottom_tempKey)   { $divedata['bottom_temp'] = $data; }
    elseif($curTag == $begin_psiKey)     { $divedata['begin_psi'] = $data; }
    elseif($curTag == $end_psiKey)       { $divedata['end_psi'] = $data; }
    elseif($curTag == $vizKey)           { $divedata['viz'] = $data; }
    elseif($curTag == $weightKey)        { $divedata['weight'] = $data; }
    elseif($curTag == $saltKey)          { $divedata['salt'] = $data; }
    elseif($curTag == $freshKey)         { $divedata['fresh'] = $data; }
    elseif($curTag == $boatKey)          { $divedata['boat'] = $data; }
    elseif($curTag == $shoreKey)         { $divedata['shore'] = $data; }
    elseif($curTag == $surgeKey)         { $divedata['surge'] = $data; }
    elseif($curTag == $wavesKey)         { $divedata['waves'] = $data; }
    elseif($curTag == $wetsuitKey)       { $divedata['wetsuit'] = $data; }
    elseif($curTag == $drysuitKey)       { $divedata['drysuit'] = $data; }
    elseif($curTag == $hoodKey)          { $divedata['hood'] = $data; }
    elseif($curTag == $glovesKey)        { $divedata['gloves'] = $data; }
    elseif($curTag == $bootsKey)         { $divedata['boots'] = $data; }
    elseif($curTag == $vestKey)          { $divedata['vest'] = $data; }
    elseif($curTag == $computerKey)      { $divedata['computer'] = $data; }
    elseif($curTag == $computer_descKey) { $divedata['computer_desc'] = $data; }
    elseif($curTag == $eanxKey)          { $divedata['eanx'] = $data; }
    elseif($curTag == $eanx_percentKey)  { $divedata['eanx_percent'] = $data; }
    elseif($curTag == $rntKey)           { $divedata['rnt'] = $data; }
    elseif($curTag == $abtKey)           { $divedata['abt'] = $data; }
    elseif($curTag == $tbtKey)           { $divedata['tbt'] = $data; }
    elseif($curTag == $siKey)            { $divedata['si'] = $data; }
    elseif($curTag == $begin_pgKey)      { $divedata['begin_pg'] = $data; }
    elseif($curTag == $end_pgKey)        { $divedata['end_pg'] = $data; }
    elseif($curTag == $depthKey)         { $divedata['depth'] = $data; }
    elseif($curTag == $safety_stopKey)   { $divedata['safety_stop'] = $data; }
    elseif($curTag == $bottom_timeKey)   { $divedata['bottom_time'] = $data; }
    elseif($curTag == $commentsKey)      { $divedata['comments'] = $data; }
    elseif($curTag == $timestampKey)     { $divedata['timestamp'] = $data; }
} 

?>
