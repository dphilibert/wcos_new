var scrambleLoginData = function ()
{
  $(document).ready(function ()
  {
    var username = $("#unscrumbledUsername").val();
    var password = $("#unscrumbledPassword").val();
    $("#username").val($.base64Encode(username));
    $("#password").val($.base64Encode(password));
  });
}

var css3html5Check = function ()
{
  if ($("html.boxshadow").length == 0)
  {
    return false;
  }
  else
  {
    return true;
  }
}

var getText = function ()
{
  var meinname = $("#meinname").val();
  $.post("admin/ajax/test", {
    name:meinname
  },
  function (response)
  {
    $("#ajaxText").text(response);
  },
  "text"
  );
}


function AjaxLoadForm(ajaxURL)
{
  var ajaxData = '';
  $.ajax(
  {
    dataType:"json",
    data:{
      anbieterID:$("#anbieterID").val(),
      hash:$("#anbieterHash").val()
    },
    url:ajaxURL,
    async: false,
    success:function (ajaxData)
    {
      $('#[name~="bs_"]').css("cursor", "pointer");
      $('#ajaxReturnData').val (ajaxData.html);
    }
  });
};

$("#bs_alle").css("cursor", "pointer");
$("#bs_alle").click(function ()
{
  alert('foo');
});

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


var getOptionsSysteme = function ()
{
  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();

  var ajaxData = '';
  var ajaxURL = "/system/system.ajax/getoptionssysteme";
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
      $.each(ajaxData ['optionsSysteme'], function (k, v)
      {
        $("#e_systemSelect").append('<option value="' + k + '">' + v + '</option>');
      });
    }
  });
}

$(document).ready(function ()
{
  if (!css3html5Check())
  {
    $("head").append('<link href="/css/nonCSS3.css" media="screen" rel="stylesheet" type="text/css">');
  }

//    $('select').selectbox();   TODO herausfinden wie selectbox generiert wird! 

  if ($("#premiumLevel").val() == 0 && $('#userStatus').val() !=  -1)
  {
    var module = $("#module").val();
    if (module != "stammdaten" && module != "uebersicht" && module != "einfuehrung" && module != "logout")
    {
      fancyNoUse("Sie sind leider (noch) kein Premiumkunde.<br>Um diese Funktionen nutzen zu können, müssen Sie Premiumkunde werden.", function (ret)
      {
      });
    }
  }
  $("#weitereTags").css("cursor", "pointer");
  $("#weitereTags").click(function ()
  {
    elementId = "#tagDialog";
    thetitle = "Test";
    ajaxContent = AjaxLoadForm('/api/pageloader.ajax/load/loadmodule/tags/loadcontroller/tagAssignment/loadaction/overview/page/overview');
    $(elementId).dialog('destroy');
    $(elementId).html(ajaxContent);
    $(elementId).dialog(
    {
      title:thetitle,
      autoOpen:true,
      bgiframe:false,
      modal:true,
      position:'middle',
      width:800,
      buttons:{
        'ok':function ()
        {
          $(elementId).dialog('destroy');
        }
      }
    });
  });

});

