/**
 * lädt eine Liste von Anbietern via Ajax
 *
 * @param pagingStart Paging Start
 * @param pagingLimit Paging Anzahl (Limit)
 */

var ajaxLoadList = function (pagingStart, pagingLimit)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxData = '';
  var ajaxURL = "/admin/anbieter.ajax/getanbieterlist";
  var anzahlSpalten = $('.tableListeZeileHeader td').size ();
  $.ajax(
  {
    dataType:"json",
    url     :ajaxURL,
    type    :"POST",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
      pageStart :pagingStart,
      pageLimit :paginLimit
    },
    success :function (ajaxData)
    {
      $.each(ajaxData, function (k, v)
      {
        var anzahlDatensaetze = ajaxData.length;
        for (var i = 1; i <= pagingLimit; i++)
        {
          if (i < anzahlDatensaetze)
          {
          }
        }
      });
    }
  });
}

$(document).ready(function ()
{

});
