// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// divelog.js        Mark Brettin    JULY 2012
//
// The divelog.html is the main form.  All other forms are activated from
// the main divelog form.
//
// When the main divelog form is initially displayed, it is populated
// with data from database (retrived via ajax call in load_xml().
// Only the 'Edit' button is displayed after startup.  When 'Edit' is
// clicked, it is hidden and 'Save' and 'Delete' buttons are shown. 
// In addition, while in edit mode, if the 'Previous' or 'Next' buttons
// are clicked, they take the user out of edit mode, hide 'Save' and 
// 'Delete' and show 'Edit'.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


var fold_speed = 300;                 // speed at which a form is displayed
var divelog_display = true;           // display status of divelog form
var divelog_listing_display = false;  // display status of divelog_listing form
var divelog_settings_display = false; // display status of divelog_settings form
var divelog_help_display = false;     // display status of divelog_help form
var curr_dive_index = 0;              // index into the dives array; current dive being displayed
var save_curr_dive_index = 0;         // used to preserve the value of curr_dive_index
var dives = new Array();              // an array of associative arrays containing all logged dives
var prefs = new Array();              // an array of associative arrays containing diverlog preferences

var email = 'captainmarkos@gmail.com';


$(document).ready(function() {

    $('#dive_date').datepicker({ dateFormat: "yy-mm-dd" });  // Display the date in ISO format
    $('#divelog_listing').hide();
    $('#divelog_settings').hide();
    $('#divelog_help').hide();

    load_preferences_xml();
    load_xml();


    //
    // Setup click event triggers
    //
    $('#divelog_listing_open').click(function() {
        if(!divelog_listing_display) {
            $('#divelog').hide();

            // Since load_xml() was called above, we can loop
            // through the dives array to display logged dives.
            save_curr_dive_index = curr_dive_index;
            display_listing_rows();
	    $('#divelog_listing').show('fold', {}, fold_speed);
            $('#divelog_listing_info').html('3500 hours spent underwater!');
            divelog_listing_display = true;
            divelog_display = false;
        }
    });


    $('#divelog_listing_close').click(function() { divelog_listing_close(); });


    $('#divelog_settings_open').click(function() {
        if(!divelog_settings_display) { 
            $('#divelog').hide(); 
            $('#divelog_settings').show('fold', {}, fold_speed); 
            $('#divelog_settings_info').html('Tribal War ina Babylon');
            divelog_settings_display = true;  
            divelog_display = false;
            display_settings();
        }
    });


    $('#divelog_settings_close').click(function() {
        if(divelog_settings_display) {
            $('#divelog_settings').hide(); 
            $('#divelog').show('fold', {}, fold_speed); 
            divelog_settings_display = false; 
            divelog_display = true;
        }
    });


    $('#divelog_help_open').click(function() {
        if(!divelog_help_display) { 
            $('#divelog').hide(); 
            $('#divelog_help').show('fold', {}, fold_speed); 
            divelog_help_display = true;  
            divelog_display = false;
        }
    });


    $('#divelog_help_close').click(function() {
        if(divelog_help_display) {
            $('#divelog_help').hide(); 
            $('#divelog').show('fold', {}, fold_speed); 
            divelog_help_display = false; 
            divelog_display = true;
        }
    });

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // These click events belong to the divelog form.
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // Trigger for 'New Dive' button click.
    $('#divelog_new_dive').click(function() { log_new_dive(); });

    // Trigger for 'Edit' button click.
    $('#divelog_edit').click(function() { enable_divelog(); });

    // Trigger for 'Save' button click.
    $('#divelog_save').click(function() { save_dive(); });

    // Trigger for 'Delete' button click.
    $('#divelog_delete').click(function() { delete_dive(); });

    // Trigger for 'Cancel' button click.
    $('#divelog_cancel').click(function() { disable_divelog(); display_dive(curr_dive_index); });

    // Display the previous dive.
    $('#prev_dive').click(function() { previous_dive(); });

    // Display the next dive.
    $('#next_dive').click(function() { next_dive(); });

    // Display the first dive.
    $('#first_dive').click(function() { first_dive(); });

    // Display the first dive.
    $('#last_dive').click(function() { last_dive(); });


    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // These click events belong to the divelog_listing form.
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // Trigger for the 'Save' (settings) button.
    $('#divelog_settings_save').click(function() { save_divelogprefs(); });


    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // These click events belong to the divelog_listing form.
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    // Display the previous dive listing.
    $('#prev_listing').click(function() { previous_listing(); });

    // Display the next dive listing.
    $('#next_listing').click(function() { next_listing(); });

    // Display the first dive listing.
    $('#first_listing').click(function() { first_listing(); });

    // Display the first dive listing.
    $('#last_listing').click(function() { last_listing(); });
});


