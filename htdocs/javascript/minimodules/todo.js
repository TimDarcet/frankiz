var todo = {
    "run" : function() {
        todo.make();
    },

    "make" : function() {
        $('#todo_list div[todo_id]').each(function(index) {
            $(this).unbind();
            $(this).click(function() { todo.toggle($(this).attr('todo_id')); });
        });
    },

    "add" : function() {
        var tobedone = $('#todo_tobedone').val();
        $('#todo_tobedone').val('');

        if (tobedone && tobedone != '')
        {
            request({ "url": 'todo/ajax/add'
                    ,"data": {"tobedone": tobedone}
                 ,"success": function(json) {
                                $('#todo_list table').prepend('<tr><td><div class="checkbox" todo_id="'+json.todo_id+'" /></td><td>'+tobedone+'</td></tr>').children().children().first().hide().fadeIn('slow');
                                todo.make();
                             }
            });
        }
    },

    "toggle" : function(todo_id) {
        var checkbox = $('#todo_list div[todo_id='+todo_id+']').first();
        if (checkbox.attr('disabled') != 'disabled')
        {
            var target;
            if (checkbox.attr('checked') == 'checked')
            {
                checkbox.removeAttr('checked');
                checkbox.attr('disabled', 'disabled');
                target = "uncheck";
             } else {
                checkbox.attr('checked', 'checked');
                checkbox.attr('disabled', 'disabled');
                target = "check";
             }
            request({ "url": 'todo/ajax/' + target
                    ,"data": {"todo_id": todo_id}
                 ,"success": function (json) { checkbox.removeAttr('disabled'); } });
        }
    },

    "clear" : function() {
        request({ "url": 'todo/ajax/clear'
             ,"success": function (json){$('#todo_list div[checked="checked"]').parent().parent().fadeOut('slow', function() {this.parentNode.removeChild(this);}); } 
        });
    }
};