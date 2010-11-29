$(document).ready(function() {
	$('.news .title').click(function() {
	    var news = $(this).closest('.news');
	    if (news.hasClass('unread')) {
	        var id = news.attr('nid');
	        $.get('news/ajax/read/' + id + '/1');
	    }

	    if (news.hasClass('close')) {
	        news.switchClass('close unread', 'open read', 100);
	    } else {
	        news.switchClass('open', 'close', 100);
	    }
	});

    $('.open_all').click(function() {
        var unread_news = $(this).siblings('ul').first().children('li.unread');

        var ids = [];
        unread_news.each(function() {
            ids.push($(this).attr('nid'));
            $(this).addClass('open read');
            $(this).removeClass('close unread');
        });

        ids = ids.join(',');
        $.get('news/ajax/read/' + ids + '/1');
    });

    $('.news .switcher_star').click(function() {
        $(this).closest('.news').switchClass('unstar', 'star', 100);
        var id = $(this).closest('.news').attr('nid');
        $.get('news/ajax/star/' + id + '/1');
    });
    $('.news .switcher_unstar').click(function() {
        $(this).closest('.news').switchClass('star', 'unstar', 100);
        var id = $(this).closest('.news').attr('nid');
        $.get('news/ajax/star/' + id + '/0');
    });
});
