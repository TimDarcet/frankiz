$(function(){
    var nss = ['binet', 'course', 'free'];
    for (var i in nss) {
        (function() {
            var ns = nss[i];
            var $input = $("#section input[name=filter_" + ns +"]");
            if ($input) {
                var filter_binet = groups(ns, $input);
                $input.keyup(function() {
                    filter_binet.search();
                });
            }
        })();
    }
});

var groups = function(ns, $input) {
    var searching = false;
    var newsearch = false;

    function force_search() {
        request({
            "url": 'groups/ajax/search'
          ,"data": {"ns": ns, "piece": $input.val(), "html": true}
       ,"success": function(json) {
                var ul = $("#section tbody." + ns);

                var groups = json.groups;

                ul.empty();

                var html = [];

                for (var i in groups) {
                    html.push(groups[i]);
                }

                ul.append(html.join(''));

                searching = false;
                if (newsearch !== false) {
                    _search();
                }
            }});
    };

    function _search() {
        newsearch = true;
        if (!searching) {
            searching = true;
            newsearch = false;
            force_search();
        }
    }

    return {
        search: function () {
            _search();
        }
    };
};
