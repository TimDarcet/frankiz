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

                    for (var i in users[rights]) {
                        (function() {
                            var u = users[rights][i];
                            var img = new Image();
                            $(img).hide();
                            img.src = u.micro;
                            img.title = u.displayName;
                            img.uid = i;
                            $(img).load(function() {
                                var _this = $(this);
                                subul.append(
                                        $('<li uid="' + u.id + '" class="user">').append(this));
                                _this.fadeIn();
                                _this.mouseover(function() {
                                    var temp = $('<div><a href="tol?hruid=' + u.hruid +'"><img style="height:100%" src="' + u.micro + '" /></a></div>');
                                    $('body').append(temp);
                                    temp.attr('title', u.displayName);
                                    temp.css('margin', 0);
                                    temp.css('padding', 0);
                                    temp.css('height', $(this).height() + 5);
                                    temp.css('width', '100%');
                                    temp.css('position', 'absolute');
                                    temp.css('vertical-align', 'middle');
                                    temp.css({top: $(this).offset().top, left: $(this).offset().left});
                                    temp.animate({
                                        queue: true,
                                        height: 105,
                                        left: "+=-20",
                                        top: "+=-52"
                                      }, 500);
                                    temp.animate({queue: true}, 1000, function() {
                                        temp.animate({
                                            height: _this.height(),
                                            left: "+=+20",
                                            top: "+=+52"
                                          }, 500, function() {
                                              $(this).remove();
                                          });
                                    });
                                    var smallimg = new Image();
                                    $(smallimg).hide();
                                    smallimg.src = u.small;;
                                    $(smallimg).css('width', '100%');
                                    $(smallimg).load(function() {
                                        temp.find('img').attr('src', this.src);
                                    })
                                });
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