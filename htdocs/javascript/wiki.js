$(function() {
    $("#versions > a").click(function() {
        var _this   = $(this);
        var wid     = $("#content input[name=wid]").val();
        var version = _this.attr('version');

        request({  "url" : 'wiki/ajax/get'
                , "data" : {"wid": wid, "versions": [version]}
              ,"success" : function(json) {
                    var v = json.wiki.versions;

                    for (var i in v) {
                        v = v[i];
                        break;
                    }

                    $("#versions > a").removeClass('on');
                    _this.addClass('on');

                    $("#oldwriter").html(v.writer.hruid);
                    $("#oldwrote").html(v.wrote);
                    $("#oldhtml").html(v.html);
                    $("#oldcontent textarea").val(v.content);
                 }
        });
    });
});