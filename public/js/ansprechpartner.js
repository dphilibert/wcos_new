/**
 * URL der Ajax-Anfrage
 * @type {String}
 */
var ajaxBaseURL = "/ansprechpartner/ansprechpartner.ajax/";

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
var BuildAjaxGrid = function ()
{
  $("tr[name='ajaxZeile']").remove();
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  ajaxData = '';
  var ajaxURL = ajaxBaseURL + "loadlist";
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    async   :true,
    type    :"POST",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
      filter    :myFilter
    },
    success :function (ajaxData)
    {            
      $("tr[name='ajaxZeile']").remove();
      var anzahlDatensaetze = ajaxData.length;
      var ungerundeteSeitenanzahl = anzahlDatensaetze / anzahlPerPage;
      anzahlPages = Math.round(ungerundeteSeitenanzahl);
                  
      if (anzahlPages < 1) anzahlPages = 1;
      endPagination = Number(startPagination) + Number(anzahlPerPage) - 1;
      if (endPagination > anzahlDatensaetze) endPagination = anzahlDatensaetze;
      if (ungerundeteSeitenanzahl > anzahlPages) anzahlPages++;
      $(".paginatorPageNo").html('Seite ' + page + ' von ' + anzahlPages);
      $('.paginator_maxPages').html(anzahlPages);
      var counter = 0;
      for (var i = startPagination; i <= endPagination; i++)
      {
        if (i < anzahlDatensaetze)
        {
          counter++;
          var id = ajaxData[i].ansprechpartnerID;
          $("#apID").val(id);
          $("#null").clone(true).insertAfter("#null");
          $("#null:last").attr('id', id);
          $("#" + id).attr('name', 'ajaxZeile');
          $("#" + id).removeClass();
          $("td[style='display:none']").remove();
          $("#" + id + " td[id='vorname']").html(ajaxData[i].vorname);
          $("#" + id + " td[id='nachname']").html(ajaxData[i].nachname);
          $('#' + id + ' a[id="edit"]').attr('onClick', 'editLine (' + id + ')');
          $('#' + id + ' a[id="edit"]').attr('class', 'edit');
          $('#' + id + ' a[id="del"]').attr('onClick', 'deleteLine (' + id + ')');
          $('#' + id).removeAttr('style');
          if (counter % 2 == 0) $("#" + id).addClass('tableListeZeileGrey');
          if (counter % 2 > 0) $("#" + id).addClass('tableListeZeile');
        }
      }
    }
  });
}

// die Options werden per Ajax eingelesen. Sinn: Man kann sie aus der Session holen damit der User sie nicht staendig neu auswaehlen muss
/**
 * Options für Select per Ajax laden
 *
 * @return void
 */
var getOptions = function ()
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxData = '';
  var ajaxURL = "/system/system.ajax/getpagesoptions";
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    type    :"POST",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
    },
    success :function (ajaxData)
    {
      $.each(ajaxData ['anzahlEintraegeOptions'], function (k, v)
      {
        $("#anzahlEintraege").append('<option value="' + k + '">' + v + '</option>');
      });
    }
  });
}

/**
 * Eintrag bearbeiten
 * @param id
 *
 * @return void
 */
var editLine = function (id)
{
  $("#apID").val(id);
  loadFormData(id);
}

/**
 * Eintrag löschen
 * @param id
 *
 * @return void
 */
var deleteLine = function (id)
{
  fancyConfirm("Wollen Sie den Ansprechpartner wirklich l&ouml;schen?", function (ret)
  {
    if (ret == true)
    {
      AjaxDelete(id);
      BuildAjaxGrid();
    }
  });
  // AjaxDelete (id);
}

/**
 * neuen Eintrag erstellen
 * @return void
 */
var newEntry = function ()
{
  $("[id^=ap_]").val('');
  editLine(0);
}

/**
 * lädt die Daten in das Edit-Form
 * @param apID
 *
 * @return void
 */
