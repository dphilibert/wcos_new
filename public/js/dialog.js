/**
 * Confirm-Dialog
 *
 * @param thetitle Titel
 * @param themessage Nachricht
 *
 * @return void
 */
var showConfirmDialog = function (thetitle, themessage)
{
  $(document).ready(function ()
  {
    var elementId = '#dialogBox';

    $(elementId).dialog('destroy');
    $(elementId).html(themessage);
    $(elementId).dialog({
      title   :thetitle,
      autoOpen:true,
      bgiframe:false,
      modal   :true,
      position:'middle',
      buttons :{
        'ok':function ()
        {
          $(elementId).dialog('destroy');
        }
      }
    });
  });
};

/**
 * Dialog mit ja/nein Button
 * @param thetitle Titel
 * @param themessage Nachricht
 *
 * @return void
 */
var showYesNoDialog = function (thetitle, themessage)
{
  $(document).ready(function ()
  {
    var elementId = '#dialogBox';

    $(elementId).dialog('destroy');
    $(elementId).html(themessage);
    $(elementId).dialog({
      title   :thetitle,
      autoOpen:true,
      bgiframe:false,
      modal   :true,
      position:'middle',
      buttons :{
        'Ja'  :function ()
        {
          window.location.href = $.base64Decode($('#back_to_url').val());
        },
        'Nein':function ()
        {
          $(elementId).dialog('destroy');
        }
      }
    });
  });
};

/**
 * Nachrichten-Popup
 * @depricated
 * @param message Nachricht
 *
 * @return void
 */
var popupMessage = function (message)
{
  $("#popupMessage").dialog("destroy");
  $("#popupMessage").text(message);
  $("#popupMessage").dialog(
  {
    height   :200,
    width    :300,
    title    :"ACHTUNG!",
    modal    :true,
    resizable:false,
    stack    :true,
    draggable:false,
    buttons  :{
      "OK":function ()
      {
        $(this).dialog("close")
      }
    }
  });
}

/**
 * Lightbox (Fancboy) Confirm-Dialog
 * @param msg Nachricht
 * @param callback Callback-Funktion (referenz)
 *
 * @return void
 */
function fancyConfirm(msg, callback)
{
  $(document).ready(function ()
  {
    var ret;
    jQuery.fancybox({
      modal     :true,
      content   :"<div style=\"margin:1px;width:240px;\">" + msg + "<div style=\"text-align:right;margin-top:10px;\"><input id=\"fancyConfirm_cancel\" style=\"margin:3px;padding:0px;\" type=\"button\" value=\"Nein\"><input id=\"fancyConfirm_ok\" style=\"margin:3px;padding:0px;\" type=\"button\" value=\"Ja\"></div></div>",
      onComplete:function ()
      {
        jQuery("#fancyConfirm_cancel").click(function ()
        {
          ret = false;
          jQuery.fancybox.close();
        })
        jQuery("#fancyConfirm_ok").click(function ()
        {
          ret = true;
          jQuery.fancybox.close();
        })
      },
      onClosed  :function ()
      {
        callback.call(this, ret);
      }
    });
  });
}

/**
 * Lightbox (Fancybox) Info-Dialog
 *
 * @param content Inhalt
 * @param width Breite
 * @param height Höhe
 *
 * @return void
 */
function fancyInfoBox(content, width, height)
{
  $(document).ready(function ()
  {
    if (width == 0) width = 537;
    if (height == 0) height = 600;
    jQuery.fancybox({
      modal             :true,
      showCloseButton   :false,
      hideOnContentClick:false,
      changeFade        :"slow",
      overlayOpacity    :"0.7",
      autoDimensions    :false,
      width             :width,
      height            :height,
      content           :"<div style=\"margin:1px;width:240px;\">" + content + "<div style=\"text-align:right;margin-top:10px;\"><input id=\"fancyConfirm_ok\" style=\"margin:3px;padding:0px;\" type=\"button\" value=\"OK\"></div></div>",
      onComplete        :function ()
      {
        jQuery("#fancyConfirm_ok").click(function ()
        {
          ret = false;
          jQuery.fancybox.close();
        })
      },
      onClosed          :function ()
      {
      }
    });
  });
}
/**
 * Lightbox (fancybox) - OK-Dialog
 *
 * @param msg Nachricht
 * @param callback Callback-Funktion (referenz)
 *
 * @return void
 */
function fancyNoUse(msg, callback)
{
  $(document).ready(function ()
  {
    var ret;
    jQuery.fancybox({
      modal             :false,
      showCloseButton   :false,
      hideOnContentClick:false,
      changeFade        :"slow",
      overlayOpacity    :"0.3",
      content           :"<div style=\"margin:1px;width:240px;\">" + msg + "<div style=\"text-align:right;margin-top:10px;\"><input id=\"fancyConfirm_ok\" style=\"margin:3px;padding:0px;\" type=\"button\" value=\"OK\"></div></div>",
      onComplete        :function ()
      {
        jQuery("#fancyConfirm_ok").click(function ()
        {
          ret = false;
          jQuery.fancybox.close();
        })
      },
      onClosed          :function ()
      {
        $(location).attr('href', '/stammdaten/index/index');
      }
    });
  });
}

/**
 * Lightbox (fancybox) - Error-Dialog
 * @param msg Nachricht
 *
 * @return void
 */
function fancyError(msg)
{
  $(document).ready(function ()
  {

    var ret;
    jQuery.fancybox({
      modal             :false,
      showCloseButton   :false,
      hideOnContentClick:false,
      changeFade        :"slow",
      overlayOpacity    :"0.3",
      content           :"<div style=\"margin:1px;width:240px;\">" + msg + "<div style=\"text-align:right;margin-top:10px;\"><input id=\"fancyConfirm_ok\" style=\"margin:3px;padding:0px;\" type=\"button\" value=\"OK\"></div></div>",
      onComplete        :function ()
      {
        jQuery("#fancyConfirm_ok").click(function ()
        {
          ret = false;
          jQuery.fancybox.close();
        })
      },
      onClosed          :function ()
      {
      }
    });
  });
}

/**
 * Lightbox (fancybox) - modaler Dialog
 * @param content Nachricht
 *
 * @return void
 */
function fancyModalContent(content)
{
  $(document).ready(function ()
  {

    var ret;
    jQuery.fancybox({
      modal             :true,
      showCloseButton   :false,
      hideOnContentClick:false,
      changeFade        :"slow",
      overlayOpacity    :"0.3",
      content           :content,
      onComplete        :function ()
      {
        jQuery("#fancyConfirm_ok").click(function ()
        {
          ret = false;
          jQuery.fancybox.close();
        })
      },
      onClosed          :function ()
      {
      }
    });
  });
}