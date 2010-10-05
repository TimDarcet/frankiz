var tol_busy = false;
var tol_late = false;
var mode = 'micro';

$(document).ready(function(){
    $("#tol_searcher").submit(function() {
        $('#tol_searcher input[name=mode]').val('fiche');
        return false;
    });

    $("#tol_searcher input[type=submit]").click(function() {
        $('#tol_searcher input[name=mode]').val('fiche');
        $(this).keyup();
        return false;
    });

    $("#tol_infos").removeClass("loading");
    $('#tol_searcher input').keyup(function() {
        search();
    });
});

function search()
{
    if (tol_busy) {
        tol_late = true;
    } else {
        $("#tol_infos").addClass("loading");
        tol_busy = true;

        request({
            "url": 'tol/ajax'
          ,"data": $('#tol_searcher form').formToJSON()
          ,"fail": false
       ,"success": function(json) {
                // Show informations about the request (number of results, ...)
                var counter = 0;
                for (var result in json.results)
                      counter++;

                if (counter > 0) {
                    $("#tol_infos .empty").hide();
                    $("#tol_infos .count").html(counter);
                    $("#tol_infos .total").html(json.total);
                    $("#tol_infos .notempty").show();
                } else {
                    $("#tol_infos .notempty").hide();
                    $("#tol_infos .empty").show();
                }

                // On retire les résultats ayant disparus
                if (json.mode == mode)
                    $.each($("#tol_results > li"), function(index, value) {
                        if (!key_exists($(value).attr("uid"), json.results))
                            $(value).remove();
                    });
                else
                    $("#tol_results").html('');

                // On ajoute les éventuels nouveaux résultats
                for (var uid in json.results)
                {
                    if ($("#tol_results > li[uid='" + uid + "']").length == 0)
                        $('#tol_results').append(json.results[uid]);
                }

                mode = json.mode;
                tol_busy = false;
                $('#tol_searcher input[name=mode]').val('micro');
                if (tol_late) {
                    tol_late = false;
                    search();
                } else {
                    $("#tol_infos").removeClass("loading");
                }
            }});
    }
}
