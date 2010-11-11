$(document).ready(function() {
	$(".prop_desc").hide();
	$(".prop_obj").click(function() {
		$(this).next(".prop_desc").slideToggle(150);
	});
	$(".section_body").hide();
	$(".section_title").click(function() {
		$(this).next(".section_body").slideToggle(150);
	});
});
