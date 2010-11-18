$(document).ready(function() {
	
	// For validations
	$(".hide").hide();
	$(".click").click(function() {
		$(this).next(".hide").slideToggle(0);
	});
	$(".show").show();
	
	$(".addcom_validate").hide();
	$(".text_validate").height(15);
	$(".text_validate").click(function() {
		$(".addcom_validate").slideToggle(150);
		if ($(".text_validate").height() == 80)
			$(".text_validate").height(15);
		else
			$(".text_validate").height(80);
	});
	
	// For propositions
	wiki_preview.start($('#text_proposal'), $('#preview_proposal'));

	if ($('#regular_activity_proposal').attr('checked'))
		$("#number_activity_proposal").show();
	else
		$("#number_activity_proposal").hide();
	$("#regular_activity_proposal").click(function() {
		if ($(this).attr('checked'))
			$("#number_activity_proposal").show();
		else
			$("#number_activity_proposal").hide();
	});
});