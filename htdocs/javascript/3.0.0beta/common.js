$(document).ready(function(){
    $.ajaxSetup({ cache: false });
    $('#errorBox').dialog({
        autoOpen: false,
        width: "25%",
        resizable: false,
        dialogClass: 'alert',
        stack: true,
        modal: true,
        buttons: {
            "Ok": function() {
                $(this).dialog("close");
            }
        }
    })
});

function showError(message)
{
    $('#errorBox').html(message);
    $('#errorBox').dialog('open');
}