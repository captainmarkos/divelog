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


var curr_dive_index = 0;    // Current dive being displayed (not necessarily the same as dive_no).
var dives = new Array();    // This will be an array of associative arrays.
var email = 'captainmarkos@gmail.com';


$(document).ready(function() {
    var fold_speed = 300;
    var divelog_display = true;
    var divelog_listing_display = false;
    var divelog_settings_display = false;
    var divelog_help_display = false;

    $('#dive_date').datepicker({ dateFormat: "yy-mm-dd" });  // Display the date in ISO format
    $('#divelog_listing').hide();
    $('#divelog_settings').hide();
    $('#divelog_help').hide();

    disable_divelog();

    load_xml(email);


    //
    // Setup click event triggers
    //
    $('#divelog_listing_open').click(function() {
        if(!divelog_listing_display) {
            $('#divelog').hide();
	    $('#divelog_listing').show('fold', {}, fold_speed);
            $('#divelog_listing_info').html('3500 hours spent underwater!');
            divelog_listing_display = true;
            divelog_display = false;

            // Since load_xml() was called above, we can loop
            // through the dives array to display logged dives.
        }
    });


    $('#divelog_listing_close').click(function() {
        if(divelog_listing_display) {
            $('#divelog_listing').hide(); 
            $('#divelog').show('fold', {}, fold_speed); 
            divelog_listing_display = false; 
            divelog_display = true;
        }
    });


    $('#divelog_settings_open').click(function() {
        if(!divelog_settings_display) { 
            $('#divelog').hide(); 
            $('#divelog_settings').show('fold', {}, fold_speed); 
            $('#divelog_settings_info').html('Tribal War ina Babylon');
            divelog_settings_display = true;  
            divelog_display = false;
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


    $('#row1').click(function() {
        alert('row clicked');
    });


    // Trigger for 'New Dive' button click.
    $('#divelog_new_dive').click(function() { log_new_dive(); });

    // Trigger for 'Edit' button click.
    $('#divelog_edit').click(function() { enable_divelog(); });

    // Trigger for 'Save' button click.
    $('#divelog_save').click(function() { save_dive(); });

    // Trigger for 'Delete' button click.
    $('#divelog_delete').click(function() { delete_dive(); });

    // Display the previous dive.
    $('#prev_dive').click(function() { previous_dive(); });

    // Display the next dive.
    $('#next_dive').click(function() { next_dive(); });

    // Display the first dive.
    $('#first_dive').click(function() { first_dive(); });

    // Display the first dive.
    $('#last_dive').click(function() { last_dive(); });
});


function save_dive() {
    // Do some form validation.
    if($('#dive_no').val() == '')   { alert('Dive # is required.'); $('#dive_no').focus(); return; }
    if($('#dive_date').val() == '') { alert('Dive Date is required.'); $('#dive_date').focus(); return; }

    disable_divelog();
    var dive_data = make_dive_xml();   // returns base64 encoded xml

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
            load_xml(email);
	}
    })
    .fail(function(jqXHR, textStatus) { 
        alert("Request failed: " + jqXHR.status + " " + textStatus); 
    });
}


function delete_dive() {
    $.ajax({
        type: "POST",
        url: 'ajax/divelog_delete.php',
        data: { email: email, dive_no: $('#dive_no').val() }
    })
    .done(function(result) {
        if(result.substr(0, 5) == 'ERROR') { alert(result); }
        else { 
            // The logged dive has been deleted.  Display the previous logged dive.
            disable_divelog();
            alert(result);
            load_xml(email);
            previous_dive();
        }
    })
    .fail(function(jqXHR, textStatus) { 
        alert("Request failed: " + jqXHR.status + " " + textStatus); 
    });
}


function log_new_dive() {
    clear_divelog();

    $.ajax({
        type: "POST",
        url: 'ajax/divelog_next_diveno.php',
        data: { xmldata: email }
    })
    .done(function(result) {
        if(result.substr(0, 5) == 'ERROR') { alert(result); disable_divelog(); }
        else { $('#dive_no').val(result); }
        enable_divelog();
        $('#divelog_delete').hide();
    })
    .fail(function(jqXHR, textStatus) { 
        alert("Request failed: " + jqXHR.status + " " + textStatus); 
    });
}


