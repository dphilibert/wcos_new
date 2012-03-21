/**
 * suche.js - Ajaxsuche generisch für alle Module
 * Version 1.0
 * copyright (c) 2011 Thomas Grahammer <thomas.grahammer@ndcom.de>
*/

var myFilter = "";

var minZeichen = 0;
var keyCounter = 0;


$(document).ready(function()
{
	$("#searchBox").keyup (function (event)
	{
		
			myFilter = "";
			myFilter = $("#searchBox").val ();
			//myFilter (String.fromCharCode(event.which));
		    BuildAjaxGrid ();
	});
});