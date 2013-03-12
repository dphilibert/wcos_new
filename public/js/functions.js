// Globale Einstellungen für Datepicker (jquery Kalender)
var datePickerSettings = {
	dateFormat: 'dd.mm.yy',
	dayNames: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
	dayNamesMin: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
	changeMonth: true,
	changeYear: true,
	monthNames: ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
	monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez']
};

var tinySettingsMinimal = {
	// General options
	//mode : "exact",
	//elements : "tiny",
	
	skin: "o2k7",
	mode : "textareas",
	editor_selector: "tiny",
	theme : "advanced",
	plugins : "paste,fullscreen,tiny_mce_wiris",
	//plugins : "paste,fullscreen",
	//valid_elements : "a[href|target=_blank],strong/b,br,i,u,ul,ol,li,sub,sup,p,img",
	
	
	// Theme options
	theme_advanced_buttons1 : "bold,italic,|,pastetext,pasteword,|,bullist,numlist,|,link,unlink,|,undo,redo,|,search,replace,|,sub,sup,|,fullscreen,|,tiny_mce_wiris_formulaEditor",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	
	//theme_advanced_buttons4 : "moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	

	// Example content CSS (should be your site CSS)
	content_css : "/css/tiny/content.css",

	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "lists/template_list.js",
	external_link_list_url : "lists/link_list.js",
	external_image_list_url : "lists/image_list.js",
	media_external_list_url : "lists/media_list.js",

	// Replace values for the template plugin
	template_replace_values : {username : "Some User", staffid : "991234"}
};

var tinySettingsTable = {
	skin: "o2k7",
	mode: 'textareas',
	editor_selector: 'tinyTable',	
	plugins: 'table,fullscreen,tiny_mce_wiris',
	//plugins: 'table,fullscreen',
	//valid_elements : "a[href|target=_blank],strong/b,br,i,u,ul,ol,li,sub,sup,p,img,table,tr,th,td",
	theme: 'advanced',
	theme_advanced_buttons1: 'delete_col,delete_row,col_after,col_before,row_after,row_before,split_cells,merge_cells,link,unlink,|,sub,sup,|,fullscreen,|,tiny_mce_wiris_formulaEditor',
	theme_advanced_buttons2: '',
	theme_advanced_buttons3: '',
	theme_advanced_toolbar_location: 'top',
	theme_advanced_toolbar_align: 'left',
	theme_advanced_statusbar_location: 'bottom',
	theme_advanced_resizing: true,
	theme_advanced_resize_horizontal: false				
};


