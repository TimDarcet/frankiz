var tempLayout;
var includedJs = [];
var homeCols = ["COL_LEFT", "COL_MIDDLE", "COL_RIGHT"];

$(function(){
    minimodules();
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
    if ($('.minimodules_zone').size() == 4)
        for (var i in homeCols)
            if ($('#' + homeCols[i]).sortable('toArray').length == 0)
            	$('#' + homeCols[i]).parent().hide();

    if ($('#COL_FLOAT').sortable('toArray').length == 0) {
        $('body').removeClass('enabledAside');
        $('body').addClass('disabledAside');
    }
}

function getLayout()
{
	var cols = {"COL_FLOAT": []};
    $('#COL_FLOAT > li').each(function (k) {
        cols["COL_FLOAT"][k] = $(this).attr('name');
    });

    if($('.minimodules_zone').size() == 4)
    {
	    for (var i in homeCols) {
	    	cols[homeCols[i]] = new Array();
	        $('#' + homeCols[i] + ' > li').each(function (k) {
	            cols[homeCols[i]][k] = $(this).attr('name');
	        });
	    }
    }

    return JSON.stringify(cols);
}

function saveLayout(layout)
{ 
    request({
        "url" : "profile/minimodules/ajax/layout"
       ,"data": layout
       ,"raw" : true
    });
}

function addMinimodule(name, sender, box)
{
    sender.attr('disabled', 'disabled');
    $('body').removeClass('disabledAside').addClass('enabledAside');
    request({
        "url"    : "profile/minimodules/ajax/add"
       ,"data"   : {"name" : name}
       ,"success": function (json) {
                        if (json.css)
                            $.getCSS("css/" + json.css);
                        $('#COL_FLOAT').prepend(json.html);
                        $('#minimodule_'+name).hide();
                        $('#minimodule_'+name).show('slow', function() {
                            sender.removeAttr('disabled');
                            box.addClass('on');
                        });
	               }
    });
}

function removeMinimodule(name, sender, box)
{
    sender.attr('disabled', 'disabled');
    request({
        "url"     : "profile/minimodules/ajax/remove"
       ,"data"    : {"name":name}
       ,"success" : function(json) {
                       sender.removeAttr('disabled');
                       box.removeClass('on');
                        $('#minimodule_'+name).hide('slow', function() {
                            this.parentNode.removeChild(this);
                            cleanEmptyColumns();
                        });
					}
    });
}