function save_dive() {
    // Do some form validation.
    if(check_diver_email() == false) { return; }
    if($('#dive_no').val() == '')    { alert('Dive # is required.'); $('#dive_no').focus(); return; }
    if($('#dive_date').val() == '')  { alert('Dive Date is required.'); $('#dive_date').focus(); return; }

    disable_divelog();

    var xmldata = make_dive_xml();           // get xml of dive that is to be logged
    //alert(xmldata);
    var dive_data = Base64.encode(xmldata);  // base64 encode it for posting


    $.ajax({
        type: "POST",
        url: 'ajax/divelog_save.php',
        data: { xmldata: dive_data }   // dive_data is base64 encoded xml
    })
    .done(function(result) {
        if(result.substr(0, 5) == 'ERROR') { alert(result); }
        else {
            alert("Result: " + result);
            if(result.substr(0, 9) == 'Saved new') { curr_dive_index = dives.length; }

            load_xml();   // asynchronous call
	}
    })
    .fail(function(jqXHR, textStatus) { 
        alert("Request failed: " + jqXHR.status + " " + textStatus); 
    });
}


function delete_dive() {
    disable_divelog();

    if(check_diver_email() == false) { return; }

    $.ajax({
        type: "POST",
        url: 'ajax/divelog_delete.php',
        data: { email: email, dive_no: $('#dive_no').val() }
    })
    .done(function(result) {
        if(result.substr(0, 5) == 'ERROR') { alert(result); }
        else { 
            // The logged dive has been deleted.  Display the previous logged dive.
            alert(result);
            curr_dive_index = (curr_dive_index > 0) ? curr_dive_index -1 : 0;

            load_xml(email);  // asynchronous call
        }
    })
    .fail(function(jqXHR, textStatus) { 
        alert("Request failed: " + jqXHR.status + " " + textStatus); 
    });
}


function log_new_dive() {
    clear_divelog();

    if(check_diver_email() == false) { return; }

    $.ajax({
        type: "POST",
        url: 'ajax/divelog_next_diveno.php',
        data: { email: email }
    })
    .done(function(result) {
        if(result.substr(0, 5) == 'ERROR') { alert(result); disable_divelog(); display_dive(curr_dive_index); }
        else { $('#dive_no').val(result); }
        enable_divelog();
        $('#divelog_delete').hide();
    })
    .fail(function(jqXHR, textStatus) { 
        alert("Request failed: " + jqXHR.status + " " + textStatus); 
    });
}

function check_diver_email() {
    if(email == undefined || email == '') {
        alert('No email specified. Log in to use the dive log.');
        return(false);
    }
    return(true);
}


function load_xml() {
    // Get all the logbook records for the email and store in data structure.
    //
    disable_divelog();

    if(check_diver_email() == false) { return; }

    $.ajax({
        type: "POST",
        url: 'ajax/divelog_load_xml.php',
        data: { email: email }
    })
    .done(function(result) {
        //alert("Result: " + result);
        if(result.substr(0, 5) == 'ERROR') { alert(result); }
        else {
            var parsedXML = $.parseXML(result);
            var dives_tmp = new Array();

            // Build an array of associative arrays.
            $(parsedXML).find("dive").each(function() {
                var hash = new Array();
                hash['dive_no'] = $(this).find('dive_no').text();
                hash['dive_date'] = $(this).find('dive_date').text();
                hash['location'] = $(this).find('location').text();
                hash['site_name'] = $(this).find('site_name').text();
                hash['time_in'] = $(this).find('time_in').text();
                hash['time_out'] = $(this).find('time_out').text();
                hash['rnt'] = $(this).find('rnt').text();
                hash['abt'] = $(this).find('abt').text();
                hash['tbt'] = $(this).find('tbt').text();
                hash['air_temp'] = $(this).find('air_temp').text();
                hash['bottom_temp'] = $(this).find('bottom_temp').text();
                hash['begin_psi'] = $(this).find('begin_psi').text();
                hash['end_psi'] = $(this).find('end_psi').text();
                hash['viz'] = $(this).find('viz').text();
                hash['weight'] = $(this).find('weight').text();
                hash['si'] = $(this).find('si').text();
                hash['begin_pg'] = $(this).find('begin_pg').text();
                hash['end_pg'] = $(this).find('end_pg').text();
                hash['depth'] = $(this).find('depth').text();
                hash['bottom_time'] = $(this).find('bottom_time').text();
                hash['safety_stop'] = $(this).find('safety_stop').text();
                hash['salt'] = $(this).find('salt').text();
                hash['fresh'] = $(this).find('fresh').text();
                hash['boat'] = $(this).find('boat').text();
                hash['shore'] = $(this).find('shore').text();
                hash['surge'] = $(this).find('surge').text();
                hash['waves'] = $(this).find('waves').text();
                hash['wetsuit'] = $(this).find('wetsuit').text();
                hash['drysuit'] = $(this).find('drysuit').text();
                hash['hood'] = $(this).find('hood').text();
                hash['gloves'] = $(this).find('gloves').text();
                hash['boots'] = $(this).find('boots').text();
                hash['vest'] = $(this).find('vest').text();
                hash['computer'] = $(this).find('computer').text();
                hash['computer_desc'] = $(this).find('computer_desc').text();
                hash['eanx'] = $(this).find('eanx').text();
                hash['eanx_percent'] = $(this).find('eanx_percent').text();
                hash['comments'] = $(this).find('comments').text();
                dives_tmp.push(hash);
            });

            dives = dives_tmp;  // setting the global array the new data
            $('#divelog_info').html(dives.length + ' dives logged');
            display_dive(curr_dive_index);
	}
    })
    .fail(function(jqXHR, textStatus) { 
        alert("Request failed: " + jqXHR.status + " " + textStatus); 
    });
}


