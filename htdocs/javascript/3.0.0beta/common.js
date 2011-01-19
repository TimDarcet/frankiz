(function($){
   $.getCSS = function(url, media){
      $(document.createElement('link') ).attr({
          href: url,
          media: media || 'screen',
          type: 'text/css',
          rel: 'stylesheet'
      }).appendTo('head');
   };
})(jQuery);

var currentPage = '';
$(document).ready(function(){
    $.ajaxSetup({ cache: false });
});

function showError(json) {
    console.log(json);
};

function request(fields)
{
    if (fields.success && fields.fail)
        callback = function(json) { if (json.pl_errors || json.errors || json.status) { fields.fail(json); } else { fields.success(json); } };
    else if (fields.success && fields.fail === false)
        callback = function(json) { fields.success(json); };
    else if (fields.success)
        callback = function(json) { if (json.pl_errors || json.errors || json.status) { showError(json); } else { fields.success(json); } };
    else if (fields.fail)
        callback = function(json) { if (json.pl_errors || json.errors || json.status) { fields.fail(json); } };
    else
        callback = function(json) { if (json.pl_errors || json.errors || json.status) { showError(json); }};

    var data = (fields.data) ? fields.data : {};

    if (!fields.raw)
        data = JSON.stringify(data);

    $.ajax({
          url: fields.url,
          dataType: 'json',
          data: 'json=' + data,
          success: callback,
          error: function (xhr, textStatus, errorThrown) {
                         var json = $.parseJSON(xhr.responseText);
                         json.status = xhr.status;
                         callback(json);
                 }
    });
}

$.fn.formToJSON = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function key_exists(key, search) {
    return key in search;
}

var wiki_preview = {
    "start" : function($textarea, $display) {
        $textarea.keyup(function() {
            $.ajax({
                    type: 'POST',
                     url: 'wiki_preview',
                    data: 'text=' + $textarea.val(),
                 success: function(data) { $display.html(data); },
                dataType: 'text'
              });
        });
    },

    "stop" : function($textarea) {
        $textarea.unbind('keyup');
    }
};

function wikify(container)
{
    var wid      = container.attr('wid');
    var display  = container.children("div").first();
    var textarea = container.children("textarea").first();

    var handler = function() {
        request({  "url"    : 'wiki/ajax/update'
                 , "data"   : {"wid": wid, "content": textarea.val()}
                 ,"success" : function(json) {
                     display.html(json.html);
                     wiki_preview.stop(textarea);
                     textarea.slideUp(100);
                     $(document).unbind("dblclick", handler);
                  }
        });
    };

    display.dblclick(function() {
        textarea.slideDown(100);
        container.mouseleave(function() {
            $(document).dblclick(handler);
        });
        container.mouseenter(function() {
            $(document).unbind("dblclick", handler);
        });
        wiki_preview.start(textarea, display);
    });
}
