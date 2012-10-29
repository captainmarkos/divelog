<?php

    require_once 'config.php';

    // Attempt to save a new dive into the divelog table.  The complete XML
    // of the dive to be logged is passed in and is base64 encoded.
    //


    $xmldata = (isset($_REQUEST['xmldata']) && $_REQUEST['xmldata'] != '') ? $_REQUEST['xmldata'] : '';
    if($xmldata == '') { print "ERROR: xmldata is empty - divelog_save.php"; exit(); }


    $xmlfile = 'divelog_' . date("Y-m-d_H-i-s") . '.xml';
    if(!($fp = fopen($xmlfile, "a"))) {
        print "ERROR: unable to open file \"$xmlfile\"\n"; exit(); }
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
                      'timestamp' => ''
	       );

    parse_xml($xmlfile);

    unlink($xmlfile);

    $sql  = "SELECT * FROM divelog WHERE email='"; 
    $sql .= $dbconn->real_escape_string($divedata['email']);
    $sql .= "' AND dive_no=" . $dbconn->real_escape_string($divedata['dive_no']);
    $res = $dbconn->query($sql);
    if(!$res) {
        print "ERROR: Query Failed: errno: " . $dbconn->errno . " error: " . $dbconn->error;
        exit();
    }

    $row_cnt = $res->num_rows;
    if($row_cnt >= 1) {
        // There is a dive logged for this email address and dive_no.  Maybe it has deleted == 'Y'?
        $row = $res->fetch_assoc();
  	if($row['deleted'] == 'Y') {
  	    $sql = "DELETE FROM divelog WHERE id=" . $row['id'];
            $res = $dbconn->query($sql);
            if(!$res) {
                print "ERROR: Delete Failed: " . $dbconn->error;
                exit();
            }
            DB_execute_insert($dbconn, $divedata);
            print "Saved new dive# " . $divedata['dive_no'] .
                  "\nLocation: " . $divedata['location'] .
                  "\nSite Name: " . $divedata['site_name'];  // NOTE: This text is parsed in divelog.js.
            exit();
        }
        else {
	    DB_execute_update($dbconn, $divedata);
            print "Updated dive# " . $divedata['dive_no'] .
                  "\nLocation: " . $divedata['location'] .
                  "\nSite Name: " . $divedata['site_name'];  // NOTE: This text is parsed in divelog.js.
            exit();
        }
    }
    else {
        DB_execute_insert($dbconn, $divedata);
        print "Saved new dive# " . $divedata['dive_no'] .
              "\nLocation: " . $divedata['location'] .
              "\nSite Name: " . $divedata['site_name'];  // NOTE: This text is parsed in divelog.js.
        exit();
    }

    // Terminating program successfully.
    print "Why did we get here?\n\nDive# " . $divedata['dive_no'];
    exit();


?>


<?php

