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

    var plugins = ["themes", "json_data", "sort"];

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
        "sort" : function (a, b) { return parseInt($(a).attr('l')) > parseInt($(b).attr('l')) ? 1 : -1; },
        "plugins" : plugins
    });
}

function groups_picker(id, data, check)
{
    // Principal blocks
    var container  = $("#container_" + id);
    var tree       = $("#tree_" + id);
    var flat       = $("#flat_" + id);
    var input      = $("#" + id);

    var flat_empty = flat.html();

    // What blocks are to be hidden / shown
    input.hide();
    var handler = function() {
        tree.slideUp(100);
        flat.slideDown(100);
    };

    var plugins = [ "themes", "json_data", "types", "sort"];
    // If we are in the UI mode
    if (check != -1) {
        plugins.push("ui");
        tree.hide();
        container.click(function() {
            flat.slideUp(100);
            tree.slideDown(100);
            $.jstree._reference(tree).set_focus();
        });
        tree.mouseleave(function() {
            $("body").mousedown(handler);
        });
        tree.mouseenter(function() {
            $("body").unbind("mousedown", handler);
        });
    } else {
        flat.hide();
    }

    // We build the tree
    tree.jstree({
        "core" : {
            "animation" : 100
        },
        "themes" : {
            "theme" : "jstree"
        },
        "json_data" : {
            "data" : jsonGroupToJstreeData(data),
            "ajax" : {
                "url" : function(n) {
                        return platal_baseurl + 'groups/ajax/children/?json={"gid": ' + n.attr("gid") + '}';
                    },
                "success" : function (data) {
                        return jsonGroupToJstreeData(data.children);
                    }
            }
        },
        "types" : {
            "types" : {
                "default" : {
                    "select_node" : function(n) {
                        var ref = $.jstree._reference(n);
                        var children = ref._get_children(n);
                        var childSelected = false;
                        children.each(function() {
                            if (ref.is_selected($(this))) {
                                childSelected = true;
                                return;
                            }
                        });
                        return !childSelected;
                    }
                }
            }
        },
        "ui" : {
            "select_limit" : (check > 0) ? check : -1,
            "select_multiple_modifier" : "on",
            "disable_selecting_children" : true
        },
        "sort" : function (a, b) { return parseInt($(a).attr('l')) > parseInt($(b).attr('l')) ? 1 : -1; },
        "plugins" : plugins
    });

    // Catch the checked boxes to build a reminder (=flat) and the gids list (=input)
    var update = function() {
        flat.html("");
        var checkeds = tree.jstree("get_selected");
        var gids = new Array();
        checkeds.each(function(index) {
            gids.push($(this).attr("gid"));
            flat.append($('<li title="' + $(this).attr("name") + '">' + $(this).attr("label") + '</li>'));
        });
        input.val(gids.join(";"));
        input.keyup();
        if (flat.children("li").size() == 0) flat.html(flat_empty);
    };
    tree.bind("select_node.jstree", update);
    tree.bind("deselect_node.jstree", update);
}

function groups_modifier(container, data, rootGid)
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

        var chan   = "groups_";
        var client = new APE.Client();
        client.load();

        client.addEvent('load', function() {
            client.core.start({"name": new Date().getTime().toString()});
        });

        client.addEvent('ready', function() {
            var gids_array = [];
            for (var i in gids)
                gids_array.push(chan + gids[i]);
            gids_array = ["groups_43"];
            console.log(gids_array);
            client.core.join(gids_array);
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