$(document).ready(function() {

        $(document).mousemove(function(e) {
            var posX = e.pageX + 10;
            var posY = e.pageY + 25;
            $('#ajaxLoader').css({
                left: posX,
                top: posY
            });
        });

	if($('#tabs').length)
	{
		$("#tabs").tabs();
	}
	
	$(':button').each( function() {
		$(this).addClass('ui-corner-all');
	});
	$(':submit').each( function() {
		$(this).addClass('ui-corner-all');
	});
	
	if($('#section_id_global').val() > 0 && $('.sectionIdHeader').length)
	{
		$('.sectionIdHeader').css('background-color', 'red');
	}
	else if($('.sectionIdHeader').length)
	{
		$('.sectionIdHeader').css('background-color', '');
	}
	
	// Globale Einstellungen für SuggestBox (jquery)
	var suggestBoxSettings = {
		max: 50, 
		minChars: 2,
		width: 350,
		dataType: 'json',
		autoFill: true,
		//mustMatch: true,
		selectFirst:true,
		extraParams: {
	        format: 'json' // pass the required context to the Zend Controller
	    },
		parse: function(response) {
	        var parsed = [];
	        var data = response.data;
	        if(data)
	        {
		        for (var i = 0; i < data.length; i++) {
		            parsed[parsed.length] = {
		                data: data[i],
		                value: data[i].name,
		                result: data[i].name
		            };
		        }
	        }
	
	        return parsed;
	    },
	    formatItem: function(item) {
	    	return item.name;
	    }
	
	};
		
	// alle Felder, die als class "datum" beinhalten, bekommen den datepicker angezeigt
	if($('.datum').length) 
	{
		$('.datum').datepicker(datePickerSettings);
	}
	
	if($(".listTable").length) {
		$(".listTable > tbody > tr").click( 
			function () {
				$(".listTable > tbody > tr").css('background-color', '');
				if(this.className == 'listBackgr' ||  this.className == 'listBackgrEmpty')
				{
					$(this).css('background-color', '#FFFFDD');
				}
		});
	}
		
	// Baum Funktion für Tags uns Sections
	if($(".hasSubtree").length) {
		$(".hasSubtree").dblclick( function() {
			
			var elementId = $(this).attr('id');
			var subTreeId = 'subtree_'+elementId;
				
			$('#'+subTreeId).toggle('fast', function() {
				if('none' == $(this).css('display'))
				{
					$('#'+elementId).css('background', 'url(/img/icons/arrow_right.gif) left no-repeat');
				}
				else
				{
					$('#'+elementId).css('background', 'url(/img/icons/arrow_down.gif) left no-repeat');
				}
			});			
		});
	}
	
	// autocomplete Funktion für Firmen Tags
	if($('#company_name').length)
	{
		$('#company_name')
			.autocomplete('/admin/tag-company/suggest', suggestBoxSettings)
			.result(function(e, data) {
	        	if(data)
	        	{
					$('#company_id').val(data.id);
	        	}
	        });
	}
	
	// autocomplete Funktion für Firmen Tags
	if($('#filter_wor_section__name').length)
	{
		$('#filter_wor_section__name')
			.autocomplete('/admin/section/suggest', suggestBoxSettings)
			.result(function(e, data) {
	        	if(data)
	        	{
					$('#filter_wor_section__id').val(data.id);
					$('#filter_wor_section__name').val(data.name);
					submitList($('#filter_wor_section__id').attr('ajaxurl'));
	        	}
	        });
	}
	
	// autocomplete Funktion für Firmen Tags
	if($('#sel_tag').length)
	{
		$('#sel_tag')
			.autocomplete('/monitor/statistic/suggest-tags', suggestBoxSettings)
			.result(function(e, data) {
	        	if(data)
	        	{
	        		document.getElementById('tag_id').value = data.id;
	        	}
	        });
	}
	
	$(function() {
		$(".accordion").accordion({active:0, autoHeight: false, collapsible: true});
	});
	
	// workaround für Anzeigeproblem mit leeren TDs in IE
	$("td").each(function(index){
		var content = $(this).html();
		if(content.length == 0)
		{
			$(this).html('&nbsp;');
		}
	});
	
});

//generate an ajaxmanager named clearQueue 
$.manageAjax.create('clearQueue', {
    queue: 'clear',
    maxRequests: 1,
    abortOld: true,
    preventDoubbleRequests: true
});

/* universelle AJAX Funktion */
var submitAjaxForm = function (formElementId, url, responseElementId)
{
    var params = $(formElementId).formSerialize();
    
    //console.log($('.ajaxloader'));
    if(responseElementId)
    {

        $.manageAjax.add('clearQueue', {
            beforeSend: function() {
               $('#ajaxLoader').show();
            },
            success: function(response) {
                $(responseElementId).html(response);
                $('#ajaxLoader').fadeOut(2000);
            },
            url: url,
            data: params,
            type: 'post'
        });

    }
    else
    {
        $.post(url, params);
    }
    
     
};

/* Paginator Funktion für die Versendung des Filterformulars */
var submitList = function (url, varname, varvalue)
{
	url = url + '/format/html';
	if (varvalue){
		$("#"+varname).val(varvalue);
	}
	submitAjaxForm('#filter', url, '#ajax_list');
};

/* Paginator Funktion für die Versendung des Filterformulars */
var submitSearchBoxListMain = function (formId, tagId, url, readId)
{
	var val = document.getElementById(readId).value;
	url = url + '/' + readId + '/' + val;
	
	submitAjaxForm(formId, url, tagId);
	$.post('/monitor/statistic/change-rubrik-ext', '', function(response) {
		$('#rubrik_ext').html(response);
	});
};

