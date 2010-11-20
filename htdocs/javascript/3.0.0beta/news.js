$(document).ready(function() {
	$('.news .switcher_show').click(function() {
	    $(this).closest('.news').switchClass('hide', null, 100);
	    var id = $(this).closest('.news').attr('nid');
        $.get('news/ajax/show/' + id);
	});
    $('.news .switcher_hide').click(function() {
        $(this).closest('.news').switchClass(null, 'hide', 100);
        var id = $(this).closest('.news').attr('nid');
        $.get('news/ajax/hide/' + id);
    });
});
