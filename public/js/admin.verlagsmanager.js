/**
 * Import-Script via Ajax anstossen
 *
 * @constructor
 */

var AjaxStartImport = function ()
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();

  var ajaxURL = "/admin/verlagsmanager.ajax/import/";
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
    },
    url     :ajaxURL,
    success :function (ajaxData)
    {
      if (ajaxData != 'end')
      {
        AjaxGetImportStatus(0);
      }
    }
  });

}

/**
 * fragt den Import-Status via Ajax ab
 *
 * @param param Prozent
 * @constructor
 */
var AjaxGetImportStatus = function (param)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();

  var ajaxURL = "/admin/verlagsmanager.ajax/getimportstatus/progress/" + param;
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
    },
    url     :ajaxURL,
    success :function (ajaxData)
    {
      if (ajaxData != 'end') // rekursiver Aufruf bis Status = "end"
      {
        $("#fortschritt").html(ajaxData + "%");
        AjaxGetImportStatus(ajaxData);
      }
    }
  });

}

/**
 * Testfunktion für die VM-API
 * @constructor
 */
var AjaxAPITest = function ()
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();

  var ajaxURL = "/admin/verlagsmanager.ajax/apitest";
  var ajaxData = '';

  $.ajax(
  {
    dataType:"json",
    data    :{
      anbieterID:anbieterID,
      hash      :anbieterHash,
    },
    url     :ajaxURL,
    success :function (ajaxData)
    {
      $('#fortschritt').append(ajaxData)
    }
  });

}

$(document).ready(function ()
{
  $("#start_import").click(function ()
  {
    AjaxStartImport();
  });

  $("#vmapi_test").click(function ()
  {
    AjaxAPITest();
  });

});


