var timeleft = {
    "update" : function(target, future) {
        var timeoutRun;
        run = function() {
            var now = new Date();
            nowTime = now.getTime();
            futureTime = future.getTime();
            delta = future.getTime() - now.getTime();

            if (delta > 0) {
                var msecs = delta % 1000;
                if (msecs < 100) msecs = '0' + msecs;
                if (msecs < 10) msecs = '0' + msecs;
                delta = (delta - msecs) / 1000;

                var secs = delta % 60;
                if (secs < 10) secs = '0' + secs;
                delta = (delta - secs) / 60;

                var mins = delta % 60;
                if (mins < 10) mins = '0' + mins;
                delta = (delta - mins) / 60;

                var hours = delta % 24;

                var days = (delta - hours) / 24;

                target.html("J - " + days + "<br />" + hours + 'h ' + mins + 'min ' + secs + 's');
            } else {
                target.html("0");
                clearInterval(timeoutRun);
            }
        };
        run();
        timeoutRun = setInterval(run, 1000);
    }
};
