var groups = {
    "run" : function() {
        console.log("Groups MiniModule runed");
    }
};

$(function() {
    $("#minimodule_groups [rid] .room_master").hover(
        function () {
            $(this).addClass("hover");
        },
        function () {
            $(this).removeClass("hover");
        }
    );

    $('#minimodule_groups [rid] .room_master').click(function() {
        var $room = $(this).closest('[rid]');
        var rid = $room.attr('rid');
        var $group = $(this).closest('[gid]');
        var gid = $group.attr('gid');

        if ($room.hasClass('close')) {
            $('[gid=' + gid + '] [rid=' + rid  + ']').switchClass('close', 'open', 100);
            $('#section [gid=' + gid + '] [rid=' + rid  + '] .traffic_light_switcher').attr('title','Fermer le local');
            request('groups/ajax/open/' + gid + '/' + rid + '/1');
        } else {
            $('[gid=' + gid + '] [rid=' + rid  + ']').switchClass('open', 'close', 100);
            $('#section [gid=' + gid + '] [rid=' + rid  + '] .traffic_light_switcher').attr('title','Ouvrir le local');
            request('groups/ajax/open/' + gid + '/' + rid + '/0');
        }
        $(this).removeClass("hover");
     });
});
