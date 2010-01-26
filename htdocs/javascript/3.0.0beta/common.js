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
    
    $('#clubsList').sortable({
         distance: 5,
         tolerance: 'pointer',
         opacity: 0.6,
         revert: false
    });
});

function showError(message)
{
    $('#errorBox').html(message);
    $('#errorBox').dialog('open');
}

function defaultCallback(json) {
    if (!json.done)
    {
        showError(json.message);
    }
};

function request(path, data, callbacks, stringify)
{
    if (!$.isEmptyObject(callbacks)) {
        if (callbacks.custom)
        {
            callback = callbacks.custom;
        } else {
            if (callbacks.success && callbacks.fail) {
                callback = function(json) { if (json.success) { callbacks.success(json); } else { callbacks.fail(json); } };
            } else if (callbacks.success) {
                callback = function(json) { if (json.success) { callbacks.success(json); } else { showError(json.error); } };
            } else if (callbacks.success) {
                callback = function(json) { if (json.fail) { callbacks.fail(json); } };
            }
        }
    } else {
        callback = function(json) { if (!json.success) { showError(json.error); }};
    }

    if (stringify == undefined) stringify = true;
    $.getJSON(path, 'json='+((stringify)?JSON.stringify(data):data), callback);
}
