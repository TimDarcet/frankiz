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

    var trima = new RegExp('^[/]+', 'g');
    var trimb = new RegExp('[/]+$', 'g');
    $(window).hashchange(function(){
        var hash = location.hash;
        var fakeSharp = hash.indexOf("#!");
        var trueSharp = hash.indexOf("#", fakeSharp + 1);

        if (trueSharp == -1)
            var trueUrl = hash.slice(hash.indexOf("#!") + 2);
        else
            var trueUrl = hash.slice(hash.indexOf("#!") + 2, trueSharp);

        trueUrl = trueUrl.replace(trima, '').replace(trimb, '');

        if (currentPage != trueUrl)
            getSection(trueUrl);
    });

    if (location.hash.indexOf("#!") >= 0)
        $(window).hashchange();
});

function ajaxify(target)
{
    target.find("a[href]").each(function(index){
        var _this = $(this);
        var href  = $(this).attr("href");

        if (!_this.attr('nosolo') && href != undefined)
        {
            if (_this.children('img').length == 1 && jQuery.fn.fancyBox)
            {
                _this.fancybox({
                    'overlayOpacity' :  0.5,
                    'type'           : 'image'
                });
            }
            else
            {
                if (href.indexOf(platal_baseurl) == 0)
                    _this.attr("href", "#!/" + href.substr(platal_baseurl.length));
                else if (href.indexOf('http://') != 0)
                    _this.attr("href", "#!/" + href);
            }

            _this.attr("nosolo", true);
        }
    });
}

function getSection(page)
{
    $("body").addClass("loading");
    var delim = "?";
    if (page.indexOf("?") >=0)
        delim = "&";

    request({  "url"    : platal_baseurl + page + delim + "solo"
              ,"success" : function(json)
                  {
                    currentPage = page;
                    $("body").removeClass("loading");
                    $("#content").html(json.content);
                    $("#section .header").html(json.title);
                    for (i in json.pl_css)
                        $.getCSS("css/" + json.pl_css[i]);
                    for (i in json.pl_js)
                        $.getScript(json.pl_js[i]);
                    ajaxify($("#content"));
                    minimodules();
                }
              ,"fail"  : false
    });
}

function trueFormReset(target)
{
    $.each($(target).find("input[type=text]"), function(index, value){
        $(value).val("");
       });
}

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
