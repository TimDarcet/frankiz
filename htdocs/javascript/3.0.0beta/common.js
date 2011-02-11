$(function(){
   $.getCSS = function(url, media){
      $(document.createElement('link') ).attr({
          href: url,
          media: media || 'screen',
          type: 'text/css',
          rel: 'stylesheet'
      }).appendTo('head');
   };

    $.ajaxSetup({ cache: false });

    $(".wiki_textarea .wiki_slider").click(function() {
        //TODO
    });

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
                    target_picker.find('.comments').html("Sera visible par tout le monde et les membres du groupe " +
                                                          selected.text() + " seront mis au courant");
                } else {
                    target_picker.find('.comments').html("Sera visible uniquement par les membres du groupe " + selected.text());
                }
            }
        }
    });
    $(".target_picker select, .target_picker input").change();

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
          type: 'POST',
          url: fields.url,
          dataType: 'json',
          data: 'token=' + xsrf_token + '&json=' + data,
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
        var busy = false;
        var newdata = false;

        function get() {
            busy = true;
            $.ajax({
                type: 'POST',
                 url: 'wiki_preview',
                data: 'text=' + $textarea.val(),
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
    };
    container.click(function() {
        if (!focus) {
            searcher.show();
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
                           var groups = json.groups;
                           var group;
                           for (var i in groups)
                           {
                               group = json.groups[i];
                               list.append('<li gid="' + group.id + '">' +
                                               '<img src="' + group.src + '" />' +
                                               group.label +
                                           '</li>');
                           }

                           list.children('li').click(function() {
                               var gid = $(this).attr('gid');
                               var alreadyExists = false;
                               selected.children('li').each(function() {
                                   if ($(this).attr('gid') == gid) {
                                       alreadyExists = true;
                                       return false;
                                   }
                               });
                               if (!alreadyExists)
                               {
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
    })
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
}