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

    $('#'+groupTypesToId('club')).sortable({
         distance: 5,
         tolerance: 'pointer',
         containment: 'parent',
         opacity: 0.6,
         revert: false,
         update: function() { orderUpdate('club'); }
    });

    $('#'+groupTypesToId('free')).sortable({
         distance: 5,
         tolerance: 'pointer',
         containment: 'parent',
         opacity: 0.6,
         revert: false,
         update: function() { orderUpdate('free'); }
    });

    makeNav();
});

function showError(message)
{
    $('#errorBox').html(message);
    $('#errorBox').dialog('open');
}

function trueFormReset(target)
{
	$.each($(target).find("input[type=text]"), function(index, value) {
		$(value).val("");
	   });
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
            } else if (callbacks.fail) {
                callback = function(json) { if (!json.success) { callbacks.fail(json); } };
            }
        }
    } else {
        callback = function(json) { if (!json.success) { showError(json.error); }};
    }

    if (stringify == undefined) stringify = true;
    $.getJSON(path, 'json='+((stringify)?JSON.stringify(data):data), callback);
}

function makeNav()
{
    $('#nav_menu > li a.link').hover(
    function(eventObject) {
        $(this).prev().addClass('hover');
    },
    function(eventObject) {
        $(this).prev().removeClass('hover');
    }
    );

    $('#nav_menu > li .arrow').hover(
    function(eventObject) {
        $(this).addClass('hover');
    },
    function(eventObject) {
        $(this).removeClass('hover');
    }
    );

    $('#nav_menu a.link.on').prev().addClass('on');

    $('#nav_menu > li .arrow').each(function (i) {
        if($(this).parent().children('ul').children('li').length > 1)
        {
            $(this).click(function() {
                $(this).parent().children('ul').slideToggle('normal');
                $(this).toggleClass('hidden');
                request('navigation/ajax/layout', {"layout": getNavLayout()});
            });
        } else {
            $(this).addClass('hidden');
        }
        if (nav_layout[$(this).next().attr('href')]) {
            $(this).parent().children('ul').hide();
            $(this).addClass('hidden');
        }
    });
}

function groupTypesToId($type)
{
    switch ($type) {
        case "club": return "nav_club";

        case "free": return "nav_free"

        default: return false;
    }
}

function getNavLayout()
{
    var layout = new Object();
    $('#nav_menu > li .arrow').each(function (i) {
        layout[''+$(this).next().attr("href")] = $(this).hasClass('hidden');
    })
    return layout;
}

function getNavOrder(type)
{
    id = groupTypesToId(type);
    if (id)
    {
        var temp = new Array();
        $('#'+id+' > li').each(function (i) {
            if ($(this).attr('gid')) temp.push($(this).attr('gid'));
        });
        return temp;
    }
    else
    {
        return false;
    }
}

function orderUpdate(type)
{
    request('navigation/ajax/order', {"layout": getNavOrder(type)});
}

$.fn.formToJSON = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function key_exists(key, search) {
    return key in search;
}