/* Paginator Funktion für die Versendung des Filterformulars */
var submitSearchBoxListRubrik = function (formId, tagId, url, readId)
{
	var val = document.getElementById(readId).value;
	url = url + '/' + readId + '/' + val;
	
	submitAjaxForm(formId, url, tagId);
};


/* In Edit oder New Modus zurück Button */
var goBack = function(formId, formAction)
{
	formId = '#'+formId;
	
	$(formId).attr('action', formAction);
	$(formId).attr('target', '_self');
	$(formId).submit();
};


/**
* TODO: alle Skripte, die diese Funktion nutzen,
* sollten auf loadListDetailView  umgestellt werden!
*
* Funktion für DHTML Box in Listenansicht
*/
var loaduserdata = function (id){
	var initurl = "/admin/user/list-detail/id/" + id;
	$("#userDetailContainer_" + id).removeClass("listDetail");
	$("#userDetailContainer_" + id).load(initurl);
};

/* Funktion für DHTML Box in Listenansicht */
var loadListDetailView = function (url, id){
	var initurl = url + "id/" + id;
	$(".listDetail").hide();
	$("#listDetailContainer_" + id).load(initurl);
	$('#listDetailContainerRow_' + id).show();
};

/* Funktion für die Anzeige des Loading-Gifs */
var showLoadingGif = function (elementId) {
	$(elementId).css('backgound', 'url(/img/ajax-loader.gif) no-repeat center');
};


/* Funktion für DHTML Box in Listenansicht */
var showDetailView = function (url, title){

	url = url + '/format/html';
	var elementId = "#dialogBox";
	
	$(elementId).dialog('destroy');
	
	$.ajax({
	  	url: url,
	  	cache: false,
	  	success: function(response) {
	    	$(elementId).html(response);
	    	
	    	$(elementId).dialog({
				autoOpen: true,
				bgiframe: true,
				minHeight: 400,
				minWidght: 600,
				height: 400,
				width: 900,
				modal: true,
				title: title,
				close: function() {
					$(this).dialog('destroy');
				},
				buttons: {
					'Schließen': function() {
						$(this).dialog('destroy');
					}
				}
			});
	  	}	  	
	});
};

/* Funktion für DHTML Box in Listenansicht */
var showFormView = function (formElementId, url, title, eid, window_width, disable_save){
	
	if (!window_width){
		window_width = 650;
	}
	
	if (eid){
		var elementId = eid;
		var zidx = 600;
	}
	else {
		var elementId = '#dialogBox';
		var zidx = 500;
	}
	
	url = url + '/format/html';
	
	$(elementId).dialog('destroy');
	
	if (disable_save){
		$(elementId).dialog({
			title: title,
			autoOpen: true,
			bgiframe: true,
			width: window_width,
			modal: true,
			position: 'top',
			zIndex: zidx,
			buttons: {
				'Liste schließen': function() {
					$(elementId).dialog('close');
				}
			}
		});
	}
	else {
		$(elementId).dialog({
			title: title,
			autoOpen: true,
			bgiframe: false,
			width: window_width,
			modal: true,
			position: 'top',
			buttons: {
				'Speichern': function() {
					submitAjaxForm(formElementId, url, elementId);
				},
				'Abbrechen': function() {
					$(elementId).dialog('close');
				}
			}
		});
	}
	
	$.ajax({
	  	url: url,
	  	cache: false,
	  	dataType: 'html',
	  	success: function(data, textStatus){
	  		$(elementId).html(data);
 	  	}
	});

};


/* Bestätigung vor dem Löschen */
var confirmDelete = function (url) {
	var elementId = '#dialogBox';
	
	$(elementId).dialog('destroy');
	$(elementId).html('<p>Soll dieser Datensatz wirklich gel&ouml;scht werden?</p>');
	
	$(elementId).dialog({
		title: 'L&ouml;schung best&auml;tigen',
		autoOpen: true,
		bgiframe: true,
		resizable: false,
		height:140,
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 0.5
		},
		buttons: {
			'Nein': function() {
				$(this).dialog('close');
			},
			'Ja, löschen!': function() {
				window.location.href = url;
				$(this).dialog('close');
			}			
		}
	});
};

