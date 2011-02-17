$(function(){
    $("#section form[name=filters]").submit(function(event) {
        users.search();
        event.stopPropagation();
        return false;
    });

    $('#section form[name=filters] input[auto]').keyup(function() {
        users.search();
    });

    $("#section input[name=comments]").change(function() {
        var $this = $(this);
        var gid = $("#gid").val();
        request({"url" : "groups/ajax/comment"
               ,"data" : {"gid": gid, "comments": $this.val()}
            ,"success" : function(json) {
                             if (json.uid) {
                                 $this.addClass("success");
                                 $("li[uid=" + json.uid + "] .comments").html($this.val());
                             } else {
                                 $this.addClass("error");
                             }
                         }
        });
    });
});

var users = function() {
    var searching = false;
    var newsearch = false;

    function force_search() {
        request({
            "url": 'groups/ajax/users/' + $('#content form[name=filters]').val()
          ,"data": $('#content form[name=filters]').formToJSON()
          ,"fail": false
       ,"success": function(json) {
                var ul = $("#section .members > ul");

                var users = json.users;
                var limit = json.limit;

                if (users.length == 0) {
                    ul.find("ul").empty();
                }

                for (var rights in users) {
                    var users_list = users[rights].users;
                    (function() {
                        var subul = ul.find("ul." + rights);

                        // Total & Plural
                        subul.siblings('.total').html(users[rights].total);
                        if (users[rights].total <= 1) {
                            subul.siblings('.plural').hide();
                        } else {
                            subul.siblings('.plural').show();
                        }

                        // Pages
                        if (users[rights].total > limit) {
                            var pages = '(Pages : ';
                            for(var i = 1; i <= Math.ceil(users[rights].total / limit); i++) {
                                var onclick = "$('input[name=" + rights + "_page]').val($(this).html()); users.search();";
                                var selected = ($('input[name=' + rights + '_page]').val() == i) ? 'class="selected"' : '';
                                pages += ' <a ' + selected + ' onclick="' + onclick + '">' + i + '</a> ';
                            }
                            pages += ")";

                            subul.siblings('.page').html(pages);
                        }

                        $.each(subul.children("li"), function(index, value) {
                            if (key_exists($(value).attr("uid"), users_list)) {
                                delete users_list[$(value).attr("uid")];
                            } else {
                                $(value).remove();
                            }
                        });

                        var html = new Array();
                        for (var i in users_list) {
                            var u = users_list[i];

                            var img = '<span title="' + u.displayName + '" class="img"><img style="display:none" onload="$(this).fadeIn()" src="' + u.micro + '" /></span>';
                            var comments = '<span class="comments">' + ((u.comments) ? u.comments : u.displayName) + '</span>';
                            html.push('<li uid="' + i + '"><a href="tol/?hruid=' + u.hruid + '">' + img + '</a>' + comments + '</li>');
                        }

                        subul.append(html.join(''));
                    })();
                }

                searching = false;
                if (newsearch)
                    search();
            }});
    };

    function _search() {
        newsearch = true;
        if (!searching) {
            searching = true;
            newsearch = false;
            force_search();
        }
    }

    return {
        search: function () {
            _search();
        }
    };
}();

var news = function() {
    var searching = false;
    var newsearch = false;

    function force_search() {
        request({
            "url": 'groups/ajax/news/' + $("#gid").val()
       ,"success": function(json) {
                var news = json.news;

                var html = [];
                for (var nid in news) {
                    html.push(news);
                }

                var ul = $("#section .news > table");
                ul.html(html.join(''));

                searching = false;
                if (newsearch)
                    search();
            }});
    };

    function _search() {
        newsearch = true;
        if (!searching) {
            searching = true;
            newsearch = false;
            force_search();
        }
    }

    return {
        search: function () {
            _search();
        }
    };
}();