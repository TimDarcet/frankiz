$(function(){
    $('#section form[name=filters] input').keyup(function() {
        $("#section form[name=filters] input[name=page]").val(1);
        users.search();
    });

    $('#section form[name=filters] select').change(function() {
        $("#section form[name=filters] input[name=page]").val(1);
        users.search();
    });

    $("#section table.list").delegate("input[name=comment]", "change",
        function() {
            var $this = $(this);
            var gid = $("#gid").val();
            var uid = $this.closest("[uid]").attr("uid");
            request({"url" : "groups/ajax/comment"
                   ,"data" : {"gid": gid, "uid": uid, "comments": $this.val()}
                ,"success" : function(json) {
                                 if (json.uid) {
                                     $this.addClass("success");
                                 } else {
                                     $this.addClass("error");
                                 }
                             }
        });
    });
 
    $("#section table.list").delegate(".rights", "click",
            function() {
                var $this = $(this);
                var gid = $("#gid").val();
                var uid = $this.closest("[uid]").attr("uid");
                var rights = false;
                if ($this.hasClass("admin")) {
                    rights = "admin";
                } else if ($this.hasClass("member")) {
                    rights = "member";
                }   else if ($this.hasClass("friend")) {
                    rights = "friend";
                }
                var add = ($this.hasClass("off")) ? true : false;
                if (add) {
                    $this.switchClass("off", "on", 0);
                } else {
                    $this.switchClass("on", "off", 0);
                }
                request({"url" : "groups/ajax/admin/rights"
                       ,"data" : {"gid": gid, "uid": uid, "rights": rights, "add": add}
                    ,"success" : function(json) {
                                     if (json.msg) {
                                         alert(json.msg);
                                         if (add) {
                                             $this.switchClass("on", "off", 0);
                                         } else {
                                             $this.switchClass("off", "on", 0);
                                         }
                                     }
                                 }
                });
        });

    users.search();
});

var users = function() {
    var searching = false;
    var newsearch = false;

    function force_search() {
        request({
            "url": 'groups/ajax/admin/users/'
          ,"data": $('#section form[name=filters]').formToJSON()
          ,"fail": false
       ,"success": function(json) {
                var $table = $("#section .admin_users table.list");

                var users = json.users;
                var limit = json.limit;
                var total = json.total;

                if (users.length == 0) {
                    $table.empty();
                    $("#section .admin_users .pages").empty();
                }

                // Pages
                if (total > limit) {
                    var pages = '(Pages : ';
                    for(var i = 1; i <= Math.ceil(total / limit); i++) {
                        var onclick = "$('input[name=page]').val($(this).html()); users.search();";
                        var selected = ($('input[name=page]').val() == i) ? 'class="selected"' : '';
                        pages += ' <a ' + selected + ' onclick="' + onclick + '">' + i + '</a> ';
                    }
                    pages += ")";

                    $("#section .admin_users .pages").html(pages);
                }

                $.each($table.find("tr"), function(index, value) {
                    if (key_exists($(value).attr("uid"), users)) {
                        delete users[$(value).attr("uid")];
                    } else {
                        $(value).remove();
                    }
                });

                var html = new Array();
                for (var i in users) {
                    html.push(users[i]);
                }

                $table.append(html.join(''));

                searching = false;
                if (newsearch)
                    _search();
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
