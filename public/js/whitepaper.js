var ajaxBaseURL = "/whitepaper/whitepaper.ajax/";
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
  var ajaxURL = ajaxBaseURL + "loadlist";
  $.ajax(
  {
    dataType:"json",
    url:ajaxURL,
    type:"POST",
    data:{
      anbieterID:anbieterID,
      hash:anbieterHash,
    },
    success:function (ajaxData)
    {
      var anzahlDatensaetze = ajaxData.length;
      var ungerundeteSeitenanzahl = anzahlDatensaetze / anzahlPerPage;
      anzahlPages = Math.round(ungerundeteSeitenanzahl);
      endPagination = Number(startPagination) + Number(anzahlPerPage) - 1;
      if (ungerundeteSeitenanzahl > anzahlPages) anzahlPages++;
      $(".paginatorPageNo").html('Seite ' + page + ' von ' + anzahlPages);
      $('.paginator_maxPages').html(anzahlPages);
      for (var i = startPagination; i <= endPagination; i++)
      {
        if (i < anzahlDatensaetze)
        {
          var id = ajaxData[i].whitepaperID;
          $("#null").clone(true).insertAfter("#null");
          $("#null:last").attr('id', id);
          $("#" + id).attr('name', 'ajaxZeile');
          $("#" + id).removeClass();
          if (i % 2 == 0) $("#" + id).addClass('tableListeZeileGrey');
          if (i % 2 > 0) $("#" + id).addClass('tableListeZeile');
          $("#" + id + " td[id='kategorien']").html(ajaxData[i].whitepaper_kategorie);
          $("#" + id + " td[id='beschreibung']").html(ajaxData[i].whitepaper_beschreibung);
          $('#' + id + ' a[id="edit"]').attr('onClick', 'editLine (' + id + ')');
          $('#' + id + ' a[id="del"]').attr('onClick', 'deleteLine (' + id + ')');
          if (ajaxData[i].whitepaper_freigabe_hash != '') $("#" + id + " td[class='hinweis']").attr('style', 'background:#FFFFFF;display:all');
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
      hash:anbieterHash,
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
var editLine = function (id)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  $("#ID").val(id);
  loadFormData(id);
  $("#edit").fancybox({
    'width':800,
    'height':220,
    'autoScale':false,
    'autoDimensions':false,
    'transitionIn':'elastic',
    'transitionOut':'elastic',
    'centerOnScroll':true,
    'hideOnOverlayClick':true,
    'titleShow':false,
    'showCloseButton':true,
    'enableEscapeButton':true,
    'onClosed':function (dialog)
    {
      var ajaxURL = "/whitepaper/whitepaper.ajax/cleartable";
      var ajaxData = '';
      $.ajax(
      {
        dataType:"json",
        url:ajaxURL,
        data:{
          anbieterID:anbieterID,
          hash:anbieterHash,
        },
        success:function (ajaxData)
        {
        }
      });
      $.modal.close();
    },
  });
}
var deleteLine = function (id)
{
  AjaxDelete(id);
}
var newEntry = function ()
{
  $("[id^=we_]").val('');
  editLine(0);
}
var loadFormData = function (ID)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  if (ID > 0) // bei ID > 0 Ansprechpartner bearbeiten
  {
    var ajaxURL = "/whitepaper/whitepaper.ajax/load";
  }
  if (ID == 0) // bei ID < 0 Ansprechpartner neu anlegen
  {
    var ajaxURL = "/whitepaper/whitepaper.ajax/new";
  }
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url:ajaxURL,
    type:"POST",
    data:{
      anbieterID:anbieterID,
      hash:anbieterHash,
      id:ID,
    },
    success:function (ajaxData)
    {
      // die Daten des "Forms"
      ID = ajaxData.whitepaperID;
      $("#we_kategorien").val(ajaxData.whitepaper_kategorie);
      $("#we_beschreibung").val(ajaxData.whitepaper_beschreibung);
      $("#we_link").val(ajaxData.whitepaper_link);
      $("#ID").val(ID);
    }
  });
  createUploader(ID);
};
var AjaxSave = function (selectedField, selectedValue, id)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/whitepaper/whitepaper.ajax/save";
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
      value:selectedValue,
    },
    success:function (ajaxData)
    {
      if (selectedField == 'kategorien')
      {
        $("#" + id + " td[id='kategorien']").html(selectedValue);
      }
      if (selectedField == 'beschreibung')
      {
        $("#" + id + " td[id='beschreibung']").html(selectedValue);
      }
      if (selectedField == 'link')
      {
        $("#" + id + " td[id='link']").html(selectedValue);
      }
    }
  });
}
var AjaxLockEntry = function (id, dateiname)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/whitepaper/whitepaper.ajax/lockentry";
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
      fname:dateiname
    },
    success:function (ajaxData)
    {
    }
  });
}
var AjaxDelete = function (ID)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/whitepaper/whitepaper.ajax/del";
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url:ajaxURL,
    type:"POST",
    data:{
      anbieterID:anbieterID,
      hash:anbieterHash,
      id:ID,
    },
    success:function (ajaxData)
    {
    }
  });
  $("#" + ID).remove();
}
function createUploader(ID)
{
  var uploader = new qq.FileUploader({
    element:document.getElementById('pdf'),
    action:'/whitepaper/upload/',
    multiple:false,
    onComplete:function (id, fileName, responseJSON)
    {
      mediaExtension = responseJSON ['MEDIAEXTENSION'];
      generatedFilename = responseJSON ['FILENAME'] + "." + mediaExtension;
      AjaxSave("whitepaper_datei", generatedFilename, ID);
      AjaxSave("whitepaper_datei_originalname", fileName, ID);
      AjaxLockEntry(ID, fileName);
      removeUploadButton();
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
  });
  $("#new").click(function ()
  {
    newEntry();
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
    'enableEscapeButton':true,
  });
  $("#del").fancybox({
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
//	$("#liste img[title]").tooltip();
});


