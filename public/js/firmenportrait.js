/**
 * URL der Ajax-Anfrage
 * @type {String}
 */
var ajaxBaseURL = "/firmenportrait/firmenportrait.ajax/";

/**
 * Start der Seitenschaltung (Pagination)
 * @type {Number}
 */
var startPagination = 0;

/**
 * Ende der Seitenschaltung (Pagination)
 * @type {Number}
 */
var endPagination = 10;

/**
 * Anzahl der Elemente pro Seite
 * @type {Number}
 */
var anzahlPerPage = 10;

/**
 * Anzahl der Seiten (Start)
 * @type {Number}
 */
var anzahlPages = 1;

/**
 * Start-Seite
 * @type {Number}
 */
var page = 1;

/**
 * Generiert das Grid
 * @constructor
 *
 * @return void
 */

/**
 * Generiert das Grid
 * @constructor
 *
 * @return void
 */
var BuildAjaxGrid = function ()
{
  $("tr[name='ajaxZeile']").remove();
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxData = '';
  var ajaxURL = ajaxBaseURL + "loadlist/anbieterID/" + anbieterID + "/hash/" + anbieterHash;
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    type    :"POST",
    success :function (ajaxData)
    {
      var anzahlDatensaetze = ajaxData.length;
      var ungerundeteSeitenanzahl = anzahlDatensaetze / anzahlPerPage;
      anzahlPages = Math.round(ungerundeteSeitenanzahl);
      endPagination = Number(startPagination) + Number(anzahlPerPage) - 1;
      if (ungerundeteSeitenanzahl > anzahlPages) anzahlPages++;
      $(".paginatorPageNo").html('Seite ' + page + ' von ' + anzahlPages);
      for (var i = startPagination; i <= endPagination; i++)
      {
        var id = i;
        $("#null").clone(true).insertAfter("#null");
        $("#null:last").attr('id', id);
        $("#" + id).attr('name', 'ajaxZeile');
        $("#" + id).removeClass();
        if (i % 2 == 0) $("#" + id).addClass('tableListeZeileGrey');
        if (i % 2 > 0) $("#" + id).addClass('tableListeZeile');
        $("#" + id + " td[id='eintrag']").html(ajaxData[i].eintrag);
        var dbFeld = ajaxData[i].dbfeld;
        $('#' + id + ' a[id="edit"]').attr('onClick', 'editLine ("' + dbFeld + '")');
        $('#' + id + ' a[id="del"]').attr('onClick', 'deleteLine ("' + dbFeld + '")');
        $('#' + id).removeAttr('style');
      }
    }
  });
}

/**
 * lädt die Daten in das Edit-Form
 * @param apID
 *
 * @return void
 */
var loadFormData = function (dbFeld)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/firmenportrait/firmenportrait.ajax/load/anbieterID/" + anbieterID + "/hash/" + anbieterHash;
  var ajaxData = '';
  $("#ID").val(dbFeld);
  ergebnis = '';
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    type    :"POST",
    cache   :false,
    async   :false,
    success :function (ajaxData)
    {
      ergebnis = eval("ajaxData." + dbFeld);
      $("textarea").html(ergebnis);
    }
  });
  return ergebnis;
};

/**
 * Eintrag bearbeiten
 * @param dbFeld
 *
 * @return void
 */
var editLine = function (dbFeld)
{
  loadFormData(dbFeld);
  $("#ID").val(dbFeld);
}
/**
 * tinyMCE Editor
 * @param dbFeld
 *
 * @return void
 */
function tinymceInit(dbFeld)
{
  tinyMCE.init({
    // General options
    mode                           :"textareas",
    theme                          :"advanced",
    plugins                        :"safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",
    width                          :790,
    // Theme options
    theme_advanced_buttons1        :"bold,italic,underline,strikethrough,|,fontselect,fontsizeselect,bullist,numlist,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,hr,removeformat,visualaid,separator,sub,sup,separator,charmap",
    theme_advanced_buttons2        :"",
    theme_advanced_buttons3        :"",
    theme_advanced_toolbar_location:"top",
    theme_advanced_toolbar_align   :"left",
    entity_encoding                :"raw",
    dialog_type                    :"modal",
    forced_root_block              :"",
    force_br_newlines              :false
  })
//loadFormData (dbFeld);
}

/**
 * tinyMCE killen
 *
 * @return void
 */
function tinymceKill()
{
  $('#tinyMCETD').html('<textarea id="editor" rows="25" cols="90"></textarea>'); //TODO
//location.reload(true);
}

/**
 * Eintrag speichern
 * @param selectedField Datenbank-Feld
 * @param selectedValue Wert
 * @constructor
 *
 * @return void
 */
var AjaxSave = function (selectedField, selectedValue)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/firmenportrait/firmenportrait.ajax/save/anbieterID/" + anbieterID + "/hash/" + anbieterHash;
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    asynch  :false,
    url     :ajaxURL,
    type    :"POST",
    data    :{
      'field':selectedField,
      'value':selectedValue
    },
    success :function (ajaxData)
    {
    }
  });
}

$(document).ready(function ()
{
  BuildAjaxGrid();
  $("#buttonSave").click(function ()
  {
    var selectedField = $("#ID").val();
    var selectedValue = $("iframe").contents().find("body").html();
    //    var selectedValue = $("#editor_ifr").tinymce ().getContent ();
    $('#tinyMCETD').html('<textarea id="editor" rows="25" cols="90"></textarea>');
    $.fancybox.close();
    AjaxSave(selectedField, selectedValue);
  });
  $("#anzahlEintraege").change(function ()
  {
    page = 1;
    startPagination = 1;
    anzahlPerPage = $(this).val();
    BuildAjaxGrid();
  });
  $("#edit").fancybox({
    'width'             :800,
    'height'            :450,
    'autoScale'         :false,
    'autoDimensions'    :false,
    'transitionIn'      :'elastic',
    'transitionOut'     :'elastic',
    'centerOnScroll'    :true,
    'hideOnOverlayClick':true,
    'titleShow'         :false,
    'showCloseButton'   :true,
    'enableEscapeButton':true,
    'onComplete'        :tinymceInit,
    'onCleanup'         :tinymceKill
  });
});