function load_preferences_xml() {
    // Get all the logbook records for the email and store in data structure.
    //
    disable_divelog();

    if(check_diver_email() == false) { return; }

    $.ajax({
        type: "POST",
        url: 'ajax/divelogprefs_load_xml.php',
        data: { email: email }
    })
    .done(function(result) {
        //alert("Result: " + result);
        if(result.substr(0, 5) == 'ERROR') { alert(result); }
        else {
            //alert('result: ' + result);
            var parsedXML = $.parseXML(result);
            // Build an array of associative arrays.
            $(parsedXML).find("divelogprefs").each(function() {
                prefs['email'] = $(this).find('email').text();
                prefs['fname'] = $(this).find('fname').text();
                prefs['lname'] = $(this).find('lname').text();
                prefs['cert_level'] = $(this).find('cert_level').text();
                prefs['cert_agency'] = $(this).find('cert_agency').text();
                prefs['weight'] = $(this).find('weight').text();
                prefs['distance'] = $(this).find('distance').text();
                prefs['temperature'] = $(this).find('temperature').text();
                prefs['pressure'] = $(this).find('pressure').text();
            });

            display_prefs();
            display_settings();     
	}
    })
    .fail(function(jqXHR, textStatus) { 
        alert("Request failed: " + jqXHR.status + " " + textStatus); 
    });
}


function save_divelogprefs() {
    // Do some form validation.
    if(check_diver_email() == false) { return; }
    if($('#diver_email').val() == '') { alert('Email address is required.'); $('#diver_email').focus(); return; }

    $('#divelog_settings_save').attr('disabled', 'disabled');
    $('#divelog_settings_save').css({backgroundColor: '#c0c0c0'});

    var xmldata = make_diveprefs_xml();
    //alert(xmldata);
    var diveprefs_data = Base64.encode(xmldata);


    $.ajax({
        type: "POST",
        url: 'ajax/divelogprefs_save.php',
        data: { xmldata: diveprefs_data }   // diveprefs_data is base64 encoded xml
    })
    .done(function(result) {
        if(result.substr(0, 5) == 'ERROR') { alert(result); }
        else {
            //alert(result);
            $('#divelog_settings_info').html(result);
            load_preferences_xml();   // asynchronous call
	}
    })
    .fail(function(jqXHR, textStatus) { 
        alert("Request failed: " + jqXHR.status + " " + textStatus); 
    });
}


