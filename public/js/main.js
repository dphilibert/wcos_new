var fancy_config = {};
var mce_config = {};

//Konfiguration Fancy-Box und tinyMCE
$(document).ready(function()
{
  fancy_config = {    
    'autoSize' : false,        
    'autoCenter' : false,
    'fitToView' : false,
    'helpers' : {overlay : null}    
  };    
  mce_config = {  
    selector                       : "textarea",
    mode                           :"textareas",
    theme                          :"advanced",
    plugins                        :"safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",       
    theme_advanced_buttons1        :"pastetext,pasteword,|,bold,italic,underline,strikethrough,|, link,unlink,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,justifyfull,hr,removeformat,visualaid,separator,sub,sup,separator,charmap,code",    
    paste_auto_cleanup_on_paste : true,
    paste_remove_styles: true,
    paste_remove_styles_if_webkit: true,
    paste_strip_class_attributes: true,    
    theme_advanced_buttons2        :"",
    theme_advanced_buttons3        :"",
    theme_advanced_toolbar_location:"top",
    theme_advanced_toolbar_align   :"left",
    entity_encoding                :"raw",
    dialog_type                    :"modal",
    forced_root_block              :"",
    force_br_newlines              :false
  }

  $(function($){
	$.datepicker.regional['de'] = {
		closeText: 'schließen',
		prevText: '&#x3c;zurück',
		nextText: 'Vor&#x3e;',
		currentText: 'heute',
		monthNames: ['Januar','Februar','März','April','Mai','Juni',
		'Juli','August','September','Oktober','November','Dezember'],
		monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
		'Jul','Aug','Sep','Okt','Nov','Dez'],
		dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
		dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
		dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
		weekHeader: 'Wo',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['de']);
  });
  $(function() {$( "input[id^=datepicker_]" ).datepicker ();});  
});

/* Bestaetigungsdialog Loeschen */
function fancyConfirm (callback)
{  
  var ret;
  $.fancybox({ 
    closeBtn : false,
    helpers : {overlay : null},
    content   :'<h3>Sind Sie sicher?</h3>' + 
               '<button class="btn btn-small" id="fancyConfirm_ok" style="margin-right:30px;"><b>Löschen</b></button>' + 
               '<button class="btn btn-small" id="fancyConfirm_cancel"><b>Abbrechen</b></button>',
    afterShow : function ()
    {
      $("#fancyConfirm_cancel").click(function () {ret = false;$.fancybox.close();})
      $("#fancyConfirm_ok").click(function () {ret = true;$.fancybox.close();})
    },
    afterClose : function () {callback.call(this, ret);}
  });  
}

//Absenden eines Fancy-Box Ajax-Formulars - Holen des Formulars mit
//Fehlermeldungen oder schließen
function submit_form (url, mce_name, dp_flag)
{     
  mce_name = (mce_name === undefined) ? '' : mce_name;
  dp_flag = (dp_flag === undefined) ? false : dp_flag;
  var form_data = $(".form-horizontal").serialize ();    
     
  if (mce_name.length > 0)
  {    
    $('[name='+ mce_name + ']').html ('');                            
    var old = new RegExp (mce_name + '=' + '(.*?&)?');
    form_data = form_data.replace (old, mce_name+'=' + encodeURIComponent (tinyMCE.activeEditor.getContent ()) + '&');    
  }        
  $.ajax ({    
    url: url,
    type: "POST",
    data: form_data,
    complete: function (response, status)
    {             
      if (response.responseText == 'success')
      {
        $.fancybox.close ();        
        reload_list ();
      } else
      {        
        $(".fancybox-inner").html (response.responseText);
        if (mce_name.length > 0)                   
          tinyMCE.init (mce_config);  
        if (dp_flag === true)
        {
          $('#beginn').datepicker ();
          $('#ende').datepicker ();
        }  
        
      }  
    }        
  });  
}

