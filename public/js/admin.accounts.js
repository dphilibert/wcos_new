/**
 * Ajax-Bibliothek fuer Adminbereich->Accountverwaltung
 *
 * @author Thomas Grahammer
 * @version $id$
 */

var ajaxBaseURL = "/admin/accounts.ajax/";

var startPagination = 0;
var endPagination = 10;
var anzahlPerPage = 10;
var anzahlPages = 1;
var page = 1;

/**
 * erstellt das Grid via Ajax
 *
 * @constructor
 */
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
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash
    },
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
        if (i < anzahlDatensaetze)
        {
          var id = ajaxData[i].user_id;
          $("#null").clone(true).insertAfter("#null");
          $("#null:last").attr('id', id);
          $("#" + id).attr('name', 'ajaxZeile');
          $("#" + id).removeClass();
          if (i % 2 == 0) $("#" + id).addClass('tableListeZeileGrey');
          if (i % 2 > 0) $("#" + id).addClass('tableListeZeile');
          $("#" + id + " td[id='ID']").html(ajaxData[i].user_id);
          $("#" + id + " td[id='firmenname']").html(ajaxData[i].username);
          $("#" + id + " td[id='system']").html(ajaxData[i].systemID);
          $("#" + id + " td[id='status']").html(ajaxData[i].premiumLevel);
          $("#" + id + " td[id='letzter_login']").html(ajaxData[i].lastLogin);
          $('#' + id + ' a[id="edit"]').attr('onClick', 'editLine (' + id + ')');
          $('#' + id + ' a[id="del"]').attr('onClick', 'deleteLine (' + id + ')');
          $('#' + id).removeAttr('style');
        }
      }
    }
  });
}

$(document).ready(function ()
{
  getOptionsPages();
  BuildAjaxGrid();

  $("#lightboxOpen").fancybox({
    'width'             :800,
    'height'            :220,
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
    'height'            :220,
    'autoScale'         :false,
    'autoDimensions'    :false,
    'transitionIn'      :'elastic',
    'transitionOut'     :'elastic',
    'centerOnScroll'    :true,
    'hideOnOverlayClick':true,
    'titleShow'         :false,
    'showCloseButton'   :true,
    'enableEscapeButton':true,
  });

  $("#del").fancybox({
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

