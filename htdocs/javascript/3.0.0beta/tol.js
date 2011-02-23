$(function(){
    $("#tol_searcher form").submit(function(event) {
        $('#tol_searcher input[name=mode]').val('sheet');
        users.search();
        event.stopPropagation();
        return false;
    });

    $("#tol_infos").removeClass("searching");
    $('#tol_searcher input[auto]').keyup(function(event) {
        if (event.keyCode != '13') {
            users.search();
        }
    });

    users.imageur();
});

var users = function() {
    var searching = false;
    var newsearch = false;
    var mode = 'card';

    function _imageur() {
        $.each($("#section .result .sheet .img a"), function(index, value) {
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

                return false;
            });
        });
    };

    function force_search() {
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
                    $("#tol_infos .page").empty();
                    $("#tol_infos .empty").hide();
                    $("#tol_infos .count").html(counter);
                    $("#tol_infos .total").html(json.total);
                    $("#tol_infos .notempty").show();
                    if (json.total > 20) {
                        var pages = '(Pages : ';
                        for(var i = 1; i <= Math.ceil(json.total / ((json.mode == 'card')?20:50)); i++) {
                            var onclick = "$('input[name=page]').val($(this).html()); users().search();";
                            var selected = ($('input[name=page]').val() == i) ? 'class="selected"' : '';
                            pages += ' <a ' + selected + ' onclick="' + onclick + '">' + i + '</a> ';
                        }
                        pages += ")";

                        $("#tol_infos .page").html(pages);
                    }
                    $('input[name="page"]').val(1);
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
                for (var uid in json.results) {
                    if ($("#tol_results > li[uid='" + uid + "']").length == 0) {
                        $('#tol_results').append(json.results[uid]);
                    }
                }

                mode = json.mode;
                $('#tol_searcher input[name=mode]').val('card');

                if (mode == 'card') {
                    $.each($("#tol_results > li .card .img a"), function(index, value) {
                        $(value).removeAttr('href');
                        $(value).closest('.card').click(function() {
                            var li  = $(this).closest('li[uid]');
                            var uid = li.attr('uid');
                            request({
                                "url": 'tol/ajax/sheet/' + uid
                              ,"fail": false
                           ,"success": function(json) {
                                        mode = '';
                                        li.children('.sheet').html(json.sheet);
                                        li.removeClass('card');
                                        li.addClass('sheet');
                                        _imageur();
                                        li.children('.sheet').find('a, li').click(function(event) {
                                            event.stopPropagation();
                                        });
                                        li.children('.sheet').click(function() {
                                            li.removeClass('sheet');
                                            li.addClass('card');
                                        });
                                       }
                            });
                        });
                    });
                }

                if (mode != 'card') {
                    _imageur();
                }

                searching = false;
                $("#tol_infos").removeClass("searching");
                if (newsearch)
                    _search();
            }});
    };

    function _search() {
        if (!searching) {
            searching = true;
            newsearch = false;
            force_search();
        } else {
            newsearch = true;
        }
    };

    return {
        search: function () {
            _search();
        },
        imageur: function () {
            _imageur();
        }
    };
}();
