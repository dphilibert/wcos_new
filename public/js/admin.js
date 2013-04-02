var fancy_config = {};

//Konfiguration der Fancy-Box
$(document).ready(function()
{
  fancy_config = {    
    'autoScale' : false,
    'autoDimensions' : false,
    'transitionIn' : 'none',
    'transitionOut' : 'none',
    'centerOnScroll' : true,
    'hideOnOverlayClick' : true,
    'titleShow' : false,
    'showCloseButton' : true,
    'enableEscapeButton' : true
  };     
});

//Absenden eines Fancy-Box Ajax-Formulars - Holen des Formulars mit
//Fehlermeldungen oder schließen
var submit_form = function (url, form_name, form_container)
{          
  $.ajax ({    
    url: url,
    data: $("#" + form_name).serialize (),
    complete: function (response, status)
    {                        
      if (response.responseText == 'success')
      {
        $.fancybox.close ();        
        window.location.reload ();
      } else
      {        
        $("#" + form_container).html (response.responseText);
      }  
    }        
  });  
}

//Ausfuehren einer Controller-Action
var call_action = function (url)
{
  $.ajax ({    
    url: url,    
    complete: function (response, status)
    {            
      window.location.reload ();
    }        
  });  
}

//Ausfuehren einer Ajax-Controller-Action mit Rueckgabe in Fancy-Box
var call_action_fancy = function (url, width, height)
{
  fancy_config ['width'] = width;
  fancy_config ['height'] = height;
  fancy_config ['type'] = 'ajax';
  fancy_config ['href'] = url;
    
  $.fancybox (fancy_config);
}