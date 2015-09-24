var MINUTE = 1;
var HOUR = 0;
var PATH_START_SCRIPT = 'sudo /home/pi/elektro/webcam/bash/start_hoticam.sh';
var PATH_STOP_SCRIPT = 'sudo /home/pi/elektro/webcam/bash/stop_hoticam.sh';
var DELETE_BUTTON = '<input name="btnCronjobDelete" type="button" value="L&ouml;schen"/>';
var weekdays = new Array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");


getCronjobLine = function(time_from, time_to, weekday) {
    var time_from = time_from.split(":");
    var time_to = time_to.split(":");
    var weekday = weekdays.indexOf(weekday);
//                Cronjob Zeilen erzeugen und an Listeneintrag haengen
    var data = new Array();
    data.push(time_from[MINUTE] + " " + time_from[HOUR] + " " + "*" + " " + "*" + " " + weekday + " " + PATH_START_SCRIPT);
    data.push(time_to[MINUTE] + " " + time_to[HOUR] + " " + "*" + " " + "*" + " " + weekday + " " + PATH_STOP_SCRIPT);

    return data;
};




jQuery(document).ready(function($) {

    var rules = jQuery('#rules').DataTable({
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "bAutoWidth": false
    });

    var fileImport = new FileImport(rules);



    getRules = function() {
        var cronjobs = new Array();
        rules.rows().data().each(function(value, index) {
            console.log(value);

            var data = getCronjobLine(value[0], value[1], value[2]);
            cronjobs = cronjobs.concat(data);
        });

        return cronjobs;
    };

    btnDeleteClick = function() {
        var parent = jQuery(this).parent();
        while (!parent.is('tr')) {
            parent = parent.parent();
        }
//                    parent.remove();
        var row = rules.row(parent);
        var rowNode = row.node();
        row.remove();
        rowNode.remove();
    };


    jQuery("[name='btnCronjobDelete']").bind('click', btnDeleteClick);


    jQuery('#btnExport').bind('click', function() {
//                      var cronjobs = getRules();
        var json = new Array();
        jQuery.each(rules.rows().data(), function(index, value) {
            console.log(value);
//                         console.log(index);
            json.push(value);
        });
        var csvString = JSON.stringify(json);

        var a = document.createElement('a');
        a.href = 'data:text/plain;charset=utf-8,' + csvString;
        a.target = '_blank';
        a.download = 'export.txt';

        document.body.appendChild(a);
        a.click();
    });

    jQuery('#addRule').bind('click', function() {
        // Zeiten holen
        var time_from = jQuery('#datetimepicker_from').val();
        var time_to = jQuery('#datetimepicker_to').val();
        if (time_from == "" || time_to == "") {
            alert("Von/Bis nicht ausgefüllt!");
            return;
        }
        var weekday = jQuery('#weekday').val();
        console.log(time_from + " " + time_to + " " + weekday);
        // Zeile hinzufuegen
        rules.row.add([time_from, time_to, weekdays[weekday], DELETE_BUTTON]).draw();
        // Binding setzen
        jQuery("[name='btnCronjobDelete']").unbind('click');
        jQuery("[name='btnCronjobDelete']").bind('click', btnDeleteClick);
    });

    jQuery('#submit').bind('click', function() {
        // Alle Regeln sammeln
        var cronjobs = getRules();

        // Daten senden
        $.ajax({
            type: "POST",
            url: "ajax/crontab_generate.php",
            data: {cronjobs: cronjobs},
            success: function(data) {
                console.log("done");
                console.log(data);
//                window.location = "crontab.php";
                alert("Regeln wurden übernommen.");
            }
        });
    });

    jQuery("#imgBack").bind('mouseover', function() {
        jQuery('#imgBack').attr('src', 'img/back_mouseover.png');
    });
    jQuery("#imgBack").bind('mouseout', function() {
        jQuery('#imgBack').attr('src', 'img/back.png');
    });

    jQuery('#datetimepicker_from').datetimepicker({
        datepicker: false,
        format: 'H:i',
        step: 5
    });
    jQuery('#datetimepicker_to').datetimepicker({
        datepicker: false,
        format: 'H:i',
        step: 5
    });

    for (var i = 0; i < weekdays.length; i++) {
        var value = '<option value="' + i + '">' + weekdays[i] + '</option>';
        jQuery('#weekday').append(value);
    }


});

FileImport = function(rules) {

    var _rules = rules;
    var btnImport = jQuery('#btnImport');

    handleFileSelect = function(evt) {
        var files = evt.target.files; // FileList object

        for (var i = 0, f; f = files[i]; i++) {

            // Only process image files.
            if (!f.type.match('text/plain')) {
                continue;
            }

            var reader = new FileReader();


            // Read in the image file as a data URL.
            reader.readAsText(f);

            // Closure to capture the file information.
            reader.onload = function(theFile) {
                var text = theFile.target.result;
                console.log(text);
                text = JSON.parse(text);
                console.log(text);
                _rules.rows().remove();
                _rules.rows.add(text).draw();
                jQuery("[name='btnCronjobDelete']").bind('click', btnDeleteClick);
            };

        }
    };

//  document.getElementById('files').addEventListener('change', handleFileSelect, false);


    var initialize = function() {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            // Great success! All the File APIs are supported.
            btnImport.change(handleFileSelect);
//            document.getElementById('btnImport').addEventListener('change', handleFileSelect, false);
        } else {
            alert('The File APIs are not fully supported in this browser.');
        }

    };

    initialize();


};