function make_dive_xml() {
    var xmldata = '';

    // Some table column fields are integers.  Therefore if they are blank on the from we should
    // set the value to zero so INSERT and UPDATE sql methods work properly.
    //
    var dive_no, rnt, abt, tbt, begin_psi, end_psi, bottom_time;

    if(($('#dive_no').val() == '') || (isNaN($('#dive_no').val()) == true)) { dive_no = 0; }
    else { dive_no = parseInt($('#dive_no').val()); }

    if(($('#rnt').val() == '') || (isNaN($('#rnt').val()) == true)) { rnt = 0; }
    else { rnt = parseInt($('#rnt').val()); }

    if(($('#abt').val() == '') || (isNaN($('#abt').val()) == true)) { abt = 0; }
    else { abt = parseInt($('#abt').val()); }

    if(($('#tbt').val() == '') || (isNaN($('#tbt').val()) == true)) { tbt = 0; }
    else { tbt = parseInt($('#tbt').val()); }

    if(($('#begin_psi').val() == '') || (isNaN($('#begin_psi').val()) == true)) { begin_psi = 0; }
    else { begin_psi = parseInt($('#begin_psi').val()); }

    if(($('#end_psi').val() == '') || (isNaN($('#end_psi').val()) == true)) { end_psi = 0; }
    else { end_psi = parseInt($('#end_psi').val()); }

    if(($('#bottom_time').val() == '') || (isNaN($('#bottom_time').val()) == true)) { bottom_time = 0; }
    else { bottom_time = parseInt($('#bottom_time').val()); }


    xmldata  = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
    xmldata += "<divelog>\n\n";
    xmldata += "    <email>" + email + "</email>\n\n";

    xmldata += "    <dive>\n";
    xmldata += "        <dive_no>" + dive_no + "</dive_no>\n";
    xmldata += "        <dive_date>" + $('#dive_date').val() + "</dive_date>\n";
    xmldata += "        <location><![CDATA[" + $('#location').val() + "]]></location>\n";
    xmldata += "        <site_name><![CDATA[" + $('#site_name').val() + "]]></site_name>\n";
    xmldata += "        <time_in><![CDATA[" + $('#time_in').val() + "]]></time_in>\n";
    xmldata += "        <time_out><![CDATA[" + $('#time_out').val() + "]]></time_out>\n";
    xmldata += "        <air_temp>" + $('#air_temp').val() + "</air_temp>\n";
    xmldata += "        <bottom_temp>" + $('#bottom_temp').val() + "</bottom_temp>\n";
    xmldata += "        <begin_psi>" + begin_psi + "</begin_psi>\n";
    xmldata += "        <end_psi>" + end_psi + "</end_psi>\n";
    xmldata += "        <viz>" + $('#viz').val() + "</viz>\n";
    xmldata += "        <weight>" + $('#weight').val() + "</weight>\n";
    xmldata += "        <salt>" + ((document.getElementById('salt').checked == true) ? 'Y' : 'N') + "</salt>\n";
    xmldata += "        <fresh>" + ((document.getElementById('fresh').checked == true) ? 'Y' : 'N') + "</fresh>\n";
    xmldata += "        <boat>" + ((document.getElementById('boat').checked == true) ? 'Y' : 'N') + "</boat>\n";
    xmldata += "        <shore>" + ((document.getElementById('shore').checked == true) ? 'Y' : 'N') + "</shore>\n";
    xmldata += "        <surge>" + ((document.getElementById('surge').checked == true) ? 'Y' : 'N') + "</surge>\n";
    xmldata += "        <waves>" + ((document.getElementById('waves').checked == true) ? 'Y' : 'N') + "</waves>\n";
    xmldata += "        <wetsuit>" + ((document.getElementById('wetsuit').checked == true) ? 'Y' : 'N') + "</wetsuit>\n";
    xmldata += "        <drysuit>" + ((document.getElementById('drysuit').checked == true) ? 'Y' : 'N') + "</drysuit>\n";
    xmldata += "        <hood>" + ((document.getElementById('hood').checked == true) ? 'Y' : 'N') + "</hood>\n";
    xmldata += "        <gloves>" + ((document.getElementById('gloves').checked == true) ? 'Y' : 'N') + "</gloves>\n";
    xmldata += "        <boots>" + ((document.getElementById('boots').checked == true) ? 'Y' : 'N') + "</boots>\n";
    xmldata += "        <vest>" + ((document.getElementById('vest').checked == true) ? 'Y' : 'N') + "</vest>\n";
    xmldata += "        <computer>" + ((document.getElementById('computer').checked == true) ? 'Y' : 'N') + "</computer>\n";
    xmldata += "        <computer_desc><![CDATA[" + $('#computer_desc').val() + "]]></computer_desc>\n";
    xmldata += "        <eanx>" + ((document.getElementById('eanx').checked == true) ? 'Y' : 'N') + "</eanx>\n";
    xmldata += "        <eanx_percent><![CDATA[" + $('#eanx_percent').val() + "]]></eanx_percent>\n";
    xmldata += "        <rnt>" + rnt + "</rnt>\n";
    xmldata += "        <abt>" + abt + "</abt>\n";
    xmldata += "        <tbt>" + tbt + "</tbt>\n";
    xmldata += "        <si>" + $('#si').val() + "</si>\n";
    xmldata += "        <begin_pg>" + $('#begin_pg').val() + "</begin_pg>\n";
    xmldata += "        <end_pg>" + $('#end_pg').val() + "</end_pg>\n";
    xmldata += "        <depth>" + $('#depth').val() + "</depth>\n";
    xmldata += "        <safety_stop>" + $('#safety_stop').val() + "</safety_stop>\n";
    xmldata += "        <bottom_time>" + bottom_time + "</bottom_time>\n";
    xmldata += "        <comments><![CDATA[" + $('#comments').val() + "]]></comments>\n";
    //xmldata += "        <timestamp>" + $('#timestamp') + "</timestamp>\n";
    xmldata += "    </dive>\n\n";
    xmldata += "</divelog>\n";

    return(xmldata);
}


