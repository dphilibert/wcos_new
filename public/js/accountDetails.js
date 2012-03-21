/**
 * Anzeige der Details des Benutzeraccounts
 *
 * @author Thomas Grahammer
 * @version $id$
 *
 */

/**
 * zeigt roten Button an (Premiumlevel deaktiviert)
 *
 * @return void
 */
var switchPL2red = function ()
{
  $("#accountStatusImage").attr('src', '/images/buttonRed.png');
  $("#accountStatusText").html('Kein Premium');
  $("#accountStatusSwitcher").html('aktivieren');
}

/**
 * zeigt grünen Button an (Premiumlevel aktiviert)
 *
 * @return void
 */
var switchPL2green = function ()
{
  $("#accountStatusImage").attr('src', '/images/buttonGreen.png');
  $("#accountStatusText").html('Premium');
  $("#accountStatusSwitcher").html('deaktivieren');
}

/**
 * wechselt den Premiumlevel
 *
 * @return void
 *
 */
var switchPremiumLevel = function ()
{

  var ajaxBaseURL = "/uebersicht/uebersicht.ajax/";

  var anbieterID = $("#anbieterID").val();
  var anbieterHash = $("#anbieterHash").val();

  var ajaxData = '';
  var ajaxURL = ajaxBaseURL + "switchpremiumlevel";
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
      $("#accountStatusImage").removeAttr('src');
      if (ajaxData.newPremiumLevel == 0) switchPL2red();
      if (ajaxData.newPremiumLevel == 1) switchPL2green();
    }
  });

};

$(document).ready(function ()
{

  var premiumLevel = $("#premiumLevel").val();
  if (premiumLevel == 0) switchPL2red();
  if (premiumLevel == 1) switchPL2green();
  $("#accountStatusSwitcher").click(function ()
  {
    switchPremiumLevel();
  });

});