//Ausfuehren einer Controller-Action - optional mit Bestaetigungsdialog
function call_action (url, confirm)
{
  if (confirm === true)
  {        
    fancyConfirm (function (answer)
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
function call_action_fancy (url, width, height, mce_flag, dp_flag)
{
  var config = fancy_config;
  config ['autoSize'] = false;
  config ['content'] = null;
  config ['width'] = width;
  config ['height'] = height;
  config ['type'] = 'ajax';
  config ['href'] = url;      
  if (mce_flag === true)
    config ['afterShow'] = function () {tinyMCE.init (mce_config);}
  if (dp_flag === true)
    config ['afterShow'] = function () {$('#beginn').datepicker ();$('#ende').datepicker ();}
  $.fancybox (config);
}

//Listenansicht aktualisieren
function reload_list ()
{
  var search_term = $('#search_term').val ();
  var search_term_in_url = window.location.href.search ("/search_term/");  
  if (search_term_in_url == -1 && search_term.length > 0)
    window.location.href = window.location.href + '/search_term/' + search_term;
  else
    window.location.reload ();    
}

/* zur Ueberpruefung ob alle Text-Formular-Felder der Stammdaten ausgefuellt wurden */
function check_form ()
{
  var error = false;  
  $('.form-horizontal input:text').each (
  function (i)
  {                    
    var value = $(this).val ();                              
    $('#error' + $(this).attr ('name')).remove ();
    if (value.length <= 0)
    {      
      if ($(this).next ().attr ('name') === undefined)
        $(this).after ('<span id="error'+ $(this).attr ('name') +'" style="color:red;margin-left:20px;">Fehler!</span>');
      error = true;
    }        
  });  
  if (error !== true) $('.form-horizontal').submit ();          
}

/* zur Ueberpruefung ob die Dati-Formularfelder richtig befuellt wurden */
function check_date_form (row_id)
{
  var error = false; 
  $('#' + row_id).attr ('class', 'info');  
  var start = $('#form-'+ row_id +' input[name=start]').val ();  
  var end = $('#form-'+ row_id +' input[name=end]').val ();    
  if (start.length <= 0 || end.length <= 0)
  {  
    error = true;
  } else
  {    
    var start_parts = start.split ('.');
    var end_parts = end.split ('.');        
    var start_date = new Date (start_parts [2], start_parts [1] - 1, start_parts [0], 0, 0, 0, 0);
    var end_date = new Date (end_parts [2], end_parts [1] - 1, end_parts [0], 0, 0, 0, 0);    
    var now_date = new Date ();
    now_date = now_date.toLocaleDateString ();  
    var date = new Date (now_date);    
    //if (start_date.getTime() > end_date.getTime() || date.getTime () > start_date.getTime ())
    if (start_date.getTime() > end_date.getTime())
      error = true;
  }  
  if (error !== true)
    $('#form-' + row_id).submit ();
  else 
    $('#' + row_id).attr ('class', 'error');
}

/* tauscht die icon-chevrons aus */ 
function chevronToggle (chevron)
{
  var chevron_class = $(chevron).attr ('class');
  if (chevron_class.search ('down') > -1)
    $(chevron).attr ('class', chevron_class.replace ('down', 'up'));
  else if (chevron_class.search ('up') > -1)
    $(chevron).attr ('class', chevron_class.replace ('up', 'down'));
}

/* Anbieterspektrum Produktcodewechsel */
function code_toggle (code, action)
{  
  //andere aktion unterbinden - entweder hinzufuegen oder entfernen
  var blocked_action = (action == 'add') ? 'remove' : 'add';
  $('#code-'+ blocked_action).attr ('class', 'btn btn-large disabled');
  $('[id^='+ blocked_action +'_]').attr ('style', 'background-color:#f3f3f3;');
  $('#values_'+ blocked_action).val ('');
  
  var attr = $('#'+action+ '_' + code).attr ('style');
  if (attr === undefined) attr = '';    
  if (attr.search (/orange/) != -1)
  {        
    //deselect
    var values_now = $('#values_'+ action).val ();
    if (values_now === undefined) values_now = '';
    $('#values_'+ action).val (values_now.replace (code + ',', ''));    
    $('#'+action+ '_' + code).attr ('style', 'background-color:#f3f3f3;');     
    //button deaktivieren wenn kein Produktcode mehr ausgewählt
    var any_checked = false;          
    $('li[id^=' + action + ']').each (function (){
      var any_checked_check = ($(this).attr ('style') === undefined) ? '' : $(this).attr ('style');
      if (any_checked_check.search (/orange/) != -1) {any_checked = true;return false;}  
    });  
    if (any_checked == false)  $('#code-'+ action).attr ('class', 'btn btn-large disabled');
  } else
  {    
    //select
    $('#'+action+ '_' + code).attr ('style', 'background-color:orange;');
    var values = $('#values_'+ action).val ();
    $('#values_'+ action).val (values + code + ',');  
    //button aktivieren wenn ein produktcode ausgewählt
    $('#code-'+ action).attr ('class', 'btn btn-large');      
  }          
}

/* Anbieterspektrum aktualisieren */
function update_codes (action, button)
{
  if ($(button).attr ('class').search (/disabled/) == -1)
  {
    $.ajax ({    
      url: '/produkte/index/'+ action,
      type: "POST",
      data: 'codes=' + $('#values_'+ action).val (),
      complete: function (response, status){$('#provider_product_tree').html (response.responseText);$('#code-'+ action).attr ('class', 'btn btn-large disabled')}    
    });
  }
}

/* Auswahl/Abwahl aller Produktcodes */
function code_toggle_all (id, action, icon)
{
  var icon_class = $(icon).attr ('class');
  var icon_class_toggle = (icon_class.search (/ok/) != -1) ? 'icon-remove' : 'icon-ok';
  $(icon).attr ('class', icon_class_toggle);
    
  var select_action = (icon_class_toggle == 'icon-remove') ? 'select' : 'deselect';  
  $('ul[id=' + id + '] > li').each (function (){    
    var attr = $(this).attr ('style');
    if (attr === undefined) attr = '';    
    var selected = (attr.search (/orange/) != -1) ? true : false;         
    if ((select_action == 'select' && selected == true) || (select_action == 'deselect' && selected == false)) return '';    
    code_toggle ($(this).attr ('id').replace (action + '_', ''), action);    
  });
  
}

/* Dateiupload - fuehrt die Ajax-Dateiuebertragung durch und befuellt das Formular */
function upload (file_input)
{
  var file_data = file_input.files [0];
  $('#file_error').remove ();
      
  if (file_data.type.search (/image/) != -1) 
  {    
    readFile (file_data, function (file, evt){             
      $.ajax ({    
      url: '/admin/index/upload',
      type: "POST",   
      data: {
        filename : file_data.name,
        image : evt.target.result
      },
      complete : function (response, status) {        
        var data = JSON.parse (response.responseText);        
        $('#file_name').val (data ['name']);
        $('#file_name_orig').val (data ['orig']);                     
        var orig_filename = (data ['orig'].length > 27) ? data ['orig'].substr (0, 23) + '...' : data ['orig'];        
        $(file_input).after ('<div id="upload_info" class="alert alert-success" style="width:190px;padding-left:5px;padding-right:25px;"><b>'+ orig_filename +
          '</b><button type="button" class="close" style="font-size:17px;" onclick="remove_file (\'' + data ['name'] + '\');">x</button></div>');
        $(file_input).remove ();
      }
      });
    });
  } else
  {
    $(file_input).val ('');
    $(file_input).after ('<ul class="errors" id="file_error"><li>Dateiformat nicht erlaubt</li></ul>');        
  }   
}

/* Filereader fuer Ajax-Dateiupload */
function readFile (file, callback)
{
  var reader = new FileReader ();
  reader.onload = function (evt)
  {
    if (typeof callback == 'function') callback (file, evt);
  }  
  reader.readAsDataURL (file);
}

/* Loeschen des hochgeladenen Bildes */
function remove_file (file_name)
{  
  $('#upload_info').after ('<input type="file" name="image" id="image" onchange="upload (this);">');
  $('#file_name').val ('');
  $('#file_name_orig').val ('');
  $('#upload_info').remove ();
  delete_file (file_name);
}

/* Nimmt das Loeschen eines Bildes vor */
function delete_file (file_name, reload_flag, media_id)
{
  var params = {};
  params ['file'] = file_name;
  if (media_id !== undefined)
    params ['media_id'] = media_id;    
  $.ajax ({url: '/admin/index/remove',type: 'POST', data: params,
    complete : function (response, status) {if (reload_flag === true) {$.fancybox.close ();reload_list ();}}    
  });    
}

/* Zeigt ein Bild in der fancyBox  */
function show_preview (image, embedded_flag, media_id)
{
  var config = fancy_config;
  config ['autoSize'] = true;  
  config ['content'] = (embedded_flag === true) ? image : '<img src="/uploads/' + image + '" />';     
  if (media_id !== undefined)
    config ['content'] += '<br><button class="btn btn-small" onclick="delete_file (\''+ image +'\', true, '+ media_id +');"><i class="icon-trash"></i></button>';  
  $.fancybox (config);
}