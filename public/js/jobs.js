
var ajaxBaseURL = "/jobs/jobs.ajax/";

var startPagination = 0;
var endPagination = 10;
var anzahlPerPage = 10;
var anzahlPages = 1;
var page = 1;


var BuildAjaxGrid = function ()
{
  $("tr[name='ajaxZeile']").remove ();
  var anbieterID = $("#anbieterID").val ();
  var anbieterHash = $("#anbieterHash").val ();
		var ajaxData = '';
  var ajaxURL = ajaxBaseURL+"loadlist";
		$.ajax (
		{
				dataType: "json",
				url: ajaxURL,
    data: {
            anbieterID: anbieterID,
            hash: anbieterHash,
          },
				success: function (ajaxData)
													{
               var anzahlDatensaetze = ajaxData.length;
               var ungerundeteSeitenanzahl = anzahlDatensaetze / anzahlPerPage;
               anzahlPages = Math.round (ungerundeteSeitenanzahl);
               endPagination = Number (startPagination) + Number (anzahlPerPage) - 1;
               if (ungerundeteSeitenanzahl > anzahlPages) anzahlPages++;
               $(".paginatorPageNo").html ('Seite '+page+' von '+anzahlPages);
               for (var i=startPagination; i <= endPagination; i++)
               {
                 if (i < anzahlDatensaetze)
                 {
																			var id = ajaxData[i].jobID;
																			$("#null").clone (true).insertAfter ("#null");
																			$("#null:last").attr ('id', id);
																			$("#"+id).attr ('name', 'ajaxZeile');
																			$("#"+id).removeClass ();
																			if (i % 2 == 0) $("#"+id).addClass ('tableListeZeileGrey');
																			if (i % 2 > 0) $("#"+id).addClass ('tableListeZeile');
																			$("#"+id+" td[id='headline']").html (ajaxData[i].headline);
																			$("#"+id+" td[id='link']").html (ajaxData[i].link);
																			$('#'+id+' a[id="edit"]').attr ('onClick', 'editLine ('+id+')');
																			$('#'+id+' a[id="del"]').attr ('onClick', 'deleteLine ('+id+')');
																			$('#'+id).removeAttr ('style');
                 }
               }
													}
		});
}




// die Options werden per Ajax eingelesen. Sinn: Man kann sie aus der Session holen damit der User sie nicht staendig neu auswaehlen muss

var getOptionsPages = function ()
{
  var anbieterID = $("#anbieterID").val ();
  var anbieterHash = $("#anbieterHash").val ();

		var ajaxData = '';
  var ajaxURL = "/system/system.ajax/getpagesoptions";
		$.ajax (
		{
				dataType: "json",
				url: ajaxURL,
    data: {
            anbieterID: anbieterID,
            hash: anbieterHash,
          },
				success: function (ajaxData)
													{
               $.each (ajaxData ['anzahlEintraegeOptions'], function (k,v)
               {
                 $("#anzahlEintraege").append ('<option value="'+k+'">'+v+'</option>');
               });
													}
		});
}


// als Lightbox-Dialog wird SimpleModal verwendet

/*var editLine = function (id)
{
  var anbieterID = $("#anbieterID").val ();
  var anbieterHash = $("#anbieterHash").val ();

  $("#jobs_editor").modal (
  {
    opacity:80,
    overlayCss: { 
                   backgroundColor:"#fff",
                },
    containerCss: { 
                    width:700,
                    height:500,
                  },
    autoResize:true,
    modal:true,
    escClose: false,
    onClose: function (dialog)
             {
															var ajaxURL = "/jobs/jobs.ajax/cleartable";
															var ajaxData = '';
															$.ajax (
															{
																	dataType: "json",
																	url: ajaxURL,
																	data: {
                         anbieterID: anbieterID,
                         hash: anbieterHash,
																							},
																	success: function (ajaxData)
																										{
																										}
															});

               $.modal.close ();
             },
  });
  $("#ID").val (id);
  loadFormData (id);
}*/


var editLine = function (id)
{
  var anbieterID = $("#anbieterID").val ();
  var anbieterHash = $("#anbieterHash").val ();

  $("#edit").fancybox({
				'width'				: 800,
				'height'			: 220,
				'autoScale' : false,
				'autoDimensions' : false,
				'transitionIn'		: 'elastic',
				'transitionOut'		: 'elastic',
				'centerOnScroll' : true,
				'hideOnOverlayClick' : true,
				'titleShow' : false,
				'showCloseButton' : true,
				'enableEscapeButton' : true,
				'onClosed' : function (dialog)
             {
															var ajaxURL = "/jobs/jobs.ajax/cleartable";
															var ajaxData = '';
															$.ajax (
															{
																	dataType: "json",
																	url: ajaxURL,
																	data: {
                         anbieterID: anbieterID,
                         hash: anbieterHash,
																							},
																	success: function (ajaxData)
																										{
																										}
															});

               $.modal.close ();
             },
  });
  $("#ID").val (id);
  loadFormData (id);
}


