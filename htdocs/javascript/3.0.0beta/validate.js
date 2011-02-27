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

    $("input[type=submit][name=refuse]").click(function() {
        if (!$(this).closest("li.validate").find("textarea[name=ans]").val()) {
            alert("Il manque une raison au refus");
            return false;
        }
    });

    // For propositions
    // mails
    $("input[name=type_mail_proposal]").click(function() {
        $(".type_proposal").hide();
        $("#" + $(this).attr('value') + "_proposal").show();
    });

    // activities
    $("input[name='regular_activity_proposal']").change(function(){
        if ($("input[@name='regular_activity_proposal']:checked").val() == '0')
        {
            $("#new_activity_proposal").show();
            $("#old_activity_proposal").html('');
            $("#old_activity_proposal").hide();
        }
        else        {
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
});

function activity_show(part) {
    $(".activity_prop").hide();
    $(".activity_prop." + part).show();
}
