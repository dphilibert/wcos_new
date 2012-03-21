/**
 * neue Pagination
 *
 * @param pages
 */

$(document).ready(function ()
{
  $('.paginator_pageNo').html(page);
  $('.paginator_maxPages').html(anzahlPages);
  $('.paginatorPageBwd').click(function ()
  {
    if (page > 1)
    {
      page = page - 1;
      startPagination = Number(startPagination) - Number(anzahlPerPage);
      endPagination = Number(endPagination) - Number(anzahlPerPage);
      $('.paginator_pageNo').html(page);
      $('.paginator_maxPages').html(anzahlPages);
      BuildAjaxGrid();
    }
  });

  $('.paginatorPageFwd').click(function ()
  {
    if (page < anzahlPages)
    {
      page = page + 1;
      startPagination = Number(startPagination) + Number(anzahlPerPage);
      endPagination = Number(endPagination) + Number(anzahlPerPage);
      BuildAjaxGrid();
      $('.paginator_pageNo').html(page);
      $('.paginator_maxPages').html(anzahlPages);
    }
  });
});

/**
 * der Paginator des Grids
 *
 * @depricated aus Kompatibilität noch drin lassen!
 * @param pages
 */
var gridPaginator = function (pages)
{
  $("#pagination").html('<span class="button" id="zurueck"><< zur&uuml;ck</span>');
  for (var i = 1; i <= pages; ++i)
  {
    myClass = "button";
    if (i == page) myClass = "paginationListeLeer";
    $("#pagination").html($("#pagination").html() + '<span class="' + myClass + '" id="page' + i + '">' + i + '</span>');
  }
  $("#pagination").html($("#pagination").html() + '<span class="paginationListeLeer">&nbsp;</span>');

  $("#pagination").html($("#pagination").html() + '<span class="button" id="weiter">>> weiter</span>');
  $("#maxRows").change(function ()
  {
    rowsPerPage = $("#maxRows").val();
    showAjaxGrid();
  });

  $("#zurueck").click(function ()
  {
    if (page > 1)
    {
      page--;
      showAjaxGrid();
    }
  });
  $("#weiter").click(function ()
  {
    if (page < pages)
    {
      page++;
      showAjaxGrid();
    }
  });

  for (var i = 1; i < pages; i++)
  {
    $("#page" + i).click(function ()
    {
      page = i;
      $("#page" + i).addClass('aktuell');
      showAjaxGrid();
    });
  }
}

