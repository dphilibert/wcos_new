var ajaxBaseURL = "/termine/termine.ajax/";
var startPagination = 0;
var endPagination = 10;
var anzahlPerPage = 10;
var anzahlPages = 1;
var page = 1;

var BuildAjaxGrid = function ()
{
  $("tr[name='ajaxZeile']").remove();
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxData = '';
  var ajaxURL = ajaxBaseURL + "loadlist/anbieterID";
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
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
      if (ungerundeteSeitenanzahl > anzahlPages) anzahlPages++;
      $(".paginatorPageNo").html('Seite ' + page + ' von ' + anzahlPages);
      $('.paginator_maxPages').html(anzahlPages);
      for (var i = startPagination; i <= endPagination; i++)
      {
        if (i < anzahlDatensaetze)
        {
          var id = ajaxData[i].termineID;
          $("#null").clone(true).insertAfter("#null");
          $("#null:last").attr('id', id);
          $("#" + id).attr('name', 'ajaxZeile');
          $("#" + id).removeClass();
          if (i % 2 == 0) $("#" + id).addClass('tableListeZeileGrey');
          if (i % 2 > 0) $("#" + id).addClass('tableListeZeile');
          $("#" + id + " td[id='beginn']").html(ajaxData[i].beginn);
          $("#" + id + " td[id='ende']").html(ajaxData[i].ende);
          $("#" + id + " td[id='ort']").html(ajaxData[i].ort);
          $('#' + id + ' a[id="edit"]').attr('onClick', 'editLine (' + id + ')');
          $('#' + id + ' a[id="del"]').attr('onClick', 'deleteLine (' + id + ')');
          $('#' + id).removeAttr('style');
        }
      }
    }
  });
}

// die Options werden per Ajax eingelesen. Sinn: Man kann sie aus der Session holen damit der User sie nicht staendig neu auswaehlen muss
var getOptionsPages = function ()
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
      hash      :anbieterHash
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

// die Options fuer die Medien-Auswahl
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
    type    :"POST",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash
    },
    success :function (ajaxData)
    {
      $.each(ajaxData, function (k, v)
      {
        $("#te_medium").append('<option value="' + v.mediaID + '">' + v.mediadesc + '</option>');
      });
      $("#te_medium option[value='" + selectedMediaID + "']").attr('selected', true);
    }
  });
}

// die Options fuer die Termin-Typen-Auswahl
var getOptionsTerminTypen = function ()
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxData = '';
  var ajaxURL = "/termine/termine.ajax/loadtypenlist/anbieterID";
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    type    :"POST",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash
    },
    success :function (ajaxData)
    {
      $.each(ajaxData, function (k, v)
      {
        $("#te_typ").append('<option value="' + v.terminTypID + '">' + v.terminTyp + '</option>');
      });
    }
  });
}

var editLine = function (id)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();

  loadFormData(id);
}

function tinymceInit(id)
{
  tinyMCE.init({
    // General options
    mode                           :"exact",
    'width'                        :790,
    theme                          :"advanced",
    plugins                        :"safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",
    
    // Theme options
    theme_advanced_buttons1        :"pastetext,pasteword,|,bold,italic,underline,strikethrough,|, link,unlink,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,justifyfull,hr,removeformat,visualaid,separator,sub,sup,separator,charmap,code",
    
    paste_auto_cleanup_on_paste : true,
    paste_remove_styles: true,
    paste_remove_styles_if_webkit: true,
    paste_strip_class_attributes: true,
    
    theme_advanced_buttons2        :"",
    theme_advanced_buttons3        :"",
    theme_advanced_toolbar_location:"top",
    theme_advanced_toolbar_align   :"left",
    entity_encoding                :"raw",
    dialog_type                    :"modal",
    forced_root_block              :"",
    force_br_newlines              :false,
    elements                       :"te_beschreibung"
  });
}

