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
});

function force_search() {
    request({
        "url": 'groups/ajax/users/' + $('#content form[name=filters]').val()
      ,"data": $('#content form[name=filters]').formToJSON()
      ,"fail": false
   ,"success": function(json) {
            var ul = $("#content .users > ul");

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

                    console.log(users[rights]);

                    for (var i in users[rights]) {
                        (function() {
                            var u = users[rights][i];
                            var img = new Image();
                            $(img).hide();
                            img.src = u.src;
                            img.title = u.displayName;
                            img.uid = i;
                            $(img).load(function() {
                                subul.append($('<li uid="' + u.id + '" class="user">').append(this));
                                $(this).fadeIn();
                            });
                        })();
                    }
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