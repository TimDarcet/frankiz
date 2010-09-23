var tempLayout;
var minimodulesJs = new Array();
var includedJs = new Array();

$(document).ready(function(){
    minimodules();

    for (name in minimodulesJs)
        if (minimodulesJs[name] != '')
            includeAndRun(name, minimodulesJs[name]);

});

function minimodules()
{
    tempLayout = getLayout();

    if (logged) {
        $('.minimodules_zone').sortable({
             handle: '.head',
             tolerance: 'pointer',
             connectWith: '.minimodules_zone',
             opacity: 0.6,
             revert: false,
             activate: startSorting,
             stop: stopSorting
        });
    }
}

function includeAndRun(name, path)
{
    if ($.inArray(path, includedJs)==-1)
    {
        $.getScript('javascript/3.0.0beta/' + path, function() {
            try {
                eval(name).run();
            } catch (e)
            {};
        });
        includedJs.push(path);
    } else {
        if (eval(name).run) eval(name).run();
    }
}

function startSorting(event, ui)
{
    $('.minimodules_zone').parent().show();
    $('body').removeClass('disabledAside');
    $('body').addClass('enabledAside');
    $('.minimodules_zone').addClass('sorting');
    $('.minimodules_zone').sortable('refreshPositions');
}

function stopSorting(event, ui)
{
    $('.minimodules_zone').removeClass('sorting');
    cleanEmptyColumns();
    
    var currentLayout = getLayout();
    if (tempLayout != currentLayout)
    {
        saveLayout(currentLayout);
        tempLayout = getLayout();
    }
}

function cleanEmptyColumns()
{
    if ($('.minimodules_zone').size() == 4) {
        for (var i = 1; i <= 3; i++)
        {
            if ($('#column'+i).sortable('toArray').length == 0)
                $('#column'+i).parent().hide();
        }
    }

    if ($('#column4').sortable('toArray').length == 0) {
        $('body').removeClass('enabledAside');
        $('body').addClass('disabledAside');
    }
}

function getLayout()
{
    if($('.minimodules_zone').size() == 4)
    {
        var cols = {"c1": [], "c2": [], "c3": [], "c4": []};
        var beginC = 1;
    } else {
        var cols = {"c4": []};
        var beginC = 4;
    }

    for (var c = beginC; c <= 4; c++)
    {
        $('#column' + c + ' > li').each(function (i) {
            cols["c" + c][i] = $(this).attr('name');
        });
    }

    return JSON.stringify(cols);
}

function saveLayout(layout)
{ 
    request({
        "url" : "minimodules/ajax/layout"
       ,"data": layout
       ,"raw" : true
    });
}

function addMinimodule(name, sender)
{
    sender.disabled = true;
    $('body').removeClass('disabledAside');
    $('body').addClass('enabledAside');
    request({
        "url"  : "minimodules/ajax/add"
       ,"data" : {"name" : name}
    });
    request({
        "url"    : "minimodules/ajax/get"
       ,"data"   : {"name" : name}
       ,"success": function (json) 
               {
                $('#column4').prepend(json.html);
                $('#minimodule_'+name).hide();
                $('#minimodule_'+name).show('slow', function() {
                    sender.disabled = false;
                    if (json.js && json.js != '')
                        includeAndRun(json.name, json.js);
                });
               }
    });
}

function removeMinimodule(name, sender)
{
    sender.disabled = true;
    request({
        "url"     : "minimodules/ajax/remove"
       ,"data"    : {"name":name}
       ,"success" : function(json) { sender.disabled = false; }
    });
    $('#minimodule_'+name).hide('slow', function() {this.parentNode.removeChild(this); cleanEmptyColumns();});
}