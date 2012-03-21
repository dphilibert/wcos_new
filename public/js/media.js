var ajaxBaseURL = "/media/media.ajax/";
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
  var ajaxURL = ajaxBaseURL + "getmedienliste";
  $.ajax(
  {
    dataType:"json",
    url:ajaxURL,
    type:"POST",
    data:{
      anbieterID:anbieterID,
      hash:anbieterHash
    },
    success:function (ajaxData)
    {
      var anzahlDatensaetze = ajaxData.length;
      var ungerundeteSeitenanzahl = anzahlDatensaetze / anzahlPerPage;
      anzahlPages = Math.round(ungerundeteSeitenanzahl);
      $('.paginator_maxPages').html(anzahlPages);
      endPagination = Number(startPagination) + Number(anzahlPerPage) - 1;
      if (ungerundeteSeitenanzahl > anzahlPages) anzahlPages++;
      $(".paginatorPageNo").html('Seite ' + page + ' von ' + anzahlPages);
      for (var i = startPagination; i <= endPagination; i++)
      {
        if (i < anzahlDatensaetze)
        {
          var id = ajaxData[i].mediaID;
          $("#null").clone(true).insertAfter("#null");
          $("#null:last").attr('id', id);
          $("#" + id).attr('name', 'ajaxZeile');
          $("#" + id).removeClass();
          if (i % 2 == 0) $("#" + id).addClass('tableListeZeileGrey');
          if (i % 2 > 0) $("#" + id).addClass('tableListeZeile');
          $("#" + id + " td[id='typ']").html(ajaxData[i].mediatypdesc);
          $("#" + id + " td[id='beschreibung']").html(ajaxData[i].mediadesc);
          $("#" + id + " td[id='datei']").html(ajaxData[i].mediadatei);
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
    url:ajaxURL,
    type:"POST",
    data:{
      anbieterID:anbieterID,
      hash:anbieterHash
    },
    success:function (ajaxData)
    {
      $.each(ajaxData ['anzahlEintraegeOptions'], function (k, v)
      {
        $("#anzahlEintraege").append('<option value="' + k + '">' + v + '</option>');
      });
    }
  });
}
// die Options fuer die Termin-Typen-Auswahl
var getOptionsMediaTypen = function ()
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxData = '';
  var ajaxURL = "/media/media.ajax/loadtypenlist";
  $.ajax(
  {
    dataType:"json",
    url:ajaxURL,
    type:"POST",
    data:{
      anbieterID:anbieterID,
      hash:anbieterHash
    },
    success:function (ajaxData)
    {
      $.each(ajaxData, function (k, v)
      {
        $("#me_mediatyp").append('<option value="' + v.mediatyp + '">' + v.beschreibung + '</option>');
      });
    }
  });
}
// als Lightbox-Dialog wird SimpleModal verwendet
var editLine = function (id)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  $("#mID").val(id);
  getOptionsMediaTypen();
  loadFormData(id);
}
var deleteLine = function (id)
{
  fancyConfirm("Wollen Sie das Medium wirklich l&ouml;schen?", function (ret)
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
  editLine(0);
}
var loadFormData = function (ID)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  $("#ID").val(ID);
  if (ID > 0)
  {
    var ajaxURL = "/media/media.ajax/load";
    var makeAsync = true;
  }
  if (ID == 0)
  {
    var ajaxURL = "/media/media.ajax/new";
    var makeAsync = false;
  }
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url:ajaxURL,
    async:makeAsync,
    type:"POST",
    data:{
      anbieterID:anbieterID,
      hash:anbieterHash,
      id:ID
    },
    success:function (ajaxData)
    {
      // die Daten des "Forms"
      ID = ajaxData.mediaID;
      $("#ID").val(ajaxData.mediaID);
      $("#me_beschreibung").val(ajaxData.beschreibung);
      $("#me_link").val(ajaxData.link);
      $("#me_mediatyp option[value='" + ajaxData.mediatyp + "']").attr('selected', 'selected');
      if (ajaxData.mediatyp == 'LINK')
      {
        $("tr[name='linkSwitch']").removeClass('switch');
      }
      if (ajaxData.mediatyp == 'BILD')
      {
        $("tr[name='bildSwitch']").removeClass('switch');
        showThumbnail(ajaxData.mediaID, ajaxData.mediadatei);
      }
      if (ajaxData.mediatyp == 'FIRMENLOGO')
      {
        $("tr[name='bildSwitch']").removeClass('switch');
        showThumbnail(ajaxData.mediaID, ajaxData.mediadatei);
      }
      if (ajaxData.mediatyp == 'MESSELOGO')
      {
        $("tr[name='bildSwitch']").removeClass('switch');
        showThumbnail(ajaxData.mediaID, ajaxData.mediadatei);
      }
      if (ajaxData.mediatyp == 'ANSPRECHPARTNER_BILD')
      {
        $("tr[name='bildSwitch']").removeClass('switch');
        showThumbnail(ajaxData.mediaID, ajaxData.mediadatei);
      }
      if (ajaxData.mediatyp == 'VIDEO')
      {
        $("tr[name='bildSwitch']").removeClass('switch');
        showThumbnail(ajaxData.mediaID, ajaxData.mediadatei);
      }
      if (ajaxData.mediatyp == 'PDF')
      {
        $("tr[name='bildSwitch']").removeClass('switch');
        showThumbnail(ajaxData.mediaID, ajaxData.mediadatei);
      }
    }
  });
  createUploader(ID);
  if ($("#me_mediatyp").val() == "BILD")
  {
    $("tr[name='bildSwitch']").removeClass('switch');
  }
};
var AjaxSave = function (selectedField, selectedValue, id)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/media/media.ajax/save";
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url:ajaxURL,
    type:"POST",
    data:{
      anbieterID:anbieterID,
      hash:anbieterHash,
      id:id,
      field:selectedField,
      value:selectedValue
    },
    success:function (ajaxData)
    {
      if (selectedField == 'me_beschreibung')
      {
        $("#" + id + " td[id='me_beschreibung']").html(selectedValue);
      }
      if (selectedField == 'me_mediatyp')
      {
        $("#" + id + " td[id='me_mediatyp']").html(selectedValue);
      }
      if (selectedField == 'me_link')
      {
        $("#" + id + " td[id='link']").html(selectedValue);
      }
    }
  });
}
var AjaxDelete = function (ID)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/media/media.ajax/del";
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url:ajaxURL,
    type:"POST",
    data:{
      anbieterID:anbieterID,
      hash:anbieterHash,
      id:ID
    },
    success:function (ajaxData)
    {
    }
  });
  $("#" + ID).remove();
}
function showThumbnail(ID, fileName)
{
  var ext = fileName.split('.');
  ext = ext[ext.length - 1];
  $('#thumbNail').html('<img id="bildTN" src="/uploads/thumbnails/' + ID + '.' + ext + '">');
}
function removeUploadButton()
{
  //$('.qq-uploader').remove ();  
  $('.qq-upload-list').remove();
  $('#bildTN').remove();
}
function createUploader(ID)
{
  var uploader = new qq.FileUploader({
    element:document.getElementById('bild'),
    action:'/media/upload/',
    multiple:false,
    onComplete:function (id, fileName, responseJSON)
    {
      mediaExtension = responseJSON ['MEDIAEXTENSION'];
      AjaxSave("mediadatei", fileName, ID);
      mediaTyp = $("#me_mediatyp").val();
      AjaxSave("mediatyp", mediaTyp, ID);
      AjaxSave("mediaExtension", mediaExtension, ID);
      removeUploadButton();
      showThumbnail(ID, fileName);
    },
    params:{
      mediaID:ID
    },
    template:'<div class="qq-uploader">' +
    '<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
    '<div class="qq-upload-button">Datei hochladen</div>' +
    '<ul class="qq-upload-list"></ul>' +
    '</div>',
    debug:true
  });
}
function initForm()
{
  getOptionsMediaTypen();
}
$(document).ready(function ()
{
  getOptionsPages();
  BuildAjaxGrid();
  $("#buttonSave").click(function ()
  {
    $.fancybox.close();
    BuildAjaxGrid();
  });
  $("textarea,input,select").blur(function ()
  {
    var selectedField = $(this).attr('name');
    var selectedValue = $(this).val();
    var ID = $("#ID").val();
    AjaxSave(selectedField, selectedValue, ID);
    mID = $("#ID").val();
    mediaTyp = $("#me_mediatyp").val();
    AjaxSave("mediatyp", mediaTyp, mID);
  });
  // hier bei Aenderung pruefen ob Link ausgewaehlt und dann Link-Feld anzeigen. Genauso bei Bild und Upload
  $("select").change(function ()
  {
    $("tr[name='linkSwitch']").addClass("switch");
    $("tr[name='bildSwitch']").addClass("switch");
    if ($("#me_mediatyp").val() == 'LINK') $("tr[name='linkSwitch']").removeClass('switch');
    if ($("#me_mediatyp").val() != 'LINK')
    {
      $("tr[name='bildSwitch']").removeClass('switch');
    }
  });
  $("#anzahlEintraege").change(function ()
  {
    page = 1;
    startPagination = 1;
    anzahlPerPage = $(this).val();
    BuildAjaxGrid();
  });
  $("#add").click(function ()
  {
    newEntry();
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
  $("#lightboxOpen").fancybox({
    'width':800,
    'height':600,
    'autoScale':false,
    'autoDimensions':false,
    'transitionIn':'elastic',
    'transitionOut':'elastic',
    'centerOnScroll':true,
    'hideOnOverlayClick':true,
    'titleShow':false,
    'showCloseButton':true,
    'enableEscapeButton':true
  });
  $("#add").fancybox({
    'width':800,
    'height':600,
    'autoScale':false,
    'autoDimensions':false,
    'transitionIn':'elastic',
    'transitionOut':'elastic',
    'centerOnScroll':true,
    'hideOnOverlayClick':true,
    'titleShow':false,
    'showCloseButton':true,
    'enableEscapeButton':true
  });
  $("#edit").fancybox({
    'width':800,
    'height':600,
    'autoScale':false,
    'autoDimensions':false,
    'transitionIn':'elastic',
    'transitionOut':'elastic',
    'centerOnScroll':true,
    'hideOnOverlayClick':true,
    'titleShow':false,
    'showCloseButton':true,
    'enableEscapeButton':true
  });
  $("a.del").fancybox({
    'width':800,
    'height':400,
    'autoScale':false,
    'autoDimensions':false,
    'transitionIn':'elastic',
    'transitionOut':'elastic',
    'centerOnScroll':true,
    'hideOnOverlayClick':true,
    'titleShow':false,
    'showCloseButton':true,
    'enableEscapeButton':true
  });
});


