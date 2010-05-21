var tempLayout;
var minimodulesJs = new Array();
var includedJs = new Array();

$(document).ready(function(){
    tempLayout = getLayout();

    $('.minimodules_zone').sortable({
         handle: '.head',
         tolerance: 'pointer',
         connectWith: '.minimodules_zone',
         opacity: 0.6,
         revert: false,
         activate: startSorting,
         stop: stopSorting
    });
    
    for (name in minimodulesJs)
    {
        if (minimodulesJs[name] != '')
        {
            includeAndRun(name, minimodulesJs[name]);
        }
    }
});

function includeAndRun(name, path)
{
    if ($.inArray(path, includedJs)==-1)
    {
        $.getScript('javascript/3.0.0beta/'+path, function() {
            if (eval(name).run) eval(name).run();
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
        var cols = {1: [], 2: [], 3: [], 4: []};
        var beginC = 1;
    } else {
        var cols = {4: []};
        var beginC = 4;
    }

    for (var c = beginC; c <= 4; c++)
    {
        $('#column' + c + ' > li').each(function (i) {
            cols[c][i] = $(this).attr('name');
        });
    }

    return JSON.stringify(cols);
}

function saveLayout(layout)
{ 
    request('minimodules/ajax/layout', layout, {}, false);
}

function addMinimodule(name, sender)
{
    sender.disabled = true;
    $('body').removeClass('disabledAside');
    $('body').addClass('enabledAside');
    request('minimodules/ajax/add', {"name":name});
    request('minimodules/ajax/get', {"name":name}, {"success": 
        function (json) {
            $('#column4').prepend(json.html);
            $('#minimodule_'+name).hide();
            $('#minimodule_'+name).show('slow', function() { 
                sender.disabled = false;
                if (json.js && json.js != '')
                {
                    includeAndRun(json.name, json.js);
                }
            });
        }
    });
}

function removeMinimodule(name, sender)
{
    sender.disabled = true;
    request('minimodules/ajax/remove', {"name":name}, {"success": function(json) { sender.disabled = false; }});
    $('#minimodule_'+name).hide('slow', function() {this.parentNode.removeChild(this); cleanEmptyColumns();});
}