function make_diveprefs_xml() {
    var xmldata = '';
    var distance_measure = ($('#feet').is(':checked')) ? 'I' : 'M';
    var pressure_measure = ($('#psi').is(':checked')) ? 'I' : 'M';
    var temperature_measure = ($('#fahr').is(':checked')) ? 'I' : 'M';
    var weight_measure = ($('#lbs').is(':checked')) ? 'I' : 'M';

    xmldata  = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
    xmldata += "<divelogprefs>\n\n";
    xmldata += "    <email>" + $('#diver_email').val() + "</email>\n";
    xmldata += "    <fname>" + $('#fname').val() + "</fname>\n";
    xmldata += "    <lname>" + $('#lname').val() + "</lname>\n";
    xmldata += "    <cert_level>" + $('#cert_level').val() + "</cert_level>\n";
    xmldata += "    <cert_agency>" + $('#cert_agency').val() + "</cert_agency>\n";
    xmldata += "    <distance>" + distance_measure + "</distance>\n";
    xmldata += "    <weight>" + weight_measure + "</weight>\n";
    xmldata += "    <temperature>" + temperature_measure + "</temperature>\n";
    xmldata += "    <pressure>" + pressure_measure + "</pressure>\n";
    xmldata += "</divelogprefs>\n";

    return(xmldata);
}


function display_dive(i) {
    // Using the data structure, display a dive at the specified index.
    //
    if(dives.length <= 0) { return; }
    if(i >= dives.length) { return; }

    $('#dive_no').val(dives[i]['dive_no']);
    $('#dive_date').val(dives[i]['dive_date']);
    $('#location').val(dives[i]['location']);
    $('#site_name').val(dives[i]['site_name']);
    $('#time_in').val(dives[i]['time_in']);
    $('#time_out').val(dives[i]['time_out']);
    $('#rnt').val(dives[i]['rnt']);
    $('#abt').val(dives[i]['abt']);
    $('#tbt').val(dives[i]['tbt']);
    $('#air_temp').val(dives[i]['air_temp']);
    $('#bottom_temp').val(dives[i]['bottom_temp']);
    $('#begin_psi').val(dives[i]['begin_psi']);
    $('#end_psi').val(dives[i]['end_psi']);
    $('#viz').val(dives[i]['viz']);
    $('#weight').val(dives[i]['weight']);
    $('#si').val(dives[i]['si']);
    $('#begin_pg').val(dives[i]['begin_pg']);
    $('#end_pg').val(dives[i]['end_pg']);
    $('#depth').val(dives[i]['depth']);
    $('#bottom_time').val(dives[i]['bottom_time']);
    $('#safety_stop').val(dives[i]['safety_stop']);

    (dives[i]['salt'] == 'Y')     ? $('#salt').attr('checked', 'checked')     : $('#salt').removeAttr('checked');
    (dives[i]['fresh'] == 'Y')    ? $('#fresh').attr('checked', 'checked')    : $('#fresh').removeAttr('checked');
    (dives[i]['boat'] == 'Y')     ? $('#boat').attr('checked', 'checked')     : $('#boat').removeAttr('checked');
    (dives[i]['shore'] == 'Y')    ? $('#shore').attr('checked', 'checked')    : $('#shore').removeAttr('checked');
    (dives[i]['surge'] == 'Y')    ? $('#surge').attr('checked', 'checked')    : $('#surge').removeAttr('checked');
    (dives[i]['waves'] == 'Y')    ? $('#waves').attr('checked', 'checked')    : $('#waves').removeAttr('checked');
    (dives[i]['wetsuit'] == 'Y')  ? $('#wetsuit').attr('checked', 'checked')  : $('#wetsuit').removeAttr('checked');
    (dives[i]['drysuit'] == 'Y')  ? $('#drysuit').attr('checked', 'checked')  : $('#drysuit').removeAttr('checked');
    (dives[i]['hood'] == 'Y')     ? $('#hood').attr('checked', 'checked')     : $('#hood').removeAttr('checked');
    (dives[i]['gloves'] == 'Y')   ? $('#gloves').attr('checked', 'checked')   : $('#gloves').removeAttr('checked');
    (dives[i]['boots'] == 'Y')    ? $('#boots').attr('checked', 'checked')    : $('#boots').removeAttr('checked');
    (dives[i]['vest'] == 'Y')     ? $('#vest').attr('checked', 'checked')     : $('#vest').removeAttr('checked');
    (dives[i]['computer'] == 'Y') ? $('#computer').attr('checked', 'checked') : $('#computer').removeAttr('checked');
    (dives[i]['eanx'] == 'Y')     ? $('#eanx').attr('checked', 'checked')     : $('#eanx').removeAttr('checked');

    $('#computer_desc').val(dives[i]['computer_desc']);
    $('#eanx_percent').val(dives[i]['eanx_percent']);
    $('#comments').val(dives[i]['comments']);
}