function load_xml(diver_email) {
    // Get all the logbook records for diver_email and store in data structure.
    //
    $.ajax({
        type: "POST",
        url: 'ajax/divelog_load_xml.php',
        data: { email: diver_email }
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
    xmldata += "<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->\n";
    xmldata += "<!-- divelog.xml                                                   -->\n";
    xmldata += "<!--                                                               -->\n";
    xmldata += "<!-- Created by: bluewild.us                                       -->\n";
    xmldata += "<!-- Do no modify this file otherwise it may not be read properly. -->\n";
    xmldata += "<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->\n";
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

    return(Base64.encode(xmldata));
}


function display_dive(dive_index) {
    // Using the data structure, display a dive at the specified index.
    //
    if(dives.length <= 0) { return; }

    $('#dive_no').val(dives[dive_index]['dive_no']);
    $('#dive_date').val(dives[dive_index]['dive_date']);
    $('#location').val(dives[dive_index]['location']);
    $('#site_name').val(dives[dive_index]['site_name']);
    $('#time_in').val(dives[dive_index]['time_in']);
    $('#time_out').val(dives[dive_index]['time_out']);
    $('#rnt').val(dives[dive_index]['rnt']);
    $('#abt').val(dives[dive_index]['abt']);
    $('#tbt').val(dives[dive_index]['tbt']);
    $('#air_temp').val(dives[dive_index]['air_temp']);
    $('#bottom_temp').val(dives[dive_index]['bottom_temp']);
    $('#begin_psi').val(dives[dive_index]['begin_psi']);
    $('#end_psi').val(dives[dive_index]['end_psi']);
    $('#viz').val(dives[dive_index]['viz']);
    $('#weight').val(dives[dive_index]['weight']);
    $('#si').val(dives[dive_index]['si']);
    $('#begin_pg').val(dives[dive_index]['begin_pg']);
    $('#end_pg').val(dives[dive_index]['end_pg']);
    $('#depth').val(dives[dive_index]['depth']);
    $('#bottom_time').val(dives[dive_index]['bottom_time']);
    $('#safety_stop').val(dives[dive_index]['safety_stop']);

    (dives[dive_index]['salt'] == 'Y')     ? $('#salt').attr('checked', 'checked')     : $('#salt').removeAttr('checked');
    (dives[dive_index]['fresh'] == 'Y')    ? $('#fresh').attr('checked', 'checked')    : $('#fresh').removeAttr('checked');
    (dives[dive_index]['boat'] == 'Y')     ? $('#boat').attr('checked', 'checked')     : $('#boat').removeAttr('checked');
    (dives[dive_index]['shore'] == 'Y')    ? $('#shore').attr('checked', 'checked')    : $('#shore').removeAttr('checked');
    (dives[dive_index]['surge'] == 'Y')    ? $('#surge').attr('checked', 'checked')    : $('#surge').removeAttr('checked');
    (dives[dive_index]['waves'] == 'Y')    ? $('#waves').attr('checked', 'checked')    : $('#waves').removeAttr('checked');
    (dives[dive_index]['wetsuit'] == 'Y')  ? $('#wetsuit').attr('checked', 'checked')  : $('#wetsuit').removeAttr('checked');
    (dives[dive_index]['drysuit'] == 'Y')  ? $('#drysuit').attr('checked', 'checked')  : $('#drysuit').removeAttr('checked');
    (dives[dive_index]['hood'] == 'Y')     ? $('#hood').attr('checked', 'checked')     : $('#hood').removeAttr('checked');
    (dives[dive_index]['gloves'] == 'Y')   ? $('#gloves').attr('checked', 'checked')   : $('#gloves').removeAttr('checked');
    (dives[dive_index]['boots'] == 'Y')    ? $('#boots').attr('checked', 'checked')    : $('#boots').removeAttr('checked');
    (dives[dive_index]['vest'] == 'Y')     ? $('#vest').attr('checked', 'checked')     : $('#vest').removeAttr('checked');
    (dives[dive_index]['computer'] == 'Y') ? $('#computer').attr('checked', 'checked') : $('#computer').removeAttr('checked');
    (dives[dive_index]['eanx'] == 'Y')     ? $('#eanx').attr('checked', 'checked')     : $('#eanx').removeAttr('checked');

    $('#computer_desc').val(dives[dive_index]['computer_desc']);
    $('#eanx_percent').val(dives[dive_index]['eanx_percent']);
    $('#comments').val(dives[dive_index]['comments']);
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
    display_dive(curr_dive_index);
    disable_divelog();
}