function DB_execute_update($dbconn, $divedata) {
    // This is an update to an existing logged dive.
    $sql  = "UPDATE divelog SET ";
    $sql .= "dive_date='" . $dbconn->real_escape_string($divedata['dive_date']);
    $sql .= "', time_in='" . $dbconn->real_escape_string($divedata['time_in']);
    $sql .= "', time_out='" . $dbconn->real_escape_string($divedata['time_out']);
    $sql .= "', rnt=" . $dbconn->real_escape_string($divedata['rnt']);
    $sql .= ", abt=" . $dbconn->real_escape_string($divedata['abt']);
    $sql .= ", tbt=" . $dbconn->real_escape_string($divedata['tbt']);
    $sql .= ", air_temp='" . $dbconn->real_escape_string($divedata['air_temp']);
    $sql .= "', bottom_temp='" . $dbconn->real_escape_string($divedata['bottom_temp']);
    $sql .= "', begin_psi=" . $dbconn->real_escape_string($divedata['begin_psi']);
    $sql .= ", end_psi=" . $dbconn->real_escape_string($divedata['end_psi']);
    $sql .= ", viz='" . $dbconn->real_escape_string($divedata['viz']);
    $sql .= "', weight='" . $dbconn->real_escape_string($divedata['weight']);
    $sql .= "', salt='" . $dbconn->real_escape_string($divedata['salt']);
    $sql .= "', fresh='" . $dbconn->real_escape_string($divedata['fresh']);
    $sql .= "', shore='" . $dbconn->real_escape_string($divedata['shore']);
    $sql .= "', boat='" . $dbconn->real_escape_string($divedata['boat']);
    $sql .= "', waves='" . $dbconn->real_escape_string($divedata['waves']);
    $sql .= "', wetsuit='" . $dbconn->real_escape_string($divedata['wetsuit']);
    $sql .= "', drysuit='" . $dbconn->real_escape_string($divedata['drysuit']);
    $sql .= "', hood='" . $dbconn->real_escape_string($divedata['hood']);
    $sql .= "', gloves='" . $dbconn->real_escape_string($divedata['gloves']);
    $sql .= "', boots='" . $dbconn->real_escape_string($divedata['boots']);
    $sql .= "', surge='" . $dbconn->real_escape_string($divedata['surge']);
    $sql .= "', vest='" . $dbconn->real_escape_string($divedata['vest']);
    $sql .= "', location='" . $dbconn->real_escape_string($divedata['location']);
    $sql .= "', site_name='" . $dbconn->real_escape_string($divedata['site_name']);
    $sql .= "', si='" . $dbconn->real_escape_string($divedata['si']);
    $sql .= "', begin_pg='" . $dbconn->real_escape_string($divedata['begin_pg']);
    $sql .= "', end_pg='" . $dbconn->real_escape_string($divedata['end_pg']);
    $sql .= "', depth='" . $dbconn->real_escape_string($divedata['depth']);
    $sql .= "', safety_stop='" . $dbconn->real_escape_string($divedata['safety_stop']);
    $sql .= "', bottom_time=" . $dbconn->real_escape_string($divedata['bottom_time']);
    $sql .= ", computer='" . $dbconn->real_escape_string($divedata['computer']);
    $sql .= "', computer_desc='" . $dbconn->real_escape_string($divedata['computer_desc']);
    $sql .= "', eanx='" . $dbconn->real_escape_string($divedata['eanx']);
    $sql .= "', eanx_percent='" . $dbconn->real_escape_string($divedata['eanx_percent']);
    $sql .= "', comments='" . $dbconn->real_escape_string($divedata['comments']);
    $sql .= "' WHERE email='" . $dbconn->real_escape_string($divedata['email']);
    $sql .= "' AND dive_no=" . $dbconn->real_escape_string($divedata['dive_no']);

    $res = $dbconn->query($sql);
    if(!$res) {
        print "ERROR: Update failed: errno: " . $dbconn->error;
        print "\n\n$sql\n";
        exit();
    }
}


function DB_execute_insert($dbconn, $divedata) {
    // This is a new dive being logged, save a new record.
    $sql  = "INSERT INTO divelog (email, dive_no, dive_date, time_in, time_out, rnt, abt, tbt, ";
    $sql .= "air_temp, bottom_temp, begin_psi, end_psi, viz, weight, salt, fresh, shore, boat, ";
    $sql .= "waves, wetsuit, drysuit, hood, gloves, boots, surge, vest, location, site_name, ";
    $sql .= "si, begin_pg, end_pg, depth, safety_stop, bottom_time, computer, computer_desc, ";
    $sql .= "eanx, eanx_percent, comments) ";
    $sql .= "VALUES ('" . $dbconn->real_escape_string($divedata['email']);
    $sql .= "', " . $dbconn->real_escape_string($divedata['dive_no']);
    $sql .= ", '" . $dbconn->real_escape_string($divedata['dive_date']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['time_in']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['time_out']);
    $sql .= "', " . $dbconn->real_escape_string($divedata['rnt']);
    $sql .= ", " . $dbconn->real_escape_string($divedata['abt']);
    $sql .= ", " . $dbconn->real_escape_string($divedata['tbt']);
    $sql .= ", '" . $dbconn->real_escape_string($divedata['air_temp']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['bottom_temp']);
    $sql .= "', " . $dbconn->real_escape_string($divedata['begin_psi']);
    $sql .= ", " . $dbconn->real_escape_string($divedata['end_psi']);
    $sql .= ", '" . $dbconn->real_escape_string($divedata['viz']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['weight']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['salt']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['fresh']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['shore']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['boat']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['waves']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['wetsuit']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['drysuit']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['hood']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['gloves']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['boots']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['surge']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['vest']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['location']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['site_name']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['si']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['begin_pg']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['end_pg']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['depth']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['safety_stop']);
    $sql .= "', " . $dbconn->real_escape_string($divedata['bottom_time']);
    $sql .= ", '" . $dbconn->real_escape_string($divedata['computer']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['computer_desc']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['eanx']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['eanx_percent']);
    $sql .= "', '" . $dbconn->real_escape_string($divedata['comments']) . "')";

    $res = $dbconn->query($sql);
    if(!$res) {
        print "ERROR: Insert failed: " . $dbconn->error;
        print "\n\n$sql\n";
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
        print "ERROR: parse_xml(): could not open xml file: $infile";
        exit();
    }

    while($data = fread($fp, 8192))
    {
        if(!xml_parse($xml_parser, $data, feof($fp)))
        {
            print sprintf("ERROR: XML error: %s at line %d", 
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
    //print "curTag = $curTag\r\n    data = $data\r\n";

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