function tinymceKill()
{
  $("#teaserMCE").html('<textarea id="te_teaser" name="teaser" cols=70 rows=4></textarea>');
  $("#beschreibungMCE").html('<textarea id="te_beschreibung" name="beschreibung" cols=70 rows=4></textarea>');
}
var deleteLine = function (id)
{
  fancyConfirm("Wollen Sie den Termin wirklich l&ouml;schen?", function (ret)
  {
    if (ret == true)
    {
      AjaxDelete(id);
      BuildAjaxGrid();
    }
  });
}
var newEntry = function ()
{
  $("[id^=te_]").val('');
  editLine(0);
}
var loadFormData = function (tID)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  if (tID > 0)
  {
    var ajaxURL = "/termine/termine.ajax/load";
  }
  if (tID == 0)
  {
    var ajaxURL = "/termine/termine.ajax/new";
  }
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
      tid       :tID
    },
    success :function (ajaxData)
    {
      // die Daten des "Forms"
      tID = ajaxData.termineID;
      $("#te_loeschenTimer option[value='" + ajaxData.loeschenTimer + "']").attr('selected', true);
      $("#te_titel").val(ajaxData.name);
      $("#te_teaser").val(ajaxData.teaser);
      $("#te_beschreibung").val(ajaxData.beschreibung);
      $("#te_beginn").val(ajaxData.beginn);
      $("#te_ende").val(ajaxData.ende);
      $("#te_ort").val(ajaxData.ort);
      $("#tID").val(tID);
      getOptionsMedien(ajaxData.mediaID);
      getOptionsTerminTypen();
    }
  });
};

var AjaxSave = function (selectedField, selectedValue, id)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/termine/termine.ajax/save";
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    type    :"POST",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
      tid       :id,
      field     :selectedField,
      value     :selectedValue
    },
    success :function (ajaxData)
    {
      if (selectedField == 'beginn')
      {
        $("#" + id + " td[id='beginn']").html(selectedValue);
      }
      if (selectedField == 'ende')
      {
        $("#" + id + " td[id='ende']").html(selectedValue);
      }
      if (selectedField == 'ort')
      {
        $("#" + id + " td[id='ort']").html(selectedValue);
      }
    }
  });
}

var AjaxDelete = function (tID)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/termine/termine.ajax/del";
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    type    :"POST",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
      tid       :tID
    },
    success :function (ajaxData)
    {
    }
  });
  $("#" + tID).remove();
}
var switchMesseSelect = function ()
{
  if ($("#messenSelect").css('display') == "none")
    $("#messenSelect").show();
  else
    $("#messenSelect").hide();
}
$(document).ready(function ()
{
  getOptionsPages();
  BuildAjaxGrid();
  $("#buttonSave").click(function ()
  {
    var textarea_id_teaser = $("textarea[name='teaser']").attr('id');
    var textarea_id_beschreibung = $("textarea[name='beschreibung']").attr('id');
    //var teaser = $("#"+textarea_id_teaser+"_ifr").contents().find("body").html ();
    //var beschreibung = $("#"+textarea_id_beschreibung+"_ifr").contents().find("body").html();
    for (var i = 0; i < tinymce.editors.length; i++)
    {
      // you need to do what is needed here
      // example: write the content back to the form foreach editor instance
      tinymce.editors[i].save();
    }
    var teaser = $("#te_teaser").val();
    var beschreibung = $("#te_beschreibung_ifr").contents().find("body").html();
    var tID = $("#tID").val();
    AjaxSave("teaser", teaser, tID);
    AjaxSave("beschreibung", beschreibung, tID);
    $.fancybox.close();
    BuildAjaxGrid();
  });
  $("#new").click(function ()
  {
    newEntry();
  });
  $("textarea,input,select,iframe").blur(function ()
  {
    var selectedField = $(this).attr('name');
    var selectedValue = $(this).val();
    var tID = $("#tID").val();
    AjaxSave(selectedField, selectedValue, tID);
  });
  $("#te_typ").change(function ()
  {
    var typ = $("#te_typ").val();
    if (typ == "0") $("#messenSelect").hide();
    if (typ == "1") $("#messenSelect").show();
  });
  $("#anzahlEintraege").change(function ()
  {
    page = 1;
    startPagination = 1;
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

  $("#new").fancybox({
    'width'             :800,
    'height'            :550,
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

  $("#edit").fancybox({
    'width'             :800,
    'height'            :550,
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
  $("a.del").fancybox({
    'width'             :800,
    'height'            :400,
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

