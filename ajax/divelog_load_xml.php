<?php

    require_once 'config.php';

    $email = (isset($_REQUEST['email']) && $_REQUEST['email'] != '') ? $_REQUEST['email'] : '';
    if($email == '') { print "ERROR: email is empty"; exit(); }

    $sql  = "SELECT id, email FROM users WHERE email='";
    $sql .= $dbconn->real_escape_string($email) . "' AND deleted='N'";
    $res = $dbconn->query($sql);
    if(!$res) {
        print "ERROR: Query Failed: error: " . $dbconn->error;
        exit();
    }

    if($res->num_rows < 1) { print "ERROR: Email='$email' not found.\nNot logged in."; exit(); }
    else {
        $sql  = "SELECT * FROM divelog WHERE email='";
        $sql .= $dbconn->real_escape_string($email) . "' AND deleted='N' ORDER BY dive_no";
        $res = $dbconn->query($sql);

        $xmldata  = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
        $xmldata .= "<divelog>\n\n";
        $xmldata .= "    <email>$email</email>\n\n";

        while($row = $res->fetch_assoc()) {
            $xmldata .= "    <dive>\n";
            $xmldata .= "        <dive_no>" . $row['dive_no'] . "</dive_no>\n";
            $xmldata .= "        <dive_date>" . $row['dive_date'] . "</dive_date>\n";
            $xmldata .= "        <location><![CDATA[" . $row['location'] . "]]></location>\n";
            $xmldata .= "        <site_name><![CDATA[" . $row['site_name'] . "]]></site_name>\n";
            $xmldata .= "        <time_in><![CDATA[" . $row['time_in'] . "]]></time_in>\n";
            $xmldata .= "        <time_out><![CDATA[" . $row['time_out'] . "]]></time_out>\n";
            $xmldata .= "        <air_temp>" . $row['air_temp'] . "</air_temp>\n";
            $xmldata .= "        <bottom_temp>" . $row['bottom_temp'] . "</bottom_temp>\n";
            $xmldata .= "        <begin_psi>" . $row['begin_psi'] . "</begin_psi>\n";
            $xmldata .= "        <end_psi>" . $row['end_psi'] . "</end_psi>\n";
            $xmldata .= "        <viz>" . $row['viz'] . "</viz>\n";
            $xmldata .= "        <weight>" . $row['weight'] . "</weight>\n";
            $xmldata .= "        <salt>" . $row['salt'] . "</salt>\n";
            $xmldata .= "        <fresh>" . $row['fresh'] . "</fresh>\n";
            $xmldata .= "        <boat>" . $row['boat'] . "</boat>\n";
            $xmldata .= "        <shore>" . $row['shore'] . "</shore>\n";
            $xmldata .= "        <surge>" . $row['surge'] . "</surge>\n";
            $xmldata .= "        <waves>" . $row['waves'] . "</waves>\n";
            $xmldata .= "        <wetsuit>" . $row['wetsuit'] . "</wetsuit>\n";
            $xmldata .= "        <drysuit>" . $row['drysuit'] . "</drysuit>\n";
            $xmldata .= "        <hood>" . $row['hood'] . "</hood>\n";
            $xmldata .= "        <gloves>" . $row['gloves'] . "</gloves>\n";
            $xmldata .= "        <boots>" . $row['boots'] . "</boots>\n";
            $xmldata .= "        <vest>" . $row['vest'] . "</vest>\n";
            $xmldata .= "        <computer>" . $row['computer'] . "</computer>\n";
            $xmldata .= "        <computer_desc><![CDATA[" . $row['computer_desc'] . "]]></computer_desc>\n";
            $xmldata .= "        <eanx>" . $row['eanx'] . "</eanx>\n";
            $xmldata .= "        <eanx_percent><![CDATA[" . $row['eanx_percent'] . "]]></eanx_percent>\n";
            $xmldata .= "        <rnt>" . $row['rnt'] . "</rnt>\n";
            $xmldata .= "        <abt>" . $row['abt'] . "</abt>\n";
            $xmldata .= "        <tbt>" . $row['tbt'] . "</tbt>\n";    
            $xmldata .= "        <si>" . $row['si'] . "</si>\n";
            $xmldata .= "        <begin_pg>" . $row['begin_pg'] . "</begin_pg>\n";
            $xmldata .= "        <end_pg>" . $row['end_pg'] . "</end_pg>\n";
            $xmldata .= "        <depth>" . $row['depth'] . "</depth>\n";
            $xmldata .= "        <safety_stop>" . $row['safety_stop'] . "</safety_stop>\n";
            $xmldata .= "        <bottom_time>" . $row['bottom_time'] . "</bottom_time>\n";
            $xmldata .= "        <comments><![CDATA[" . $row['comments'] . "]]></comments>\n";
            $xmldata .= "        <timestamp>" . $row['timestamp'] . "</timestamp>\n";
            $xmldata .= "    </dive>\n\n";
        }
    }

    $xmldata .= "</divelog>\n";

    print $xmldata;

?>
