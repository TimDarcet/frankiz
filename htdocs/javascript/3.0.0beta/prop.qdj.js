$(function() {
    $('#quest_qdj_proposal').keyup(function() { $('#section .question').html($(this).val()); });
    $('#ans1_qdj_proposal').keyup(function() { $('#section .answer1').html($(this).val()); });
    $('#ans2_qdj_proposal').keyup(function() { $('#section .answer2').html($(this).val()); });
});