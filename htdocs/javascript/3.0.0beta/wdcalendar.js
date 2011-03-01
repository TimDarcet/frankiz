var wd_op = {};

$(function() {
    var view = "week";

    $('#activity_show').hide();
    $('#activity_show .close_show').click(function() {
        $('#activity_show').hide();
    });
    $('#activity_show .msg').hide();

    var _MH = document.documentElement.clientHeight;
    var dvH = $("#calhead").height() + 2;

    wd_op = {
        view: view,
        theme: 3,
        height: Math.max(_MH - dvH - 220, 400),
        eventItems: [],
        ViewCmdhandler: View,
        onWeekOrMonthToDay: wtd,
        onBeforeRequestData: cal_beforerequest,
        onAfterRequestData: cal_afterrequest,
        onRequestDataError: cal_onerror,
        autoload: true,
        readonly: true,
        url: "activity/ajax/timetable"
    };

    if ( typeof( date_cal ) != "undefined" ) {
        wd_op.showday = date_cal;
    }

    if ( typeof( url_cal ) != "undefined" ) {
        wd_op.url = url_cal;
    }
    
    var $container = $("#gridcontainer");
    var p = $container.bcalendar(wd_op).BcalGetOp();

    datestrshow();
    $("#caltoolbar").noSelect();

    $("#hdtxtshow").datepicker({ picker: "#txtdatetimeshow",
                             showtarget: $("#txtdatetimeshow"),
                               onReturn: datestrshow});

    function cal_beforerequest(type) {
        var t="Loading data...";
        switch(type) {
            case 1:
                t="Loading data...";
                break;
            case 2:
            case 3:
            case 4:
                t="The request is being processed ...";
                break;
        }
        $("#errorpannel").hide();
        $("#loadingpannel").html(t).show();
    }

    function cal_afterrequest(type) {
        switch(type) {
            case 1:
                $("#loadingpannel").hide();
                break;
            case 2:
            case 3:
            case 4:
                $("#loadingpannel").html("Success!");
                window.setTimeout(function(){ $("#loadingpannel").hide();},2000);
            break;
        }
    }

    function cal_onerror(type,data) {
        $("#errorpannel").show();
    }

    function View(data) {
        $('#activity_show').show();
        load([data[0]], true);
    }

    function wtd(p) {
        changeView("day");
    }

    function changeView(view) {
        $("#caltoolbar div.fcurrent").removeClass("fcurrent");
        $("#show" + view + "btn").addClass("fcurrent");
        $container.swtichView(view);
        datestrshow();
    }

    function datestrshow() {
        if (p && p.datestrshow) {
            $("#txtdatetimeshow").text(p.datestrshow);
        }
    }

    //to show day view
    $("#showdaybtn").click(function() { changeView("day"); });

    //to show week view
    $("#showweekbtn").click(function() { changeView("week"); });

    //to show month view
    $("#showmonthbtn").click(function(e) { changeView("month"); });

    $("#showreflashbtn").click(function(e){
        $container.reload();
    });

    //go to today
    $("#showtodaybtn").click(function(e) {
        $container.gotoDate();
        datestrshow();
    });

    //previous date range
    $("#sfprevbtn").click(function(e) {
        $container.previousRange();
        datestrshow();
    });

    //next date range
    $("#sfnextbtn").click(function(e) {
        $container.nextRange();
        datestrshow();
    });
});

