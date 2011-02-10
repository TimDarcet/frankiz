var searching = false;
var newsearch = false;

$(function(){
    $("#content form[name=filters]").submit(function(event) {
        search();
        event.stopPropagation();
        return false;
    });

    $('#content form[name=filters] input[auto]').keyup(function() {
        search();
    });

    search();

    $("input[name=comments]").change(function() {
        var $this = $(this);
        var gid = $this.siblings("input[name=gid]").val();
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

function force_search() {
    request({
        "url": 'groups/ajax/users/' + $('#content form[name=filters]').val()
      ,"data": $('#content form[name=filters]').formToJSON()
      ,"fail": false
   ,"success": function(json) {
            var ul = $("#section .members > ul");

            var users = json.users;

            for (var rights in users) {
                (function() {
                    var subul = ul.find("ul." + rights);

                    $.each(subul.children("li"), function(index, value) {
                        if (key_exists($(value).attr("uid"), users[rights])) {
                            delete users[rights][$(value).attr("uid")];
                        } else {
                            $(value).remove();
                        }
                    });

                    var html = new Array();
                    for (var i in users[rights]) {
                        var u = users[rights][i];

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
}

function search() {
    if (!searching) {
        searching = true;
        newsearch = false;
        force_search();
    } else {
        newsearch = true;
    }
}