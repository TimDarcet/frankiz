$(document).ready(function() {
    $('.hide').hide();
    if ($("#activity_show").html() == '\n')
        $("#activity_show").hide();
    
	$("input[name='admin_id']").change(function(){
        request({ "url": 'activity/ajax/admin'
                ,"data": {'id': $("input[@name='admin_id']:checked").val(), 'admin': true}
             ,"success": function(json) {
                    $("#activity_show").html(json.activity);
                    $("#activity_modify #activity_show").focusout(function(){
                        change('instance');
                    });
                    $("#activity_modify #activity_show").change(function(){
                        has_changed = true;
                    });
                    $('.hide').hide();
             }
        });
        $("#activity_show").show();
	});
    
	$("input[name='aid']").change(function(){
        request({ "url": 'activity/ajax/admin/'
                ,"data": {'id': $("input[@name='aid']:checked").val(), 'regular': true}
             ,"success": function(json) {
                    $("#activity_show").html(json.activity);
                    $("#activity_modify #activity_show").focusout(function(){
                        change('regular');
                    });
                    $("#activity_modify #activity_show").change(function(){
                        has_changed = true;
                    });
                    $('.hide').hide();
             }
        });
        $("#activity_show").show();
	});

	$("input[name='participants_id']").change(function(){
        request({ "url": 'activity/ajax/admin/'
                ,"data": {'id': $("input[@name='participants_id']:checked").val(), 'participants': true}
             ,"success": function(json) {
                    $("#activity_show").html(json.activity);
             }
        });
        $("#activity_show").show();
	});

    $('#activities_list .day .activity').hover(function(){
        load(new Array($(this).attr('aid')), true);
    })

    var temp = new Array()
    $('#activities_list .day .activity').each(function(){
        temp.push($(this).attr('aid'));
    })
    load(temp, false);
});

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
    $("#activity_show .msg").html('');
    $("#activity_show .msg").hide();
    $("#activity_show .first .target").html(results[id].target.label);
    $("#activity_show .first .title").html(results[id].title);

    if (!results[id].participate)
    {
        var html = '<a onclick="present(' + id + ');">S\'inscrire</a>';
        $("#activity_show .participate").html(html);
    }
    else
    {
        var html = '<a onclick="out(' + id + ');">Se désinscrire</a>';
        $("#activity_show .participate").html(html);
    }

    $("#activity_show .description").html(results[id].description);
    $("#activity_show .comment").html(results[id].comment);
    $("#activity_show .date").html(results[id].begin.toLocaleDateString() + ' :');
    $("#activity_show .time .hour_begin").html(results[id].begin.toLocaleTimeString());
    $("#activity_show .time .hour_end").html(results[id].end.toLocaleTimeString());

    $("#activity_show .participants_list .number").html(results[id].participants.length);
    $("#activity_show .participants").html('');
    $.each(results[id].participants, function(index, value) {
        $("#activity_show .participants").append('<div class="participant">' + value.displayName + '</div>');
    });
    $("#activity_show").show();
}

function present(id) {
    request({ "url": 'activity/participants/add/' + id
         ,"success": function(json) {
                results[id].participants[json.participant.id] = json.participant;
                results[id].participate = true;
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
                show(id);
                $("#activity_show .msg").show();
                $("#activity_show .msg").html('Tu as été enlevé à l\'activité.');
        }});
}

var results = new Array();

function change(type)
{
    if (has_changed)
    {
        has_changed = false;
        request({ "url": 'activity/ajax/modify/' + type
                ,"data": $('#activity_modify').formToJSON()
             ,"failure":function(json) {
                        $(".msg_proposal").html('La requete n\'a pas pu être envoyée.');
                        $(".msg_proposal").show();
                        setTimeout(function() {$(".msg_proposal").hide();}, 3000);
             }
             ,"success": function(json) {
                        if (json.success)
                            $(".msg_proposal").html('L\'activité a été modifiée avec succès. <br/> ' +
                                    '<span class="small">Le choix de sélection ne se modifie pas, ' +
                                    'rechargez la page pour qu\'il soit actualisé.</span>');
                        else
                            $(".msg_proposal").html('Les dates demandées ne sont pas valide.');
                        $(".msg_proposal").toggle(150);
                        setTimeout(function() {$(".msg_proposal").toggle(150);}, 3000);
             }
        });
    }
}

var has_changed = false;