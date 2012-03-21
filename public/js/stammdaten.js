var userHash = "";
var loadFormData = function (ID)
{
//alert ($("#i_anrede option[value='Frau']"));
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  var ajaxURL = "/stammdaten/stammdaten.ajax/load/anbieterID/" + anbieterID + "/hash/" + anbieterHash + "/id/" + ID;
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    url:ajaxURL,
    success:function (ajaxData)
    {
      // die Daten des "Forms"
      $('input').each(function (index)
      {
        var field = $(this).attr('id');
        if (field != '')
        {
          var foo = eval("ajaxData." + field);
          $("#" + field).val(eval("ajaxData." + field));
        }
      });
    }
  });
};
var AjaxSave = function (selectedField, selectedValue, ID)
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  $('#tempdata').val($('#tempdata').val() + "|" + selectedField);
  var ajaxURL = "/stammdaten/stammdaten.ajax/save";
  var ajaxData = '';
  $.ajax(
  {   data:{
    anbieterID:anbieterID,
    hash:anbieterHash,
    id:ID,
    field:selectedField,
    value:selectedValue
  },
    dataType:"json",
    url:ajaxURL,
    success:function (ajaxData)
    {
    }
  });
}
var requestPremiumInfo = function (ret)
{
  if (ret == true)
  {
    var anbieterID = $("#anbieterID").val();
    var anbieterHash = $("#anbieterHash").val();
    var ajaxURL = "/stammdaten/stammdaten.ajax/requestpremiuminfo";
    var ajaxData = '';
    $.ajax(
    {
      dataType:"json",
      url:ajaxURL,
      data:{
        anbieterID:anbieterID,
        hash:anbieterHash
      },
      success:function (ajaxData)
      {
        fancyInfoBox("Vielen Dank für Ihre Anfrage. Wir werden Ihnen die gewünschten Informationen zeitnah zukommen lassen.", 300, 100);
        $('#wantPremium').hide();
      }
    });
  }
}
// etwas verbesserte Version als die in admin.user.js :-)
$(document).ready(function ()
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();
  $('#infoPremiumAccount').click(function ()
  {
    fancyInfoBox("<img src=\"/images/screenshot_1.png\">", 0, 0);
  });
  $('#premiumTeaser').click(function ()
  {
    fancyInfoBox("<img src=\"/images/screenshot_1.png\">", 0, 0);
  });
  $('#infoPremiumAccountCheckbox').click(function ()
  {
    // TODO fancybox öffnen mit Info über Kosten usw.
    // TODO Kunde kann das bestätigen oder ablehnen.
    if ($('#infoPremiumAccountCheckbox').is(':checked')) // auf is prüfen weil sie ja schon angecheckt wurde an dieser Stelle!!!
    {
      AjaxLoadForm("/stammdaten/stammdaten.ajax/loadpremiuminfoform");
      var content = $('#ajaxReturnData').val();
      fancyConfirm(content, requestPremiumInfo);
    }
    //requestPremiumInfo ();
  });
  $('#save').click(function ()
  {
    $.modal.close();
  });
  $("input,select").blur(function ()
  {
    var selectedField = $(this).attr('id');
//    var selectedValue = $(eval (selectedField)).val ();
    var selectedValue = $(this).val();
    var ID = $("#stammdatenID").val();
    if ($(this).is('input') || $(this).is('select')) AjaxSave(selectedField, selectedValue, ID);
  });
  loadFormData(anbieterID);
  $('#saveStammdaten').click(function ()
  {
    var tempdata = $('#tempdata').val();
    var ajaxURL = "/stammdaten/stammdaten.ajax/sendchangemail";
    var ajaxData = '';
    $.ajax(
    {
      dataType:"json",
      data:{
        anbieterID:anbieterID,
        hash:anbieterHash,
        tempdata:tempdata
      },
      url:ajaxURL,
      success:function (ajaxData)
      {
        fancyError('Ihre Daten wurden erfolgreich gespeichert!');
      }
    });
  });
});


