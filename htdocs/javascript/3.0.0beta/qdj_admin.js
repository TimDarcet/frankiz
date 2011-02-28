$(function() {
    var $msg = $("#section .msg_qdj");

    var modify = function(id, date) {
        request({ "url": 'qdj/ajax/modify'
                ,"data": {'id': id, 'date': date}
                ,"fail": false
             ,"success": function(json) {
                    if (json.success) {
                        $msg.html('La QDJ a été déplacée avec succès.');
                    } else {
                        $msg.html("La QDJ n'a pas pu être déplacée.");
                    }
                    $msg.show();
                    setTimeout(function() {$msg.fadeOut(150);}, 1000);
                }
        });
    };

    $("#section input.date").change(function() {
        $qdj = $(this).closest("[qdj_id]");
        modify($qdj.attr("qdj_id"), $(this).val());
    });

    $("#section input[name=unplan]").click(function() {
        $qdj = $(this).closest("[qdj_id]");
        $qdj.find("input.date").val('');
        modify($qdj.attr("qdj_id"), false);
    });

    $("#section input[name=delete]").click(function() {
        $qdj = $(this).closest("[qdj_id]");
        request({ "url": 'qdj/ajax/modify'
            ,"data": {'id': $qdj.attr("qdj_id"), 'delete': true}
            ,"fail": false
         ,"success": function(json) {
                if (json.success) {
                    $msg.html('La QDJ a été supprimée avec succès.');
                    $qdj.remove();
                } else {
                    $msg.html("La QDJ n'a pas pu être supprimée.");
                }
                $msg.show();
                setTimeout(function() {$msg.toggle(150);}, 3000);
            }
        });
    });

    $(function() {
        $("#section input.date").datepicker({dateFormat: 'yy-mm-dd'});
    });
});