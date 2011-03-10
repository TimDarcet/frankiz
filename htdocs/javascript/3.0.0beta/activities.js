$(function() {
    $('.hide').hide();

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

    var $activity_show = $('#activity_show');
    initial_top = $activity_show.position().top + 175;
    $('#activities_list').delegate('.day .activity', 'hover', function() {
        var top = Math.max(0, $(this).position().top - initial_top);
        $("#activity_show").animate({top: top}, {queue: false});
        load([ $(this).attr('aid') ], true);
    });

    $("#activity_show .body .present a").live('click', function() { present($(this).closest("[aid]").attr("aid")); });
    $("#activity_show .body .out a").live('click', function() { out($(this).closest("[aid]").attr("aid")); });

    var temp = [];
    $('#activities_list .day .activity').each(function(){
        temp.push($(this).attr('aid'));
    });
    load(temp, false);
});

var initial_top;
var results = [];

// To load the list of activities on the page activity
function change_view(type)
{
    request({ "url": 'activity/ajax/get/'
            ,"data": {'visibility': type, 'list': true}
         ,"success": function(json) {
                $("#activities_list").html(json.activities);
         }
    });

    var temp = [];
    $('#activities_list .day .activity').each(function(){
        temp.push($(this).attr('aid'));
    });
    load(temp, false);
}

function load(ids, all)
{
    var lo = [];
    for (var i in ids) {
        var id = ids[i];
        if (!key_exists(id, results)) {
            lo.push(id);
        }
    }

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
    var participants = [];
    for (var i in results[id].participants) {
        participants.push(results[id].participants[i].displayName);
    };

    var assign = {};
    assign.logged = logged;

    assign.id     = id;
    assign.origin = results[id].origin;
    assign.title  = results[id].title;
    assign.writer = results[id].writer;
    assign.begin  = results[id].begin;
    assign.end    = results[id].end;
    assign.isWriter     = results[id].isWriter;
    assign.canEdit      = results[id].canEdit;
    assign.description  = results[id].description;
    assign.comment      = results[id].comment;
    assign.participate  = results[id].participate;
    assign.participants = participants;

    $("#activity_show").attr('aid', id);
    $("#activity_show").html($("#activity_template").tmpl(assign));

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

