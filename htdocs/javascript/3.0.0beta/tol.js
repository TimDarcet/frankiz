var searching = false;
var newsearch = false;
var mode = 'card';

$(function(){
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
            "url": 'tol/ajax/search'
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

                function imageur() {
                    $.each($("#tol_results > li .sheet .img a"), function(index, value) {
                        $(value).removeAttr('href');
                        $(value).click(function() {
                            var photo = $(this).closest('[photo]').attr('photo');
                            var original = $(this).closest('[original]').attr('original');
                            var modal = $('#content .modal');

                            var images = [];
                            if (photo)
                                images.push({'href' : photo});
                            if (original)
                                images.push({'href' : original});

                            $.fancybox(images, {
                                        'padding'       : 0,
                                        'transitionIn'  : 'none',
                                        'transitionOut' : 'none',
                                        'type'          : 'image',
                                        'changeFade'    : 0,
                                        'cyclic'        : true,
                                        'centerOnScroll': true,
                                        'titleShow'     : false
                                    });
                        });
                    });
                }

                if (mode == 'card') {
                    $.each($("#tol_results > li .card .img a"), function(index, value) {
                        $(value).removeAttr('href');
                        $(value).click(function() {
                            var li  = $(this).closest('li[uid]');
                            var uid = li.attr('uid');
                            request({
                                "url": 'tol/ajax/sheet/' + uid
                              ,"fail": false
                           ,"success": function(json) {
                                        mode = '';
                                        li.children('.sheet').html(json.sheet);
                                        li.removeClass('card');
                                        li.addClass('sheet', 'fast');
                                        imageur();
                                       }
                            });
                        });
                    });
                }

                if (mode != 'card') {
                    imageur();
                }

                searching = false;
                $("#tol_infos").removeClass("searching");
                if (newsearch)
                    search();
            }});
    }
}
