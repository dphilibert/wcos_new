function orderPasswort(ret)
{
    if (ret == true)
    {
        var ajaxURL = "/login/login.ajax/passwortvergessen";
        var ajaxData = '';
        $.ajax(
        {
            dataType:"json",
            url:ajaxURL,
            async:true,
            data:{
                username:$('#unscrumbledUsername').val()
            },
            success:function (ajaxData)
            {
                if (ajaxData == 1) fancyError('Wir werden Ihr Anliegen zeitnah bearbeiten und bitten um etwas Geduld'); // TODO automatische Passwortvergabe einbauen
                if (ajaxData < 0) fancyError('Sie haben einen ungültigen Benutzernamen angegeben. Wenn Sie Ihren Benutzernamen nicht mehr wissen, schicken Sie uns bitte eine eMail an <a href="mailto:technik@weka-fachmedien.de">technik@weka-fachmedien.de</a> mit Ihren vollständigen Daten.')
            }
        });
    }
}

$(document).ready(function ()
{
    $('.passwortvergessen').click(function ()
    {
        var benutzername = $('#unscrumbledUsername').val();
        var content = "Soll dem Benutzer '" + benutzername + "' ein neues Passwort geschickt werden?";

        fancyConfirm(content, orderPasswort);

    });
    
    $('.loginPassword').focus(function ()
    {
        $('.forgotPasswordText').css ('opacity', '0');
        $('.forgotPassword').css ('opacity', '0.8');
    });
    
    $('.loginPassword').blur(function ()
    {
        $('.forgotPassword').css ('opacity', '0');
        $('.forgotPasswordText').css ('opacity', '1');
    });
    
    $('.forgotPassword').tipsy();
});


