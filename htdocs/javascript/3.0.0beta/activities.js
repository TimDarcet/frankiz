$(document).ready(function() {
    $('.hide').hide();
    if ($("#activity_show").html() == '\n')
        $("#activity_show").hide();
   
    $("input[name='admin_id']").change(function(){
        request({ "url": 'activity/ajax/admin'
                ,"data": {'id': $("input[@name='admin_id']:checked").val(), 'admin': true}
             ,"success": function(json) {
                    $("#modify_show").html(json.activity);
             }
        });
        $("#modify_show").show();
    });
    
    $("input[name='aid']").change(function(){
        request({ "url": 'activity/ajax/admin/'
                ,"data": {'id': $("input[@name='aid']:checked").val(), 'regular': true}
             ,"success": function(json) {
                    $("#activity_show").html(json.activity);
             }
        });
        $("#activity_show").show();
    });

    $('#activities_list').delegate('.day .activity', 'hover', function(){
        load(new Array($(this).attr('aid')), true);
    });

    var temp = new Array()
    $('#activities_list .day .activity').each(function(){
        temp.push($(this).attr('aid'));
    });
    load(temp, false);
});


// To load the list of activities on the page activity

function change_view(type)
{
    request({ "url": 'activity/ajax/get/'
            ,"data": {'visibility': type, 'list': true}
         ,"success": function(json) {
                $("#activities_list").html(json.activities);
         }
    });
    
    var temp = new Array()
    $('#activities_list .day .activity').each(function(){
        temp.push($(this).attr('aid'));
    })
    load(temp, false);
}

function load(ids, all)
{
    var lo = new Array();
    $.each(ids, function(index, id) {
        if (!key_exists(id, results))
        {
            lo.push(id);
        }
    });

    if (lo.length !== 0)
    {
        request({ "url": 'activity/ajax/get'
                ,"data": {'ids': ids}
             ,"success": function(json) {
                    $.each(json.activities, function(id, value) {
                        results[id] = value;
                        results[id].begin = new Date(results[id].begin);
                        results[id].end = new Date(results[id].end);
                    });

                    if (all)
                        show(ids[0]);
             }
        });
    }
    else
    {
        if (all)
            show(ids[0]);
    }
}

function show(id)
{
    $("#activity_show .body .msg").html('');
    $("#activity_show .body .msg").hide();
    if (typeof results[id].origin != "undefined") {
        if (typeof results[id].origin.image != "undefined")
            $("#activity_show .head .origin").html(
                '<a href="groups/see/' + results[id].origin.name + '">' + 
                    '<img src="' + results[id].origin.image + '" title="' + results[id].origin.label + '">' +
                '</a>');
        else 
            $("#activity_show .head .origin").html(
                '<a href="groups/see/' + results[id].origin.name + '">' + 
                    '[' + results[id].origin.label + ']' +
                '</a>');
    }
    else {
        $("#activity_show .head .origin").html(
            '<a href="tol/see/' + results[id].writer.login + '">' +
                '<img src="' + results[id].writer.photo + '" title="' + results[id].writer.displayName + '">' +
            '</a>');
    }
    
    //$("#activity_show .head .target").html(results[id].target.label);
    $("#activity_show .head .title").html(results[id].title);

    if (!results[id].participate)
    {
        //var html = '<a onclick="present(' + id + ');">S\'inscrire</a>';
        //$("#activity_show .body .participate").html(html);
        $("#activity_show .body .present a").unbind('click');
        $("#activity_show .body .present a").click(function() {present(id);});
        $("#activity_show .body .present").show();
        $("#activity_show .body .out").hide();
    }
    else
    {
        //var html = '<a onclick="out(' + id + ');">Se désinscrire</a>';
        //$("#activity_show .body .participate").html(html);
        $("#activity_show .body .out a").unbind('click');
        $("#activity_show .body .out a").click(function() {out(id);});
        $("#activity_show .body .out").show();
        $("#activity_show .body .present").hide();
    }

    $("#activity_show .body .description").html(results[id].description);
    $("#activity_show .body .comment").html(results[id].comment);

    if (results[id].begin.toLocaleDateString() == results[id].end.toLocaleDateString()) {
        $("#activity_show .body .one_day").show();
        $("#activity_show .body .several_days").hide();
        $("#activity_show .body .one_day .date").html(results[id].begin.toLocaleDateString());
        $("#activity_show .body .one_day .time .hour_begin").html(results[id].begin.toLocaleTimeString());
        $("#activity_show .body .one_day .time .hour_end").html(results[id].end.toLocaleTimeString());
    }
    else {
        $("#activity_show .body .one_day").hide();
        $("#activity_show .body .several_days").show();
        $("#activity_show .body .several_days .begin .date").html(results[id].begin.toLocaleDateString());
        $("#activity_show .body .several_days .begin .hour").html(results[id].begin.toLocaleTimeString());
        $("#activity_show .body .several_days .end .date").html(results[id].end.toLocaleDateString());
        $("#activity_show .body .several_days .end .hour").html(results[id].end.toLocaleTimeString());
    }

    $("#activity_show .body .participants_list .number").html(count(results[id].participants));
    $("#activity_show .body .participants").html('');
    var out = [];
    $.each(results[id].participants, function(index, value) {
        out.push(value.displayName);
    });
    $("#activity_show .body .participants").html(out.join(', '));

    if (results[id].isWriter) {
        $("#activity_show .body .misc .mail a").attr('href', 'activity/participants/' + id);
        $("#activity_show .body .misc .mail").show();
        $("#activity_show .body .misc .participants_link").hide();
    }
    else {
        $("#activity_show .body .misc .participants_link a").attr('href', 'activity/participants/' + id);
        $("#activity_show .body .misc .participants_link").show();
        $("#activity_show .body .misc .mail").hide();
    }

    if (results[id].canEdit) {
        $("#activity_show .body .misc .admin a").attr('href', 'activity/modify/' + id);
        $("#activity_show .body .misc .admin").show();
    }
    else {
        $("#activity_show .body .admin").hide();
    }
    $("#activity_show").show();
}

function present(id) {
    request({ "url": 'activity/participants/add/' + id
         ,"success": function(json) {
                if (results[id].participants instanceof Array) {
                    results[id].participants = {};
                }
                results[id].participants[json.participant.id] = json.participant;
                results[id].participate = true;
                $("[aid='" + id + "']").switchClass('unstar', 'star', 100);
                show(id);
                $("#activity_show .msg").show();
                $("#activity_show .msg").html('Tu as été rajouté à l\'activité.');
        }});
}

function out(id) {
    request({ "url": 'activity/participants/del/' + id
         ,"success": function(json) {
                delete results[id].participants[json.participant.id];
                results[id].participate = false;
                $("[aid='" + id + "']").switchClass('star', 'unstar', 100);
                show(id);
                $("#activity_show .msg").show();
                $("#activity_show .msg").html('Tu as été enlevé à l\'activité.');
        }});
}

function switch_participate(id) {
    if ($("[aid='" + id + "']").hasClass('star'))
        out(id);
    else
        present(id);
}

var results = new Array();

function count(obj)
{
    var c = 0, key;
    for (key in obj) {
        c++;
    }
    return c;
}

function change_view_cal(url) {
    wd_op.url = 'activity/ajax/timetable/' + url;
    $("#gridcontainer").reload();
}
