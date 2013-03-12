// Whitepaper bei Firmenprofilen hinzufügen
var statChangeSystem = function (){	
    submitAjaxForm('#statisticGlobalForm', '/monitor/statistic/index-ajax/', '#allstat');
};

// Bei Auswahl eines Newsletters
var changeNL = function (){
    submitAjaxForm('#newsletterSelector', '/monitor/newsletter/load/format/html', '#allstat');
};

// change position
var changePos = function(aid, rss_id, option){
    submitAjaxForm('#statisticGlobalForm', '/monitor/newsletter/position/aid/' + aid + '/type/' + option + '/rid/' + rss_id + "/format/html", '#allstat');
};

// assign article
var assign = function(aid, sb){
    var rid = sb.options[sb.selectedIndex].value;
    submitAjaxForm('#statisticGlobalForm', '/monitor/newsletter/assign/aid/' + aid + '/rid/' + rid + "/format/html", '#allstat');
};

// unassign article
var unassign = function(aid, rid){
    submitAjaxForm('#statisticGlobalForm', '/monitor/newsletter/unassign/aid/' + aid + '/rid/' + rid + "/format/html", '#allstat');
};

// unmark "is_newsletter"
var release = function(aid){
    submitAjaxForm('#statisticGlobalForm', '/monitor/newsletter/release/aid/' + aid + "/format/html", '#allstat');
};

var reset = function(rid) {

    var elementId = '#dialogBox';
//    var url 	  = "/monitor/newsletter/reset/rid"+rid+"/format/html";

    $(elementId).dialog('destroy');
    $(elementId).html('<p>Sollen die Änderungen an dem aktuellen Newsletter verworfen werden?</p>');

    $(elementId).dialog({
        title: 'Änderungen verwerfen',
        autoOpen: true,
        bgiframe: true,
        resizable: false,
        height:140,
        modal: true,
        overlay: {
            backgroundColor: '#000',
            opacity: 0.5
        },
        buttons: {
            'Nein': function() {
                $(this).dialog('close');
            },
            'Ja': function() {
                    submitAjaxForm('#statisticGlobalForm', '/monitor/newsletter/reset/rid/' + rid + "/format/html", '#allstat');

//                $.manageAjax.add('clearQueue', {
//                    success: function(response) {
//                        location.reload(true);
//                    },
//                    url: url + '/format/html'
//                });
                $(this).dialog('close');
            }
        }
    });
};
