var switchPL2redUE = function ()
{
  $("#accountStatusImageUE").attr ('src', '/images/buttonRed.png');
  $("#accountStatusTextUE").html ('Premium Account ist nicht aktiviert');
  $("#accountStatusSwitcherUE").html ('aktivieren');
}

var switchPL2greenUE = function ()
{
  $("#accountStatusImageUE").attr ('src', '/images/buttonGreen.png');
  $("#accountStatusTextUE").html ('Premium Account ist aktiviert');
  $("#accountStatusSwitcherUE").html ('deaktivieren');
}


var switchPremiumLevelUE = function ()
{

		var ajaxBaseURL = "/uebersicht/uebersicht.ajax/";


		var anbieterID = $("#anbieterID").val ();
		var anbieterHash = $("#anbieterHash").val ();

		var ajaxData = '';
		var ajaxURL = ajaxBaseURL+"switchpremiumlevel";
		$.ajax (
		{
				dataType: "json",
				url: ajaxURL,
				data: {
								anbieterID: anbieterID,
								hash: anbieterHash
							},
				success: function (ajaxData)
													{
                            $("#accountStatusImageUE").removeAttr ('src');
                            if (ajaxData.newPremiumLevel == 0) switchPL2red ();
                            if (ajaxData.newPremiumLevel == 1) switchPL2green ();
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
				'width'				: 400,
				'height'			: 150,
				'autoScale' : false,
				'autoDimensions' : false,
				'transitionIn'		: 'elastic',
				'transitionOut'		: 'elastic',
				'centerOnScroll' : true,
				'hideOnOverlayClick' : true,
				'titleShow' : false,
				'showCloseButton' : true,
				'enableEscapeButton' : true
			}); 


});


