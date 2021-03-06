$(function(){
    /*
     * Plugin to dynamically load a .css file
     */
    $.getCSS = function(url, media){
       $(document.createElement('link') ).attr({
           href: url,
           media: media || 'screen',
           type: 'text/css',
           rel: 'stylesheet'
       }).appendTo('head');
    };

    /*
     * Plugin to get the attributes of a set of elements
     */
    $.fn.batchAttr = function(attr) {
        var attrs = [];

        this.each(function() {
            attrs.push($(this).attr(attr));
        });

        return attrs;
      };

    /*
     * Temporary ?
     */
    $.ajaxSetup({ cache: false });

    /*
     * Display full-size images in a "FancyBox"
     */
    $('#section').delegate("a[fancy]", "click", function() {
        $.fancybox([{'href' : $(this).attr('href')}], {
            'padding'       : 0,
            'transitionIn'  : 'none',
            'transitionOut' : 'none',
            'type'          : 'image',
            'changeFade'    : 0,
            'centerOnScroll': true,
            'titleShow'     : false
        });
        return false;
    });

    /*
     * Moving the background when scrolling (only in Chrome)
    var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
    if (is_chrome) {
        $('body').css('background-position', '50% 45%');
        $(document).scroll(function() {
            $('body').css('background-position', '50% ' + (45 + Math.round($(this).scrollTop() / ($(this).height() - $(window).height()) * 10)) +'%');
        });
    }  */

    /*
     * Manage the wiki_textarea components
     */
    $(".wiki_textarea").each(function() {
        var $this = $(this);
        wiki_preview.start($this.find("textarea"), $this.find(".wiki_preview"));
    });
    $(".wiki_textarea .down").click(function() {
        var $textarea = $(this).closest(".wiki_textarea").find(".textarea");
        console.log($textarea);
        $textarea.height($textarea.height() + 25);
    });
    $(".wiki_textarea .help").click(function() {
        // Using a Pop-Up, but their might be a better solution
        window.open("wiki_help/notitle", "Syntaxe Wiki",
        "scrollbars=1, status=0, height=500, width = 600, dependent=1, resizable=1");
    });

    /*
     * Manage the target_picker components
     */
    $(".target_picker select, .target_picker input[type=checkbox]").change(function() {
        var target_picker = $(this).closest('.target_picker');
        var selected = target_picker.find("option:selected");
        var checkbox = target_picker.find("input[type=checkbox]");
        checkbox.closest('label').hide();
        if (selected.closest('optgroup').attr("name") == "fkz") {
            target_picker.find('.comments').html(selected.attr("description") + "<br />" +
                                                  "Nécessite une validation de la part des Webmestres");
        } else {
            if (selected.attr('own_group')) {
                target_picker.find('.comments').html(selected.attr("description"));
            } else {
                checkbox.closest('label').show();
                if (checkbox.attr("checked")) {
                    target_picker.find('.comments').html("Sera visible par tout le monde et les membres & sympathisants du groupe " +
                                                          selected.text() + " seront mis au courant");
                } else {
                    target_picker.find('.comments').html("Sera visible uniquement par les membres du groupe " + selected.text());
                }
            }
        }
    });
    $(".target_picker select, .target_picker input").change();

    /*
     * Manage the helper components
     */
    $(".helper").click(function() {
        var $this = $(this);

        if ($this.closest(".head").siblings(".help").length > 0) {
            $this.closest(".head").siblings(".help").slideUp(function() { $(this).remove(); });
        } else {
            $this.addClass("loading");
            var target = $this.attr("target");
            var $help = $('<div class="help"></div>');
            $this.closest(".head").after($help);
            $.ajax({
                 url: 'wiki/ajax/see/' + target,
             success: function(data) {
                        $help.html(data);
                        $help.slideDown();
                        $this.removeClass("loading");
                    },
            dataType: 'text'
            });
        }
    });
});

function showError(json) {
    console.log(json);
};