var showMessage = function (message, manualDestroy) {

	var elementId = '#dialogBox';

	automaticDestroy = false;
	if(!manualDestroy)
	{
		automaticDestroy = true;
	}
		
	$(elementId).dialog('destroy');
	
	$(elementId).html('<p>'+message+'</p>');
		
	if(automaticDestroy)
	{
		$(elementId).dialog({
			autoOpen: true,
			title: 'Hinweis',
			bgiframe: true,
			modal: true,
			width: 350,
			resizable: false
		});
		window.setTimeout("$('#dialogBox').dialog('destroy')", 2000);
	}
	else
	{
		$(elementId).dialog({
			autoOpen: true,
			title: 'Hinweis',
			bgiframe: true,
			modal: true,
			width: 350,
			resizable: false,
			buttons: {
				'Ok': function() {
					$(elementId).dialog('close');
				}
			}
		});
	}
	$(elementId).dialog('open');
};


/* Für DHTML Box die sich bei Klick drüber legt */
var changeNav = function (id, clsName){
	document.getElementById("userDetailContainer_" + id).className = clsName;
};

/* Navigations Klasse austauschen */
var changeListNav = function (id)
{
	var i;
	var clsName;
	
	for (i = 1; i <= 15; i++){
		
		if (document.getElementById("srow" + i)){
			if (i == id){
				clsName = 'titleThAktiv';
			}
			else {
				clsName = 'titleTh';
			}
			document.getElementById("srow" + i).className = clsName;
		}
	}
};

/* Holt sich per Ajax die Einträge und zeigt die Vorschlagslist an */
var makeSuggestion = function (url, elementId, inputVal) 
{
	if(inputVal.length > 2)
	{
		var params = {'searchPhrase' : inputVal};

		$.ajax({
		  	url: url,
		  	data: params,
		  	cache: false,
			type: 'post',
		  	dataType: 'html',
		  	success: function(response){
		  		$(elementId).hide();
		  		if(response.length) {
			  		$(elementId).html(response);
			    	$(elementId).show();
		    	}
		    },
		  	beforeSend: function() {
		  		showLoadingGif(elementId);
		  	}
		});
	}
	else
	{
		$(elementId).hide();
	}
};


/* setzt den jeweiligen Filter zurück und holt sich die neue Liste */
var clearFilterInput = function (inpId, url) {
	
	var inpVal = $(inpId).val();
	if($(inpId).length &&  inpVal.length > 0)
	{
		$(inpId).val('');
		submitList(url);
	}
};

/* setzt den jeweiligen Filter zurück und holt sich die neue Liste */
var clearFilterInputForDate = function (inpId, url) {
	
	var inpIdFrom 	= inpId + '_from';
	var inpValFrom	= $(inpIdFrom).val();
	
	var inpIdTo 	= inpId + '_to';
	var inpValTo	= $(inpIdTo).val();

	if( ($(inpIdFrom).length && inpValFrom.length > 0) 
		|| 
		($(inpIdTo).length && inpValTo.length > 0) )
	{
		
		$(inpIdFrom).val('');
		$(inpIdTo).val('');
		submitList(url);
	}
};


/* setzt den jeweiligen Filter zurück und holt sich den neuen Baum */
var refreshTreeView = function (elementId, inpId, url, resetFilter) {
	
	var searchInputId 	= '#treeFilter';
	var firstInputId 	= '#firstLetter';
	var oldInputValue 	= $(searchInputId).val();

	if(resetFilter === true)
	{
		 $(searchInputId).val('');
		 $(firstInputId).val('');
	}
	
	var searchPhrase 	= $(searchInputId).val();
	var firstLetter 	= $(firstInputId).val();
	
	params = {'searchPhrase' : searchPhrase, 'firstLetter' : firstLetter};
	
	if(searchPhrase.length < 3 && (firstLetter && firstLetter.length < 1))
	{
		return false;
	}

	if(searchPhrase.length > 0 || (resetFilter && oldInputValue.length > 0) || firstLetter.length > 0)
	{
		$.manageAjax.add('clearQueue', { 
			beforeSend: function() {
                            $('#ajaxLoader').show();
                        },
                        success: function(response) {
                            $(elementId).html(response);
                            $('#ajaxLoader').fadeOut(2000);
			}, 
			url: url + '/format/html', 
			data: params,
			type: 'post'
		});
		/*
		$.ajax({
		  	url: url + '/format/html',
		  	data: params,
		  	cache: false,
			type: 'post',
		  	dataType: 'html',
		  	success: function(response){
			  	$(elementId).html(response);
		    },
		  	beforeSend: function() {
		  		showLoadingGif(elementId);
		  	}
		});
		*/
	}
	else
	{
		$(elementId).html('<p><br />Benutzen Sie bitte die Eingabe, um die Tags anzuzeigen.</p>');
	}
};

