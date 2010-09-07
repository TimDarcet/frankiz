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

    var plugins = [ "themes", "json_data", "types", "sort" ];
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
            "data" : data,
            "ajax" : {
                "url" : function(n) {
                        return platal_baseurl + 'groups/ajax/children/?json={"gid": ' + n.attr("gid") + '}';
                    },
                "success" : function (data) { 
                        return data.children;
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
        "plugins" : plugins
    });

    // Catch the checked boxes to build a reminder (=flat) and the gids list (=input)
    var update = function() {
    	console.log('plop');
		flat.html("");
		var checkeds = tree.jstree("get_selected");
		var gids = new Array();
		checkeds.each(function(index) {
			gids.push($(this).attr("gid"));
			flat.append($('<li title="' + $(this).attr("name") + '">' + $(this).attr("label") + '</li>'));
		});
		input.val(gids.join(";"));
		if (flat.children("li").size() == 0) flat.html(flat_empty);
    };
    tree.bind("select_node.jstree", update);
    tree.bind("deselect_node.jstree", update);
}

function groups_modifier(id, data)
{
	// Principal blocks
	var tree = $("#" + id);

    // We build the tree
    tree.jstree({
    	"core" : {
    		"animation" : 100
    	},
        "themes" : {
            "theme" : "jstree"
        },
        "json_data" : {
            "data" : data,
            "ajax" : {
                "url" : function(n) {
                        return platal_baseurl + 'groups/ajax/children/?json={"gid": ' + n.attr("gid") + '}';
                    },
                "success" : function (data) { 
                        return data.children;
                    }
            }
        },
        "types" : {
        	"types" : {
	        	"default" : {
	        		"select_node" : function(n) {
        				return true;
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
				"default_position" : "first"
			}
		},
		"contextmenu" : {
			"show_at_node" : false,
			"items" : {
				ccp: false,
	            "remove" : {
					"label"	 : "Supprimer",
					"action" : function (obj) { 
								   request('groups/ajax/remove', {"group": obj.attr("gid")}, {"success":
									   function(json) {
								    	// ok
								   }});
							       this.remove(obj); 
							   }
				},
				"create" : {
					"label"	 : "Créer",
					"action" : function (obj) { this.create(obj); }
				},
				"rename" : {
					"label"	 : "Renommer",
					"action" : function (obj) { this.rename(obj); }
				}
			}
		},
        "plugins" : [ "themes", "json_data", "types", "crrm", "dnd", "sort", "ui", "contextmenu" ]
    });

    tree.bind("move_node.jstree", function(e, data) {
    	var target = data.rslt.np.attr("gid");
    	var moved  = data.rslt.o.attr("gid");
	    request("groups/ajax/move", {"moved": moved, "target": target}, {"success":
	    	function(json) {
	    		// ok
	    	}});
    });

    tree.bind("rename_node.jstree", function(e, data) {
    	var label  = data.rslt.name;
    	var group  = data.rslt.obj;
    	
    	if (group.attr("gid") == undefined) {
    		// It's a new node
	    	var parent = data.inst._get_parent(group).attr("gid");
		    request('groups/ajax/create', {"label": label, "parent": parent}, {"success":
		    	function(json) {
		    		group.attr("gid", json.group.attr.gid);
		    		group.attr("name", json.group.attr.name);
		    		group.attr("title", json.group.attr.name);
		    }});
    	} else {
    		// Just renaming
		    request('groups/ajax/rename', {"label": label, "group": group.attr("gid")}, {"success":
		    	function(json) {
		    		// ok
		    }});
    	}
    });

    tree.bind("oncreate.jstree", function(e, data) {
    	console.log('plop');
    });
}