$(document).ready(function() {
	$('.news .switcher_open').click(function() {
	    $(this).closest('.news').switchClass('close unread', 'open read', 100);
	    var id = $(this).closest('.news').attr('nid');
        $.get('news/ajax/read/' + id + '/1');
	});
    $('.news .switcher_close').click(function() {
        $(this).closest('.news').switchClass('open', 'close', 100);
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