function display_prefs() {
    if(prefs['weight'] == 'M') { $('.prefs_weight').html('kgs'); }
    else                       { $('.prefs_weight').html('lbs'); }

    if(prefs['distance'] == 'M') { $('.prefs_distance').html('m'); }
    else                         { $('.prefs_distance').html('ft'); }

    if(prefs['pressure'] == 'M') { $('.prefs_pressure').html('bar'); }
    else                         { $('.prefs_pressure').html('psi'); }

    if(prefs['temperature'] == 'M') { $('.prefs_temp').html('C'); }
    else                            { $('.prefs_temp').html('F'); }

    if(prefs['distance'] == 'M') { $('.prefs_safety_stop').html('5m Stop'); }
    else                         { $('.prefs_safety_stop').html('15ft Stop'); }

    if(prefs['distance'] == 'M') { $('.prefs_depth').html('(m)'); }
    else                         { $('.prefs_depth').html('(ft)'); }
}


function display_settings() {
    // Displays the divelogprefs data in the settings form.

    $('#diver_email').val(prefs['email']);
    $('#fname').val(prefs['fname']);
    $('#lname').val(prefs['lname']);
    $('#cert_level').val(prefs['cert_level']);
    $('#cert_agency').val(prefs['cert_agency']);


    // Below are radio buttons
    if(prefs['distance'] == 'M') { document.getElementById('meters').checked = true; }
    else                         { document.getElementById('feet').checked = true; }

    if(prefs['pressure'] == 'M') { document.getElementById('bar').checked = true; }
    else                         { document.getElementById('psi').checked = true; }

    if(prefs['weight'] == 'M') { document.getElementById('kgs').checked = true; }
    else                         { document.getElementById('lbs').checked = true; }

    if(prefs['temperature'] == 'M') { document.getElementById('cels').checked = true; }
    else                            { document.getElementById('fahr').checked = true; }

    $('#divelog_settings_save').removeAttr('disabled', 'disabled');
    $('#divelog_settings_save').css({backgroundColor: '#205895'});   
}


function disable_divelog() {
    // This function makes all the fields on the the divelog form read-only.
    //
    $('#divelog :input').each(function() {
        //alert('$(this).attr(type) == ' + $(this).attr('type') + ' == ' + $(this).val());
        var this_type = $(this).attr('type');
        if(this_type != undefined && this_type.toUpperCase() == 'TEXT') {
            $(this).css({backgroundColor: '#f0f0f0'});
            $(this).attr('readonly', 'readonly');
        }
        else if(this_type != undefined && this_type.toUpperCase() == 'CHECKBOX') {
            $(this).attr('disabled', 'disabled');
        }
    });

    $('#comments').attr('readonly', 'readonly');  // seperate because this is a textarea

    // Finally show and hide the necessary buttons.
    $('#divelog_edit').show();
    $('#divelog_save').hide();
    $('#divelog_delete').hide();
    $('#divelog_cancel').hide();
}


function enable_divelog() {
    // This function makes all the fields on the divelog form read-write.
    //
    $('#divelog :input').each(function() {
        //alert('$(this).attr(type) == ' + $(this).attr('type') + ' == ' + $(this).val());
        var this_type = $(this).attr('type');
        if(this_type != undefined && this_type.toUpperCase() == 'TEXT') {
            $(this).css({backgroundColor: '#ffffff'});
            $(this).removeAttr('readonly', 'readonly');
        }
        else if(this_type != undefined && this_type.toUpperCase() == 'CHECKBOX') {
            $(this).removeAttr('disabled', 'disabled');
        }
    });

    $('#comments').removeAttr('readonly', 'readonly');  // seperate because this is a textarea

    // Finally show and hide the necessary buttons.
    $('#divelog_edit').hide();
    $('#divelog_save').show();
    $('#divelog_delete').show();
    $('#divelog_cancel').show();
}


