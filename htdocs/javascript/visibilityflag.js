$(function() {
    $(".visiflag.click").click(function(e) {
        $(e.target).siblings().toggle();
    });

    $(".visiselect").change(function() {
        var name = $(this).attr('name');
        var value = $(this).val();
        request({
                "url": 'tol/ajax/visibility/' + name
              ,"data": {"visibility": value}
              ,"fail": false
           ,"success": function(json) {
                var colors = ["green", "blue", "orange", "red", "grey"];
                var flag = $("#visiflag-" + json.usergroupid + " > .visiflag");
                if (typeof(json.reason) != 'undefined') {
                    // Error => reason
                    // FIXME: find a better way to show internal errors
                    alert(json.reason);
                    flag.append($('<span class="error">' + json.reason + '</span>'));
                    return;
                }
                for (colid in colors)
                    flag.removeClass(colors[colid]);
                flag.addClass(json.color);
                flag.attr('title', json.title);
                flag.siblings().hide();
            }
        });
    });
});
