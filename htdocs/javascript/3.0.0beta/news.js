$(document).ready(function() {
	$('.news_select').show();
	$(".hide").click(function() {
		var name = '.' + $(this).attr('name');
		if($(this).attr('checked')) {
			$(name).hide();
		}
		else
		{
			$(name).show();
		}
	});
	$("#news_sub").click(function() {
		var name = '.' + $('#news_text').val();
		$(".news").hide();
		$(name).show();
		return true;
	});
	$(".hide2").click(function() {
		var name = '.' + $(this).attr('id');
		if($(this).html() == '[-]') {
			$(name).hide();
			$(this).html('[+]')
		}
		else
		{
			$(name).show();
			$(this).html('[-]')
		}
	});
});