function clear_divelog() {
    // Clear values from the form so a new dive can be entered.
    $('#dive_no').val('');
    $('#dive_date').val('');
    $('#location').val('');
    $('#site_name').val('');
    $('#time_in').val('');
    $('#time_out').val('');
    $('#rnt').val('');
    $('#abt').val('');
    $('#tbt').val('');
    $('#air_temp').val('');
    $('#bottom_temp').val('');
    $('#begin_psi').val('');
    $('#end_psi').val('');
    $('#viz').val('');
    $('#weight').val('');
    $('#si').val('');
    $('#begin_pg').val('');
    $('#end_pg').val('');
    $('#depth').val('');
    $('#bottom_time').val('');
    $('#safety_stop').val('');

    $('#salt').removeAttr('checked');
    $('#fresh').removeAttr('checked');
    $('#boat').removeAttr('checked');
    $('#shore').removeAttr('checked');
    $('#surge').removeAttr('checked');
    $('#waves').removeAttr('checked');
    $('#wetsuit').removeAttr('checked');
    $('#drysuit').removeAttr('checked');
    $('#hood').removeAttr('checked');
    $('#gloves').removeAttr('checked');
    $('#boots').removeAttr('checked');
    $('#vest').removeAttr('checked');
    $('#computer').removeAttr('checked');
    $('#eanx').removeAttr('checked');

    $('#computer_desc').val('');
    $('#eanx_percent').val('');
    $('#comments').val('');
}


function first_dive() {
    // Display the first dive.
    curr_dive_index = 0;
    display_dive(curr_dive_index);
    disable_divelog();
}


function last_dive() {
    // Display the last dive.
    curr_dive_index = dives.length -1;
    if(curr_dive_index < 0) { curr_dive_index = 0; }
    display_dive(curr_dive_index);
    disable_divelog();
}


function previous_dive() {
    // Display the previous dive.
    if(curr_dive_index <= 0) { curr_dive_index = 0; }
    else { curr_dive_index--; }
    display_dive(curr_dive_index);
    disable_divelog();
}


function next_dive() {
    // Display the next dive.
    if(curr_dive_index >= (dives.length -1)) { curr_dive_index = dives.length -1; }
    else { curr_dive_index++; }

    if(curr_dive_index < 0) { curr_dive_index = 0; }
    display_dive(curr_dive_index);
    disable_divelog();
}


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
// Functions for the divelog_listing. //
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //

var LISTING_TOTAL_ROWS = 12;  // Represents the max number of records to list.
var LISTING_FIRST = 0;        // Represents the first record listed.
var LISTING_LAST = 0;         // Represents the last record listed.

