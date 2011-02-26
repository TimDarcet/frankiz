$(function(){
    $("#tol_infos").removeClass("searching");

    $("#tol_searcher form").submit(function(event) {
        $('input[name=page]').val(1);
        $('#tol_searcher input[name=mode]').val('sheet');
        users.search();
        event.stopPropagation();
        return false;
    });

    $('#tol_searcher input[auto]').keyup(function(event) {
        $('input[name=page]').val(1);
        if (event.keyCode != '13') {
            users.search();
        }
    });

    $("body").click(function(e) {
        if ($(e.target).attr('id') == 'tol_results' || $(e.target).attr('id') == 'content') {
            if (users.mode() == 'card') {
                var $lis = $("#tol_results li.result");
                $lis.removeClass('sheet');
                $lis.addClass('card');
            }
        }
    });

    $("#section").delegate("div.sheet a img", "click", function() {
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

    $("#tol_results").delegate("div.card", "click", function() {
        var $this = $(this);
        var li  = $this.closest('li[uid]');
        if (li.children('.sheet').children().length > 0) {
            li.removeClass('card');
            li.addClass('sheet');
        } else {
            var uid = li.attr('uid');
            $this.addClass('busy');
            request({
                "url": 'tol/ajax/sheet/' + uid
              ,"fail": false
           ,"success": function(json) {
                            mode = '';
                            li.children('.sheet').html(json.sheet);
                            li.removeClass('card');
                            li.addClass('sheet');
                            $this.removeClass('busy');
                       }
            });
        }
        return false;
    });

});

var users = function() {
    var searching = false;
    var newsearch = false;
    var mode = 'card';

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
                            var onclick = "$('input[name=page]').val($(this).html()); users.search();";
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

                var results = json.results;

                // On retire les résultats ayant disparus
                if (json.mode == mode) {
                    $.each($("#tol_results > li"), function(index, value) {
                        if (key_exists($(value).attr("uid"), results)) {
                            delete results[$(value).attr("uid")];
                        } else {
                            $(value).remove();
                        }
                    });
                } else {
                    $("#tol_results").html('');
                }

                // On ajoute les éventuels nouveaux résultats
                var html = [];
                for (var uid in json.results) {
                    html.push(json.results[uid]);
                }
                $('#tol_results').append(html.join(""));

                mode = json.mode;
                $('#tol_searcher input[name=mode]').val('card');

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
        mode: function() {
            return mode;
        }
    };
}();
