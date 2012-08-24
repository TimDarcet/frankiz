$(function() {
    $('#section .news .title').click(function() {
        var news = $(this).closest('.news');

        if (logged) {
            if (news.hasClass('unread')) {
                var id = news.attr('nid');
                request('news/ajax/read/' + id + '/1');
            }
        }

        if (news.hasClass('close')) {
            news.switchClass('close unread', 'open read', 100);
        } else {
            news.switchClass('open', 'close', 100);
        }

        return false;
    });

    function markRead(ids) {
        if (ids.length > 0) {
            ids = ids.join(',');
            request('news/ajax/read/' + ids + '/1');
        }
    }

    $('#section .open_all_unread').click(function() {
        var unread_news = $("#section .list").find('li.news.unread');

        unread_news.addClass('open read');
        unread_news.removeClass('close unread');

        markRead(unread_news.batchAttr('nid'));
    });

    $('#section .read_all').click(function() {
        var unread_news = $("#section .list").find('li.news.unread');

        unread_news.addClass('read');
        unread_news.removeClass('unread');

        markRead(unread_news.batchAttr('nid'));
    });

    $('#section .open_all').click(function() {
        var unread_news = $("#section .list").find('li.news.unread');
        markRead(unread_news.batchAttr('nid'));

        var all_news = $("#section .list").find('li.news');
        all_news.addClass('open read');
        all_news.removeClass('close unread');
    });

    $('#section .close_all').click(function() {
        var all_news = $("#section .list").find('li.news');
        all_news.removeClass('open');
        all_news.addClass('close');
    });

    $('#section [nid] .star_switcher').click(function() {
        var $news = $(this).closest('[nid]');
        var nid = $news.attr('nid');

        if ($news.hasClass('unstar')) {
            $news.switchClass('unstar', 'star', 100);
            request('news/ajax/star/' + nid + '/1');
        } else {
            $news.switchClass('star', 'unstar', 100);
            request('news/ajax/star/' + nid + '/0');
        }
    });
});
