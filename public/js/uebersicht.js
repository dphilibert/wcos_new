var switchPL2redUE = function ()
{
  $("#accountStatusImageUE").attr ('src', '/images/buttonRed.png');
  $("#accountStatusTextUE").html ('Premium Account ist nicht aktiviert');  
}

var switchPL2greenUE = function ()
{
  $("#accountStatusImageUE").attr ('src', '/images/buttonGreen.png');
  $("#accountStatusTextUE").html ('Premium Account ist aktiviert');  
}

var switchPremiumLevelUE = function ()
{ 
  var url = "/uebersicht/index/status";
  var anbieterID = $("#anbieterID").val ();
      
  $.ajax (
  {    
    url: url,
    data: {anbieterID: anbieterID},          
    complete: function (response, status)
    {                  
      var data = JSON.parse (response.responseText);
      
      if (data.status == 0)
      {        
        switchPL2red ();
        switchPL2redUE ();
      }  
      if (data.status == 1)
      {
        switchPL2green ();
        switchPL2greenUE ();
      }  
    }
  });
  
};

$(document).ready(function()
{
  var premiumLevelUE = $("#premiumLevel").val ();
  if (premiumLevelUE == 0) switchPL2redUE ();
  if (premiumLevelUE == 1) switchPL2greenUE ();
  $("#accountStatusSwitcherUE").click (function ()
  {
    switchPremiumLevelUE ();
  });
   
  $(".changeDataLightbox").fancybox({
    'width' : 400,
    'height' : 150,
    'autoScale' : false,
    'autoDimensions' : false,
    'transitionIn' : 'elastic',
    'transitionOut' : 'elastic',
    'centerOnScroll' : true,
    'hideOnOverlayClick' : true,
    'titleShow' : false,
    'showCloseButton' : true,
    'enableEscapeButton' : true
  }); 
});


