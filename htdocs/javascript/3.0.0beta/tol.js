var searching = false;
var newsearch = false;
var mode = 'card';

$(document).ready(function(){
    $("#tol_searcher form").submit(function() {
        alert('plop');
        $('#tol_searcher input[name=mode]').val('sheet');
        return false;
    });

    $("#tol_searcher input[type=submit]").click(function() {
        $('#tol_searcher input[name=mode]').val('sheet');
        $(this).keyup();
        return false;
    });

    $("#tol_infos").removeClass("searching");
    $('#tol_searcher input[auto]').keyup(function() {
        search();
    });
});

function search()
{
    if (!searching)
    {
        searching = true;
        newsearch = false;
        $("#tol_infos").addClass("searching");

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
                $('#tol_searcher input[name=mode]').val('card');

                searching = false;
                $("#tol_infos").removeClass("searching");
                if (newsearch)
                    search();
            }});
    }
}
