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
        reload_list ();
      } else
      {        
        $("#" + form_container).html (response.responseText);
      }  
    }        
  });  
}

//Ausfuehren einer Controller-Action - optional mit Bestaetigungsdialog
var call_action = function (url, confirm)
{
  if (confirm === true)
  {
    fancyConfirm ("Sind Sie sicher?", function (answer)
    {
      if (answer == true)
        $.ajax ({url: url, complete: function (response, status){reload_list ();}});              
    });    
  } else
  {
    $.ajax ({url: url, complete: function (response, status){reload_list ();}});
  }          
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

//Listenansicht aktualisieren
var reload_list = function ()
{
  var search_term = $('#search_term').val ();
  var search_term_in_url = window.location.href.search ("/search_term/");  
  if (search_term_in_url == -1 && search_term.length > 0)
    window.location.href = window.location.href + '/search_term/' + search_term;
  else
    window.location.reload ();    
}