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
        
        // Ajout "à la main" du menu de selection de rajout de minimodule
        var addDiv = document.getElementById('add_minimodule');
        
        var headDiv = document.createElement('div');
        headDiv.setAttribute('class','head');
        addDiv.appendChild(headDiv);
        
        var bodyDiv = document.createElement('div');
        bodyDiv.setAttribute('class','body');
        
        var select = document.createElement('select');
        select.id = 'add_minimodule_select';
        
        var option = document.createElement('option');
        option.innerHTML = "Ajouter un minimodule";
        
        select.appendChild(option);
        
        //lors de la selection d'un minimodule à ajouter
        select.onchange = function(){
                            var selectedOption = this.options[this.selectedIndex];
                            if(selectedOption.value != ''){
                                addMinimodule(selectedOption.value, null, null);
                            }
                            this.selectedIndex = 0;
                        };
        
        //pour éviter un appel Ajax dès la chargement de la page, on attend que l'utilisateur survole le menu
        select.onmouseover = function(){
                            // on supprime cette action car le rafraichissement est appelé lors de l'ajout ou de la suppression d'un minimodule
                            document.getElementById('add_minimodule_select').onmouseover = null;
                            refreshMinimodulesList();
                        };
        
        bodyDiv.appendChild(select);
        addDiv.appendChild(bodyDiv);
        
        setInterval(refreshMinimodules, 120000);
    }
}

function includeAndRun(name, path)
{
    if ($.inArray(path, includedJs)==-1)
    {
        $.getScript('javascript/' + path, function() {
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
    if($('#remove_minimodule_zone li').length != 0){
        var mini = $('#remove_minimodule_zone li');
        removeMinimodule(mini.attr("name"), null, null);
        mini.remove();
    }
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
    if ($('.minimodules_zone').size() == 5) {
        for (var i in homeCols) {
            if ($('#' + homeCols[i]).children('li').length == 0) {
                $('#' + homeCols[i]).parent().hide();
            }
        }
    }

    if ($('#COL_FLOAT').children('li').length == 0) {
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

    if($('.minimodules_zone').size() == 5)
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
    if(sender !== null)
    {
        sender.attr('disabled', 'disabled');
    }
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
                            if(sender !== null && box !== null)
                            {
                                sender.removeAttr('disabled');
                                box.addClass('on');
                            }
                        });
                        refreshMinimodulesList();
                    }
    });
}

function removeMinimodule(name, sender, box)
{
    if(sender !== null)
    {
        sender.attr('disabled', 'disabled');
    }
    request({
        "url"     : "profile/minimodules/ajax/remove"
       ,"data"    : {"name":name}
       ,"success" : function(json) {
                        refreshMinimodulesList();
                        if(sender !== null && box !== null)
                        {
                           sender.removeAttr('disabled');
                           box.removeClass('on');
                        }
                        $('#minimodule_'+name).hide('slow', function() {
                            this.parentNode.removeChild(this);
                            cleanEmptyColumns();
                        });
                    }
    });
}


function refreshMinimodulesList()
{
    document.getElementById('add_minimodule_select').firstChild.innerHTML = 'Mise à jour en cours...';
    request({
        "url"    : "profile/minimodules/ajax/removed"
       ,"success": function (json) {
                        var options = document.getElementById('add_minimodule_select');
                        
                        var len = options.childNodes.length;
                        while(len--> 1){
                            options.removeChild(options.lastChild);
                        }
                        
                        for(var minimodule in json.minimodules){
                                option = document.createElement('option');
                                option.value = minimodule;
                                option.innerHTML = json.minimodules[minimodule];
                                options.appendChild(option);
                        }
                        options.firstChild.innerHTML = 'Ajouter un minimodule';
       }
    });
}

function refreshMinimodules()
{
    request({
        "url"    : "profile/minimodules/ajax/refresh"
       ,"success": function (json) {
                        for(var minimodule in json.ajaxRefresh){
                            document.getElementById(minimodule).innerHTML = json.ajaxRefresh[minimodule];
                        }
       }
    });
}
