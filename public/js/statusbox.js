$(document).ready(function ()
{
  $("#statusBox").draggable();

  if ($('#userStatus').val() < 0)
  {

    $('#anbieterSelect').change(function ()
    {
     // window.location.href = 'http://'+$('#host').val()+'/'+$('#module').val()+'/'+$('#controller').val()+'/'+$('#action').val()+'/sato/'+$('#anbieterSelect').val ();
      window.location.href = 'http://'+$('#host').val()+'/einfuehrung/index/index/sato/'+$('#anbieterSelect').val ();
    });
  }
});