var loadFormData = function (apID)
{
//alert ($("#i_anrede option[value='Frau']"));
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  if (apID > 0) // bei apID > 0 Ansprechpartner bearbeiten
  {
    var ajaxURL = "/ansprechpartner/ansprechpartner.ajax/load";
  }
  if (apID == 0) // bei apID < 0 Ansprechpartner neu anlegen
  {
    var ajaxURL = "/ansprechpartner/ansprechpartner.ajax/new";    
  }
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    type    :"POST",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
      apid      :apID,
    },
    success :function (ajaxData)
    {
      
      // die Daten des "Forms"
      $("#ap_anredeID option[value='" + ajaxData.anredeID + "']").attr('selected', true);
      $("#ap_vorname").val(ajaxData.vorname);
      $("#ap_nachname").val(ajaxData.nachname);
      $("#ap_abteilung").val(ajaxData.abteilung);
      $("#ap_position").val(ajaxData.position);
      $("#ap_telefon").val(ajaxData.telefon);
      $("#ap_telefax").val(ajaxData.telefax);
      $("#ap_email").val(ajaxData.email);
      $("#ap_bemerkung").val(ajaxData.bemerkung);
      $("#apID").val(ajaxData.apID);
      getOptionsMedien(ajaxData.mediaID);

    }
  });
};

/**
 * die Options fuer die Medien-Auswahl
 *
 * @return void
 *
 */
var getOptionsMedien = function (selectedMediaID)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxData = '';
  var ajaxURL = "/media/media.ajax/getmedienliste/anbieterID";
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
      filter    :["ANSPRECHPARTNER_BILD", "PDF"],
    },
    success :function (ajaxData)
    {
      $.each(ajaxData, function (k, v)
      {
        $("#ap_mediaID").append('<option value="' + v.mediaID + '">' + v.mediadesc + '</option>');
      });
      $("#ap_mediaID option[value='" + selectedMediaID + "']").attr('selected', true);
    }
  });
}

/**
 * Eintrag speichern
 *
 * @param selectedField
 * @param selectedValue
 * @param id
 * @constructor
 *
 * @return void
 */
var AjaxSave = function (selectedField, selectedValue, id)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/ansprechpartner/ansprechpartner.ajax/save";
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    type    :"POST",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
      apid      :id,
      field     :selectedField,
      value     :selectedValue,
    },
    success :function (ajaxData)
    {
      if (selectedField == 'ap_vorname')
      {
        $("#" + id + " td[id='vorname']").html(selectedValue);
      }
      if (selectedField == 'ap_nachname')
      {
        $("#" + id + " td[id='nachname']").html(selectedValue);
      }
    }
  });
}

/**
 * Eintrag löschen
 * @param apID
 * @constructor
 *
 * @return void
 */
var AjaxDelete = function (apID)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/ansprechpartner/ansprechpartner.ajax/del/anbieterID";
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    async   :false,
    type    :"POST",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
      apid      :apID,
    },
    success :function (ajaxData)
    {
    }
  });
  $("#" + apID).remove();
}

function handler(nmbr)
{
  alert(nmbr);
}

$(document).ready(function ()
{
  getOptions();
  BuildAjaxGrid();
  $("#buttonSave").click(function ()
  {
    $.fancybox.close();
    //$.modal.close ();
    BuildAjaxGrid();
  });
  $("#ansprechpartner_editor input, #ansprechpartner_editor select").blur(function ()
  {
    var selectedField = $(this).attr('id');
    var selectedValue = $(this).val();
    var apID = $("#apID").val();
    AjaxSave(selectedField, selectedValue, apID);
    //  $(this).html (selectedValue);
  });
  $("#anzahlEintraege").change(function ()
  {
    page = 1;
    startPagination = 0;
    anzahlPerPage = $(this).val();
    BuildAjaxGrid();
  });
  $('.paginatorBwd').mouseover(function ()
  {
    $(this).css('cursor', 'pointer');
  });
  $('.paginatorBwd').click(function ()
  {
    if (page > 1)
    {
      page = page - 1;
      startPagination = Number(startPagination) - Number(anzahlPerPage);
      endPagination = Number(endPagination) - Number(anzahlPerPage);
      BuildAjaxGrid();
    }
  });
  $('.paginatorFwd').mouseover(function ()
  {
    $(this).css('cursor', 'pointer');
  });
  $('.paginatorFwd').click(function ()
  {
    if (page < anzahlPages)
    {
      page = page + 1;
      startPagination = Number(startPagination) + Number(anzahlPerPage);
      endPagination = Number(endPagination) + Number(anzahlPerPage);
      BuildAjaxGrid();
    }
  });
  $("#neu").fancybox({
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
    'enableEscapeButton':true
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
    'enableEscapeButton':true
  });
  $("a.del").fancybox({
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
    'enableEscapeButton':true
  });
});