var deleteLine = function (id)
{
  AjaxDelete (id);
}


var newEntry = function ()
{
  editLine (0);
}


var loadFormData = function (ID)
{
      var anbieterID = $("#anbieterID").val ();
      var anbieterHash = $("#anbieterHash").val ();

      if (ID > 0) // bei ID > 0 Ansprechpartner bearbeiten
      {
        var ajaxURL = "/jobs/jobs.ajax/load";
      }
      if (ID == 0) // bei ID < 0 Ansprechpartner neu anlegen
      {
        var ajaxURL = "/jobs/jobs.ajax/new";
      }
      var ajaxData = '';
      $.ajax (
      {
        dataType: "json",
        url: ajaxURL,
        data: {
                anbieterID: anbieterID,
                hash: anbieterHash,
                id: ID,
              },
        success: function (ajaxData)
                 {
                   // die Daten des "Forms"
                   ID = ajaxData.jobsID;
                   $("#je_headline").val (ajaxData.headline);
                   $("#je_link").val (ajaxData.link);
                   $("#ID").val (ID);
                 }
      });
};


var AjaxSave = function (selectedField, selectedValue, id)
{
  var anbieterID = $("#anbieterID").val ();
  var anbieterHash = $("#anbieterHash").val ();
		var ajaxURL = "/jobs/jobs.ajax/save";
		var ajaxData = '';
		$.ajax (
		{
				dataType: "json",
				url: ajaxURL,
    data: {
                anbieterID: anbieterID,
                hash: anbieterHash,
                id: id,
                field: selectedField,
                value: selectedValue,
          },
				success: function (ajaxData)
													{
               if (selectedField == 'headline') 
               {
                 $("#"+id+" td[id='headline']").html (selectedValue);
               }
               if (selectedField == 'link') 
               {
                 $("#"+id+" td[id='link']").html (selectedValue);
               }
													}
		});

}


var AjaxDelete = function (ID)
{
      var anbieterID = $("#anbieterID").val ();
      var anbieterHash = $("#anbieterHash").val ();

  var ajaxURL = "/jobs/jobs.ajax/del";
		var ajaxData = '';
		$.ajax (
		{
				dataType: "json",
				url: ajaxURL,
    data: {
                anbieterID: anbieterID,
                hash: anbieterHash,
                id: ID,
          },
				success: function (ajaxData)
													{
													}
		});

  $("#"+ID).remove ();
}





$(document).ready(function()
{
  getOptionsPages ();
  BuildAjaxGrid ();

  $("#buttonSave").click (function ()
  {
    $.fancybox.close ();
    BuildAjaxGrid ();
  });

  $("textarea,input,select").blur (function ()
  {
    var selectedField = $(this).attr ('name');
    var selectedValue = $(this).val ();
    var ID = $("#ID").val ();
    AjaxSave (selectedField, selectedValue, ID);
  });

  $("#new").click (function ()
  {
    newEntry ();
  });


  $("#anzahlEintraege").change (function ()
  {
    page = 1;
    startPagination = 1;
    anzahlPerPage = $(this).val ();
    BuildAjaxGrid ();
  });

  $('.paginatorBwd').mouseover (function ()
  {
    $(this).css ('cursor', 'pointer');
  });

  $('.paginatorBwd').click (function ()
  {
    if (page > 1) 
    {
      page = page-1;
      startPagination = Number (startPagination) - Number (anzahlPerPage);
      endPagination = Number (endPagination) - Number (anzahlPerPage);
      BuildAjaxGrid ();
    }
  });

  $('.paginatorFwd').mouseover (function ()
  {
    $(this).css ('cursor', 'pointer');
  });

  $('.paginatorFwd').click (function ()
  {
    if (page < anzahlPages)
    {
      page = page+1;
      startPagination = Number (startPagination) + Number (anzahlPerPage);
      endPagination = Number (endPagination) + Number (anzahlPerPage);
      BuildAjaxGrid ();
    }
  });
  
  
  
  
    $("#lightboxOpen").fancybox({
				'width'				: 800,
				'height'			: 130,
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
			
	  $("#edit").fancybox({
				'width'				: 800,
				'height'			: 130,
				'autoScale' : false,
				'autoDimensions' : false,
				'transitionIn'		: 'elastic',
				'transitionOut'		: 'elastic',
				'centerOnScroll' : true,
				'hideOnOverlayClick' : true,
				'titleShow' : false,
				'showCloseButton' : true,
				'enableEscapeButton' : true,
			});
			
	 $("#del").fancybox({
				'width'				: 800,
				'height'			: 400,
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


