var date;

var activity = {
    "run" : function() {
        date = new Date();
        date.setHours(11);
        this.load();
    },

    "present" : function(id) {
        request({ "url": 'activity/participants/add/' + id
             ,"success": function(json) {
             $(".msg_act").show();
             $(".msg_act").html('Tu as été rajouté à l\'activité.');
             setTimeout(function() {$(".msg_act").hide();}, 3000);
            }});
    },

    "forward_day" : function() {
        date.setDate(date.getDate()+1);
        this.load();
    },

    "forward_week" : function() {
        date.setDate(date.getDate()+7);
        this.load();
    },

    "backward_day" : function() {
        date.setDate(date.getDate()-1);
        this.load();
    },

    "backward_week" : function() {
        date.setDate(date.getDate()-7);
        this.load();
    },

    "load" : function() {
        request({ "url": 'activity/ajax/get'
                ,"data": {'date': date}
             ,"success": function(json) {
                    $('#minimodule_activities .activities').html('');
                    $.each(json.activities, function(index, value) {
                        $('#minimodule_activities .activities').append(value);
                    });
                var month = date.getMonth() + 1;
                    $('#minimodule_activities .act_date').html(date.getFullYear() + '-' + month + '-' + date.getDate());
            }});
    }
};
