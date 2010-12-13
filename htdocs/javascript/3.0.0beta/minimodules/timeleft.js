var timeleft = {
    "update" : function(target, future) {
        run = function() {
            var now = new Date();
            nowTime = now.getTime();
            futureTime = future.getTime();
            delta = Math.floor((futureTime - nowTime) / 1000);
            if (delta > 0) {
                target.html(delta + "s");
            } else {
                target.html("0");
                clearInterval(timeoutRun);
            }
        };
        run();
        timeoutRun = setInterval(run, 1000);
    }
};