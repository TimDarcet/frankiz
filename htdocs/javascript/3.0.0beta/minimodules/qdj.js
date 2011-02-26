var qdj_id = -1;
var reversedId = 0;

var qdj = {
    "run" : function() {
    },

    "vote" : function(votefor) {
        if (reversedId == 0)
        {
            request({ 'url': 'qdj/ajax/vote/'
                    ,'data': {"vote": votefor}
                 ,'success': qdj.load});
        }
    },

    "backward" : function() {
        reversedId++;
        this.load();
    },

    "forward" : function() {
        if (reversedId>0)
        {
            reversedId--;
            this.load();
        }
    },

    "load" : function() {
        request({ "url": 'qdj/ajax/get/'
                ,"data": {"daysShift": reversedId}
             ,"success": function(json)
                        {
                            $("#minimodule_qdj .question").html(json.qdj.question);
                            $("#minimodule_qdj .date").html(json.qdj.date);
                            $("#minimodule_qdj .answers .case1 a").html(json.qdj.answer1);
                            $("#minimodule_qdj .answers .case2 a").html(json.qdj.answer2);

                            $("#minimodule_qdj .case1").removeClass('jone');
                            $("#minimodule_qdj .case1").removeClass('rouje');
                            $("#minimodule_qdj .case2").removeClass('jone');
                            $("#minimodule_qdj .case2").removeClass('rouje');
                            if (Math.random() > 0.5) {
                                $("#minimodule_qdj .case1").addClass('jone');
                                $("#minimodule_qdj .case2").addClass('rouje');
                            } else {
                                $("#minimodule_qdj .case1").addClass('rouje');
                                $("#minimodule_qdj .case2").addClass('jone');
                            }

                            if (json.voted)
                            {
                                var count1 = parseInt(json.qdj.count1);
                                var count2 = parseInt(json.qdj.count2);
                                var total = count1 + count2;
                                $("#minimodule_qdj .counts").show();
                                $case1 = $("#minimodule_qdj .counts .case1 .count");
                                $case1.animate({height: count1/total * 100 + '%'}, 500);
                                $case1.html(count1);
                                $case2 = $("#minimodule_qdj .counts .case2 .count");
                                $case2.animate({height: count2/total * 100 + '%'}, 500);
                                $case2.html(count2);
                            } else {
                                $("#minimodule_qdj .counts").hide();
                            }
                        }
            });
    }
};