function display_listing_rows() {

    // All the divelog records are stored in the dives array.  
    // This function will display the rows in in divelog_listing.html.
    var rowhtml = '';

    var table_style = 'border-bottom: 1px solid #9b7824; border-left: 1px solid #9b7824; border-right: 1px solid #9b7824; ';
        table_style += 'width: 100%';


    // If there is no logged dives, create a row stating as such.
    if(dives.length < 1) {
        rowhtml  = '<table style="' + table_style + '" cellspacing="0">';
        rowhtml += '    <tr>';
        rowhtml += '        <td class="loglisting_cell">No dives to display.  Go diving and then come back.</td>';
        rowhtml += '    </tr>';
        rowhtml += '</table>';
    }

    var row_index = 0;
    LISTING_FIRST = curr_dive_index;

    for(var i = curr_dive_index; i < dives.length; i++) {
        if(row_index >= LISTING_TOTAL_ROWS) { break; }

        var row_tr = 'row' + row_index;           // creating strings: row0, row1, ... row11 
        var row_id = 'row' + row_index + '_id';   // creating strings: row0_id, row1_id, ... row11_id
        var row_color = ((i % 2) == 0) ? 'loglisting_row_even' : 'loglisting_row_odd';

        rowhtml += '<table style="' + table_style + '" cellspacing="0">';
        rowhtml += '    <tr id="' + row_tr + '" class="' + row_color + '">';
        rowhtml += '        <td class="loglisting_cell" style="width: 32px;">' + dives[i]['dive_no'];
        rowhtml += '            <input type="hidden" value="' + i + '" id="' + row_id + '" /></td>';
        rowhtml += '        <td class="loglisting_cell" style="width: 65px;">' + dives[i]['dive_date'] + '</td>';
        rowhtml += '        <td class="loglisting_cell" style="width: 178px;">' + dives[i]['location'] + '</td>';
        rowhtml += '        <td class="loglisting_cell" style="width: 210px;">' + dives[i]['site_name'] + '</td>';
        rowhtml += '        <td class="loglisting_cell" style="text-align: right; width: 34px;">' + dives[i]['depth'] + '</td>';
        rowhtml += '        <td class="loglisting_cell" style="text-align: right; width: auto;">' + dives[i]['bottom_time'] + '</td>';
        rowhtml += '    </tr>';
        rowhtml += '</table>';

        row_index++;
        LISTING_LAST = i;
    }

    $('#divelog_rows').html(rowhtml);

    // Setup the click event triggers for a divelog_listing row here.
    $('#row0').click(function()  { show_dive_from_listing($('#row0_id').val());  });
    $('#row1').click(function()  { show_dive_from_listing($('#row1_id').val());  });
    $('#row2').click(function()  { show_dive_from_listing($('#row2_id').val());  });
    $('#row3').click(function()  { show_dive_from_listing($('#row3_id').val());  });
    $('#row4').click(function()  { show_dive_from_listing($('#row4_id').val());  });
    $('#row5').click(function()  { show_dive_from_listing($('#row5_id').val());  });
    $('#row6').click(function()  { show_dive_from_listing($('#row6_id').val());  });
    $('#row7').click(function()  { show_dive_from_listing($('#row7_id').val());  });
    $('#row8').click(function()  { show_dive_from_listing($('#row8_id').val());  });
    $('#row9').click(function()  { show_dive_from_listing($('#row9_id').val());  });
    $('#row10').click(function() { show_dive_from_listing($('#row10_id').val()); });
    $('#row11').click(function() { show_dive_from_listing($('#row11_id').val()); });
}


function show_dive_from_listing(idx) {
    curr_dive_index = idx; 
    save_curr_dive_index = curr_dive_index;

    divelog_listing_close();
    display_dive(curr_dive_index); 
    disable_divelog();
}


function divelog_listing_close() {
    if(divelog_listing_display) {
        curr_dive_index = save_curr_dive_index;
        $('#divelog_listing').hide(); 
        $('#divelog').show('fold', {}, fold_speed); 
        divelog_listing_display = false; 
        divelog_display = true;
    }
}


function next_listing() {
    //alert('LISTING_FIRST = ' + LISTING_FIRST + '\nLISTING_LAST = ' + LISTING_LAST + '\ndives.length = ' + dives.length);

    if(LISTING_FIRST == 0 && LISTING_LAST == 0) { return; }  // No next listing if both are zero.
    if((LISTING_LAST +1) >= dives.length -1) { return; }     // No next listing to display.

    curr_dive_index = LISTING_LAST +1;
    display_listing_rows();
}


function last_listing() {
    //alert('LISTING_FIRST = ' + LISTING_FIRST + '\nLISTING_LAST = ' + LISTING_LAST + '\ndives.length = ' + dives.length);

    if(LISTING_FIRST == 0 && LISTING_LAST == 0) { return; }  // No last listing if both are zero.
    if((dives.length -1) <= LISTING_TOTAL_ROWS) { return; }  // No last listing to display.
    if((LISTING_LAST +1) >= (dives.length - LISTING_TOTAL_ROWS -1)) { return; }

    curr_dive_index = dives.length - LISTING_TOTAL_ROWS;
    display_listing_rows();
}


function previous_listing() {
    //alert('LISTING_FIRST = ' + LISTING_FIRST + '\nLISTING_LAST = ' + LISTING_LAST + '\ndives.length = ' + dives.length);

    if(LISTING_FIRST == 0 && LISTING_LAST == 0) { return; }  // No previous listing if both are zero.
    if((LISTING_FIRST -1) <= 0) { return; }                  // No previous listing to display.

    curr_dive_index = LISTING_FIRST - LISTING_TOTAL_ROWS;
    if(curr_dive_index < 0) { curr_dive_index = 0; }
    display_listing_rows();
}


function first_listing() {
    //alert('LISTING_FIRST = ' + LISTING_FIRST + '\nLISTING_LAST = ' + LISTING_LAST + '\ndives.length = ' + dives.length);

    if(LISTING_FIRST == 0 && LISTING_LAST == 0) { return; }  // No previous listing if both are zero.

    curr_dive_index = 0;
    display_listing_rows();
}
