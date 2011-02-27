$(document).ready(function() {
    $('.hide').hide();

    // for qdj/admin
    $("input[type='text']").focusout(function(){
        request({ "url": 'qdj/ajax/modify'
                ,"data": {'id': $(this).attr('id'), 'date': $(this).val()}
                ,"fail": function(json) {
                    $(".msg_qdj").html('La QDJ n\'a pas pu être déplacée.');
             }
             ,"success": function(json) {
                    if (json.success)
                        $(".msg_qdj").html('La QDJ a été déplacée avec succès.');
                    else
                        $(".msg_qdj").html('La date demandée n\'est pas valide.');
                    $(".msg_qdj").show();
                    setTimeout(function() {$(".msg_qdj").toggle(150);}, 3000);
             }
        });
    });

    // for qdj
    $('.graph').visualize({height: '82px', width: '400px', appendTitle:false, appendKey:false});
    $('.graph thead').hide();
    $('.graph tbody').hide();
    $('.graph caption').hide();
    $("#qdj_form input").hide();
    
	$("#qdj_form").change(function(){
        request({ "url": 'qdj/ajax/ranking'
                ,"data": $("#qdj_form").formToJSON()
             ,"success": function(json) {
                    $("#qdj_ranking").html(json.result);
                    $('.graph').visualize({height: '82px', width: '400px', appendTitle:false, appendKey:false});
                    $('.graph thead').hide();
                    $('.graph caption').hide();
             }
        });
        $("#news_show").show();
	});
});