var showTagAssignment = function(url, title, elementId)
{
	if(!elementId)
	{
		var elementId = '#dialogBox';
	}
	
	$(elementId).html();
	$(elementId).dialog('destroy');
	
	$(elementId).dialog({
		title: title,
		autoOpen: false,
		bgiframe: true,
		width: 900,
		modal: true,
		position: 'top',
		zIndex: 500,
		buttons: {
			'Ok': function() {
				$(elementId).dialog('close');
			}
		}
	});
	
	$.ajax({
	  	url: url,
	  	cache: false,
	  	dataType: 'html',
	  	success: function(data, textStatus){
	  		$(elementId).html(data);
 	  	},
	   	beforeSend: function() {
	  		showLoadingGif(elementId);
	  	}
	});
	
	$(elementId).dialog('open');
};

var addTagElement = function (clickedElement, tagId, tagName, tagType)
{
	var elementId	= '#tagContainer_'+tagType;
	
	var url 		= '/admin/tag-assignment/add-new-tag';
	var params		= ({'format' : 'html', 'tagId' : tagId, 'tagName' : tagName,  'tagType' : tagType});
	
	$.ajax({
	  	url: url,
	  	cache: false,
	  	dataType: 'html',
	  	data: params,
	  	type: 'post', 
	  	success: function(data, textStatus){
	  		$(elementId).append(data);
	  		$('#'+tagId).addClass('notSelectable');
	  		//clickedElement.className = clickedElement.className + ' notSelectable';
	  		$(clickedElement).removeAttr('onclick');
 	  	},
	   	beforeSend: function() {
	  		showLoadingGif(elementId);
	  	}
	});
};

var addTagSearchElement = function (clickedElement, tagId, tagName, tagType)
{
	clickedElement.className = clickedElement.className + ' notSelectable';
	
	var url 		= '/admin/tag-assignment/add-new-search-tag';
	var params		= ({'format' : 'html', 'tagId' : tagId, 'tagName' : tagName,  'tagType' : tagType});
	
	var count_nr = $('#tag_ids_count').html();
	count_nr = parseInt(count_nr);
	count_nr = count_nr + 1;
	$('#tag_ids_count').html(count_nr);
	
	$('#tag_hidden_value').hide();
	$('.tag_ids_hidden_cont').show();
		
	$.post(url, params, function(response) {
		$('#tag_ids_hidden').append(response);
		submitTagSearchList();
	});
	
	handleBgColorForTagsearch();
};

var deleteTagSearchElement = function (tagId)
{
	$('#tag_search_' + tagId).remove();
	
	var count_nr 	= $('#tag_ids_count').html();
	var url 		= '/admin/tag-assignment/remove-tag-from-session';
	var params		= ({'format' : 'html', 'tagId' : tagId});
	
	count_nr = parseInt(count_nr);
	count_nr = count_nr - 1;
	$('#tag_ids_count').html(count_nr);
	
	
	
	// falls der Tag auch aus der Session gelöscht werden soll
	// dann diese Kommentare entfernen:
	
	$.post(url, params, function(response) {
		submitTagSearchList();
	});
	
	handleBgColorForTagsearch();
	
};

