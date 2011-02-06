$(function() {

    $("li.validate > .title").click(function() {
        $(this).siblings(".more").toggle();
    });

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

	// activities
	$("input[name='regular_activity_proposal']").change(function(){
	    if ($("input[@name='regular_activity_proposal']:checked").val() == '0')
	    {
	    	$("#new_activity_proposal").show();
	    	$("#old_activity_proposal").html('');
	    	$("#old_activity_proposal").hide();
	    }
	    else 
	    {
	    	$("#new_activity_proposal").hide();
	    	$("#old_activity_proposal").show();
	    	$.ajax({
                type: 'POST',
                 url: 'proposal/activity/ajax',
                data: 'aid=' + $("input[@name='regular_activity_proposal']:checked").val(),
             success: function(data) { $("#old_activity_proposal").html(data); }
          });
	    }
	});
	
	
	
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
	
	// qdj
	wiki_preview.start($('#quest_qdj_proposal'), $('.question'));
	wiki_preview.start($('#ans1_qdj_proposal'), $('.answer1'));
	wiki_preview.start($('#ans2_qdj_proposal'), $('.answer2'));

});