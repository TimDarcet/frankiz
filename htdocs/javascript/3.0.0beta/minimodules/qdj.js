var qdj_id = -1;
var reversedId = 0;

$(document).ready(function(){
    loadQDJ();
});

function vote(answer)
{
    if (reversedId == 0)
    {
        $.getJSON('qdj/ajax/vote/'+answer, function(json){
            if (json.already_voted)
            {
                showError(json.message);
            } else {
                loadQDJ();
            }
        });
    }
}

function backward()
{
    reversedId++;
    loadQDJ();
}

function forward()
{
    if (reversedId>0)
    {
        reversedId--;
        loadQDJ();
    }
}

function loadQDJ()
{
    $.getJSON('qdj/ajax/get/'+reversedId, function(json){
        qdj_id = json.qdj.qdj_id;
        $("#minimodule_qdj .question").html(json.qdj.question);
        $("#minimodule_qdj .date").html(json.qdj.date);
        $("#minimodule_qdj .answers .case1 a").html(json.qdj.answer1);
        $("#minimodule_qdj .answers .case2 a").html(json.qdj.answer2);
        
        $("#minimodule_qdj .case1").removeClass('jone');
        $("#minimodule_qdj .case1").removeClass('rouje');
        $("#minimodule_qdj .case2").removeClass('jone');
        $("#minimodule_qdj .case2").removeClass('rouje');
        if (qdj_id%2 == 0)
        {
            $("#minimodule_qdj .case1").addClass('jone');
            $("#minimodule_qdj .case2").addClass('rouje');
        } else {
            $("#minimodule_qdj .case1").addClass('rouje');
            $("#minimodule_qdj .case2").addClass('jone');      
        }

        if (json.voted)
        {
            var count1 = parseInt(json.qdj.count1);
            var count2 = parseInt(json.qdj.count2);
            var total = count1 + count2;
            $("#minimodule_qdj .counts").show();
            $("#minimodule_qdj .counts .case1 .count").height(count1/total*200);
            $("#minimodule_qdj .counts .case1 .count").html(count1);
            $("#minimodule_qdj .counts .case1 .count").hide();
            $("#minimodule_qdj .counts .case1 .count").slideDown('slow');
            $("#minimodule_qdj .counts .case2 .count").height(count2/total*200);
            $("#minimodule_qdj .counts .case2 .count").html(count2);
            $("#minimodule_qdj .counts .case2 .count").hide();
            $("#minimodule_qdj .counts .case2 .count").slideDown('slow');
        } else {
            $("#minimodule_qdj .counts").hide();
        }
    });
}