function request(fields)
{
    if (!fields.url) {
        fields = {"url": fields};
    }

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

    return $.ajax({
          type: 'POST',
          url: fields.url,
          dataType: 'json',
          data: {token: xsrf_token, json: data},
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

function in_array(needle, haystack) {
    for (key in haystack) {
        if (haystack[key] == needle) {
            return true;
        }
    }
    return false;
}

var wiki_preview = {
    "start" : function($textarea, $display) {
        var busy = false;
        var newdata = false;

        function get() {
            busy = true;
            $.ajax({
                type: 'POST',
                 url: 'wiki_preview',
                data: { text: $textarea.val() },
             success: function(data) {
                          busy = false;
                          $display.html(data);
                          if (newdata) {
                              newdata = false;
                              get();
                          }
                      },
            dataType: 'text'
            });
        }

        $textarea.keyup(function() {
            if (busy) {
                newdata = true;
            } else {
                get();
            }
        });
    },

    "stop" : function($textarea) {
        $textarea.unbind('keyup');
    },

    "help" : function($help_zone) {
        if ($.trim($help_zone.html())) {
            $help_zone.toggle();
        } else {
            $help_zone.show();
            $.ajax({
                 url: 'embedded/raw/wiki_help',
             success: function(data) {
                          $help_zone.html(data);
                      },
            dataType: 'text'
            });
        }
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

function groups_picker(id, ns, check, order, desc)
{
    // Principal blocks
    var container  = $("#groups_picker_" + id);
    var selected   = container.children(".selected").first();
    var list       = container.children(".list").first();
    var searcher   = container.children(".searcher").first();
    var filter     = searcher.children("[name=filter]").first();
    var input      = $("#" + id);

    container.addClass('collapsed');

    var searching = false;
    var newsearch = false;
    var focus     = false;

    // What blocks are to be hidden / shown
    input.hide();
    searcher.hide();
    list.hide();
    var handler = function() {
        list.slideUp(100);
        searcher.slideUp(100);
        focus = false;
        container.switchClass('deployed', 'collapsed', 0);
    };
    container.click(function() {
        if (!focus) {
            searcher.show();
            container.switchClass('collapsed', 'deployed', 0);
            filter.val('');
            filter.focus();
            focus = true;
            search();
        }
    });
    container.mouseleave(function() {
      $(document).mouseup(handler);
    });
    container.mouseenter(function() {
      $(document).unbind("mouseup", handler);
    });

    var fillInput = function (sendEvent)
    {
        sendEvent = typeof(sendEvent) != 'undefined' ? sendEvent : true;

        var selecteds = selected.children('li');
        if (selecteds.length > 0)
            container.removeClass('empty');
        else
            container.addClass('empty');

        var gids = new Array();
        selected.children('li').each(function() {
            if ($(this).attr('gid'))
                gids.push($(this).attr('gid'));
        });

        input.val(gids.join(';'));

        if (sendEvent) {
            input.keyup();
        }
    };

    selected.children('li').click(function() {
        if (focus) {
            $(this).remove();
            fillInput();
        }
    });

    fillInput(false);

    // Search logic
    var search = function ()
    {
        if (!searching) {
            newsearch = false;
            searching = true;
            container.addClass('searching');
            request({
                "url": 'groups/ajax/search'
              ,"data": {"ns": ns, "piece": filter.val(), "order": order, "desc": desc}
              ,"fail": false
           ,"success": function(json) {
                           list.empty();

                           var html = [];
                           for (var i in json.groups) {
                               var group = json.groups[i];
                               html.push('<li gid="' + group.id + '">' +
                                               '<img src="' + group.src + '" />' +
                                               group.label +
                                           '</li>');
                           }
                           list.append(html.join(''));

                           list.children('li').click(function() {
                               var gid = $(this).attr('gid');
                               var alreadyExists = false;
                               selected.children('li').each(function() {
                                   if ($(this).attr('gid') == gid) {
                                       alreadyExists = true;
                                       return false;
                                   }
                               });
                               if (!alreadyExists) {
                                   var sel = $('<li gid="' + $(this).attr('gid') + '">' + $(this).html() + '</li>');
                                   sel.appendTo(selected);
                                   sel.click(function() {
                                       if (focus) {
                                           $(this).remove();
                                           fillInput();
                                       }
                                   });
                                   fillInput();
                               }
                           });

                           list.slideDown(100);

                           searching = false;
                           container.removeClass('searching');
                           if (newsearch)
                               search();
                       }
            });
        }
    };

    filter.keyup(function() {
        newsearch = true;
        search();
    });
}

function uploader($uploader, id) {
    $uploader.children('iframe').load(function() {
        var iid = $(this).contents().find('input[name=iid]').val();
        if (iid) {
            $uploader.append($('<input type="hidden" name="' + id + '" value="' + iid + '" />'));
        } else {
            $uploader.children('input').remove();
        }
    });
}

Nix = {
    map: null,
    convert: function(a) {
        Nix.init();
        var s = '';
        for (i = 0; i < a.length ; i++) {
            var b = a.charAt(i);
            s += ((b >= 'A' && b <= 'Z') || (b >= 'a' && b <= 'z') ? Nix.map[b] : b);
        }
        return s;
    },
    init: function() {
              if (Nix.map != null)
                  return;
              var map = new Array();
              var s='abcdefghijklmnopqrstuvwxyz';
              for (i = 0; i < s.length; i++)
                  map[s.charAt(i)] = s.charAt((i+13)%26);
              for (i=0; i<s.length; i++)map[s.charAt(i).toUpperCase()] = s.charAt((i+13)%26).toUpperCase();
              Nix.map = map;
          },
    decode: function(a) {
                document.write(Nix.convert(a));
            }
};
