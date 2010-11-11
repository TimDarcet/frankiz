$(document).ready(function() {
	$(".hide").hide();
	$(".click").click(function() {
		$(this).next(".hide").slideToggle(0);
	});
	$(".show").show();
});
