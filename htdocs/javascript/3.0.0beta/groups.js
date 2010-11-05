function jsonGroupToJstreeData(raw, noajax)
{
    var children = [];
    for (var c in raw) {
        var child = {"data": {"title"  : raw[c].label}
                    ,"attr": { "gid"   : raw[c].id
                              ,"l"     : raw[c].L
                              ,"name"  : raw[c].name
                              ,"title" : raw[c].label
                              ,"label" : raw[c].label
                             }
                    };
        if (raw[c].children === true) {
            if (!noajax)
                child.state = "closed";
        } else if (raw[c].children) {
            child.state = "open";
            child.children = jsonGroupToJstreeData(raw[c].children, noajax);
        }
        children.push(child);
    }
    return children;
}

function groups_shower(id, data)
{
    // Principal blocks
    var container  = $("#container_" + id);
    var tree       = $("#tree_" + id);

    var plugins = ["themes", "json_data", "sort", "types"];

    // We build the tree
    tree.jstree({
        "core" : {
            "animation" : 100
        },
        "themes" : {
            "theme" : "jstree"
        },
        "json_data" : {
            "data" : jsonGroupToJstreeData(data, true)
        },
        "types" : {
            "types" : {
                "default" : {
                    "select_node" : function(n) {
                        console.log($(n));
                    }
                }
            }
        },
        "sort" : function (a, b) { return parseInt($(a).attr('l')) > parseInt($(b).attr('l')) ? 1 : -1; },
        "plugins" : plugins
    });
}