var undoTagDeletion = function ()
{
	var count_nr 	= $('#tag_ids_count').html();
	var url 		= '/admin/tag-assignment/undo-tag-search-deletion';
	var params		= ({'format' : 'html'});
	
	// falls der Tag auch aus der Session gelöscht werden soll
	// dann diese Kommentare entfernen:
	
	$.ajax({
	  	url: url,
	  	cache: false,
	  	data: params,
	  	success: function(response) {
	  		$('#tagListContainer').html(response);
	  	}	  	
	});
	
	/*
	$.post(url, params, function(response) {
		submitTagSearchList();
	});
	*/
	
};

var submitTagSearchList = function ()
{
	var search_url = $('#tag_search_url').val();
	submitList(search_url);
};


var removeTagElement = function (elementId, tagId)
{
	$('#'+elementId).remove();
	
	var url 		= '/admin/tag-assignment/remove-tag-from-session';
	var params		= ({'format' : 'html', 'tagId' : tagId});
	
	$.post(url, params, function(response) {
	});
};

var timeout = null;
var timeoutFunction = function (functionName, name, time)
{
	clearTimeout(timeout);
  	eval('timeout = setTimeout("' + functionName + '(\'' + name + '\')", ' + time + ')');
};

var setSectionIdForAdmin = function (element)
{
	var sectionId = $(element).val();
	var url 	= '/admin/section/set-section-id-for-admin';
	var params	= ({'format' : 'html', 'section_id' : sectionId});
	
	$.ajax({
	  	url: url,
	  	cache: false,
	  	dataType: 'html',
	  	type: 'post',
	  	data: params, 
	  	success: function(data, textStatus){
	  		window.location.reload();
	  	}
	});
};

var fillCcDropdown = function (selectedElement, targetElement)
{
	var systemId = $(selectedElement).val();
	
	targetElement = '#'+targetElement;
	var url = '/admin/section/get-sections-for-dropdown';
	var params = ({'format' : 'html', 'system_id' : systemId});
	
	$.ajax({
	  	url: url,
	  	cache: false,
	  	dataType: 'html',
	  	type: 'post',
	  	data: params, 
	  	success: function(data, textStatus){
	  		$(targetElement).html(data);
	  	}
	});
};

var deleteCurrentImage = function ()
{
	$('#currentImage').html('');
	$('#delete_current_image').val(1);
};

var addDate = function ()
{
	submitAjaxForm('#scheduleForm', '/editor/schedule/add-date/add_wid/1/', '#dateContainer');
};

//Whitepaper bei Firmenprofilen hinzufügen
var delDate = function (wid){
	
	submitAjaxForm('#scheduleForm', '/editor/schedule/delete-date/del_wid/' + wid, '#dateContainer');
};

var handleBgColorForTagsearch = function () {
	
	var tagCounter = $('#tag_ids_count').html();
	
	if(tagCounter > 0)
	{
		$('#tagFilterContainer').css('background-color', '#FFD9D9');
	}
	else
	{
		$('#tagFilterContainer').css('background-color', '#FFF');
	}
};

var toggleBox = function(boxId, clickedElement)
{
	$(boxId).slideToggle('normal'); 
	
	var height = $(boxId).css('height');
	
	var currentText = $(clickedElement).html();

	if(currentText == 'einblenden')
	{
		$(clickedElement).html('ausblenden');
	}
	else
	{
		$(clickedElement).html('einblenden');
	}
};

var disablePackage = function (packageId, companyId)
{
	var url = '/admin/company-login/disable-package';
	var params	= {'format':'html', 'package_id':packageId, 'company_id':companyId};
	var elementId = "#dialogBox2";
	
	$(elementId).dialog('destroy');
	$(elementId).html('<p>Wollen Sie dieses Paket wirklich löschen?</p>');
	
	$(elementId).dialog({
		title: 'L&ouml;schung best&auml;tigen',
		autoOpen: true,
		bgiframe: true,
		resizable: false,
		height:140,
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 0.5
		},
		buttons: {
			'Nein': function() {
				$(this).dialog('close');
			},
			'Ja': function() {
				$.ajax({
				  	url: url,
				  	cache: false,
				  	dataType: 'html',
				  	type: 'post',
				  	data: params, 
				  	success: function(data, textStatus){
				  		$('#dialogBox').html(data);
				  	}
				});
				$(this).dialog('close');
			}			
		}
	});
};
