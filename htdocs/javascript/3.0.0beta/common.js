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

$(document).ready(function(){
    $.ajaxSetup({ cache: false });

    $(window).hashchange(function(){
        var hash = location.hash;
        var page = hash.slice(hash.indexOf("#!") + 2);
        getSection(page);
    });

    if (location.hash.indexOf("#!") >= 0)
        $(window).hashchange();

    ajaxify($("body"));
});

function ajaxify(target)
{
    target.find("a[href]").each(function(index){
        if (!$(this).attr('nosolo') && $(this).attr("href") != 'exit')
            $(this).attr("href", "#!/" + $(this).attr("href"));
            $(this).attr("nosolo", true);
    });

    // TODO ?
    target.find("form[action]").each(function(index){
        //if ($(this).attr('nosolo') !== undefined) {
        //    console.log($(this).attr('action').split('solo').join('nosolo'));
        //    $(this).attr('action', $(this).attr('action').split('solo').join('nosolo'));
        //}
    });
}

function getSection(page)
{
    request({  "url"    : platal_baseurl + page + "?solo"
              ,"success" : function(json)
                  {
                    $("#content").html(json.content);
                    $("#section .header").html(json.title);
                    for (i in json.pl_css)
                        $.getCSS("css/" + json.pl_css[i]);
                    for (i in json.pl_js)
                        $.getScript(json.pl_js[i]);
                    for (name in json.minimodules_js)
                        if (json.minimodules_js[name] != '')
                            includeAndRun(name, json.minimodules_js[name]);
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
        callback = function(json) { if (json.pl_errors || json.errors) { fields.fail(json); } else { fields.success(json); } };
    else if (fields.success && fields.fail === false)
        callback = function(json) { fields.success(json); };
    else if (fields.success)
        callback = function(json) { if (json.pl_errors || json.errors) { showError(json); } else { fields.success(json); } };
    else if (fields.fail)
        callback = function(json) { if (json.pl_errors || json.errors) { fields.fail(json); } };
    else
        callback = function(json) { if (json.pl_errors || json.errors) { showError(json); }};

    var data = (fields.data) ? fields.data : {};

    if (!fields.raw)
        data = JSON.stringify(data);

    $.ajax({
          url: fields.url,
          dataType: 'json',
          data: 'json=' + data,
          success: callback,
          error: function (xhr, textStatus, errorThrown) {
                     if(xhr.status == 403)
                     {
                         var json = $.parseJSON(xhr.responseText);
                         json.status = 403;
                         callback(json);
                     }
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