function groups_picker(id, ns, check)
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

    container.addClass('empty');
    var selected_empty = selected.html();

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

    var fillInput = function ()
    {
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
        input.keyup();
    };

    // Search logic
    var search = function ()
    {
        if (!searching)
        {
            newsearch = false;
            searching = true;
            container.addClass('searching');
            request({
                "url": 'groups/ajax/search'
              ,"data": {"ns": ns, "token" : filter.val()}
              ,"fail": false
           ,"success": function(json) {
                           list.empty();
                           var groups = json.groups;
                           groups.sort(function(a, b) {
                               return (a.frequency < b.frequency) ? -1 : (a.frequency > b.frequency) ? 1 : 0;
                            });
                           var group;
                           for (var i in groups)
                           {
                               group = json.groups[i];
                               list.append('<li gid="' + group.id + '">' + group.label + '</li>');
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

function groups_modifier(container, data)
{
    var tree = null;

    container.bind("loaded.jstree", function (event, data) {
        tree = $.jstree._reference(container);

        var gids = [];
        container.find('li').each(function() {
            gids.push($(this).attr('gid'));
             // Roots are static, you can't move nor delete them
            tree.set_type("static", $(this));
        });

        var chan   = "groups";
        var client = new APE.Client();
        client.load();

        client.addEvent('load', function() {
            client.core.start({"name": new Date().getTime().toString()});
        });

        client.addEvent('ready', function() {
//            var gids_array = [];
//            for (var i in gids)
//                gids_array.push(chan + gids[i]);
//            gids_array = ["groups_43"];
//            console.log(gids_array);
            console.log('ready');
            client.core.join(chan);
        });

        client.onRaw(' ', function(params) {
            console.log('registerGroups');
            console.log(params);
        });

        client.onRaw('groupInserted', function(params) {
            console.log('groupInserted');
            console.log(params);
            var groupAttributes = params.data.group.attr;
            var parentAttributes = params.data.parent.attr;

            tree.remove(tree._get_node( container.find('li:not([gid])')));
            container.jstree("create_node", $("li[gid=" + parentAttributes.gid + "]"), "last", params.data.group);
       });

        client.onRaw('groupRenamed', function(params) {
            console.log('groupRenamed');
            console.log(params);
            var groupAttributes = params.data.group.attr;
            var node = tree._get_node($('li[gid=' + groupAttributes.gid + ']'));
            tree.set_text(node, groupAttributes.label);
        });

        client.onRaw('groupDeleted', function(params) {
            console.log('groupDeleted');
            console.log(params);
            var groupAttributes = params.data.group.attr;
            var node = tree._get_node($('li[gid=' + groupAttributes.gid + ']'));
            tree.remove(node);
        });

        client.onRaw('children', function(params) {
            console.log('children');
            console.log(params);
            var parent   = params.data.parent;
            var children = params.data.children;

            var p = tree._get_node(container.find('li[gid=' + parent.gid + ']'));
            var d = tree._parse_json(children);

            if(p && d) {
                p.append(d).children(".jstree-loading").removeClass("jstree-loading");
                p.data("jstree-is-loading", false);
                tree.clean_node(p);
            }
        });
    });

    container.jstree({
        "core" : {
            "animation" : 100
        },
        "themes" : {
            "theme" : "jstree"
        },
        "json_data" : {
            "data" : data,
            "ajax" : {}
        },
        "types" : {
            "types" : {
                "default" : {
                    "select_node" : function(n) {
                        return true;
                    },
                    "move_node" : function(n) {
                        return true;
                    },
                    "create_node" : function(n) {
                        if (n.attr("gid") != undefined)
                            return true;

                        alert('Erreur');
                        return false;
                    }
                },
                "static" : {
                    "move_node" : function(n) {
                        return false;
                    },
                    "delete_node" : function(n) {
                        return false;
                    }
                }
            }
        },
        "dnd" : {
            "copy_modifier" : false,
            "check_timeout" : 30
        },
        "crrm" : {
            "move" : {
                "default_position" : "last"
            }
        },
        "contextmenu" : {
            "show_at_node" : false,
            "items" : {
                ccp: false,
                "remove" : {
                    "label"     : "Supprimer",
                    "action" : function (obj) { 
                                   request('groups/ajax/delete', {"group": obj.attr("gid")}, {"success":
                                       function(json) {
                                        // ok
                                   }});
                               }
                },
                "create" : {
                    "label"     : "Créer",
                    "action" : function (obj) { this.create(obj); }
                },
                "rename" : {
                    "label"     : "Renommer",
                    "action" : function (obj) { this.rename(obj); }
                }
            }
        },
        "sort" : function (a, b) { return parseInt($(a).attr('l')) > parseInt($(b).attr('l')) ? 1 : -1; },
        "plugins" : [ "themes", "json_data", "types", "crrm", "dnd", "ui", "contextmenu" ]
    });

    container.bind("move_node.jstree", function(e, data) {
        var group   = data.rslt.o.attr("gid");
        var parent  = data.rslt.np.attr("gid");
        var sibling = data.rslt.or.attr("gid");
        console.log('plip');
        if (sibling == undefined)
        {
//            request("groups/ajax/movelast", {"group": group, "parent": parent}, {"success":
//                function(json) {
//                    // ok
//                }});
        }
        else
        {
//            request("groups/ajax/movebefore", {"group": group, "parent": sibling}, {"success":
//                function(json) {
//                    // ok
//                }});
        }
    });

    container.bind("rename_node.jstree", function(e, data) {
        var label  = data.rslt.name;
        var group  = data.rslt.obj;

        if (group.attr("gid") == undefined) {
            // It's a new node
            var parent = data.inst._get_parent(group).attr("gid");
            request('groups/ajax/insert', {"label": label, "parent": parent}, {"success":
                function(json) {
                    console.log(json);
            }});
        } else {
            // Just renaming
            request('groups/ajax/rename', {"label": label, "group": group.attr("gid")});
        }
    });

    container.bind("before.jstree", function (e, data) {
        if(data.func === "load_node") {
            e.stopImmediatePropagation();

            var p = tree._get_node(container.find('li[gid=23]'));
            var d = tree._parse_json([{"data":{"title":"BDE SupOp"},"attr":{"gid":"72","l":"24","name":"4c8656066a10c","title":"4c8656066a10c","label":"BDE SupOp"}},{"data":{"title":"K\u00e8s"},"attr":{"gid":"20","l":"22","name":"kes","title":"kes","label":"K\u00e8s"}}]);

            if(p && d) {
                p.append(d).children(".jstree-loading").removeClass("jstree-loading");
                p.data("jstree-is-loading", false);
                tree.clean_node(p);
            }

            return false;
        }
    });
}