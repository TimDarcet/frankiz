function survey_editor(id) {
    $container = $("#survey_editor_" + id);
    $questions = $container.find("ul.questions");
    $sources   = $container.find(".questions_sources");

    $container.closest('form').submit(function() {
        $container.find('#' + id).val(json_export());
    });

    $container.find("input[sname=add_question]").click(function() {
        var type = $("#add_question_type").val();
        if (type == 'text') {
            add_questiontext();
        } else if (type == 'choices') {
            add_questionchoices();
        }
    });

    $questions.sortable({
        stop: function(event, ui) {
            post_order();
        }
    });

    $questions.delegate(".rm", "click", function() {
        if (confirm("Certain ?")) {
            $(this).closest(".question").remove();
            post_order();
        }
    });

    $questions.delegate(".add_choice", "click", function() {
        var $this = $(this);
        $this.siblings('.choices').append('<li><input type="text" value="" placeholder="Choix" /><input type="button" class="rm_choice" /></li>');
        post_choice($this.closest('.question'));
    });

    $questions.delegate(".rm_choice", "click", function() {
        var $this = $(this);
        if ($this.closest(".choices").children().length > 1) {
            var $question = $this.closest('.question');
            $this.closest('li').remove();
            post_choice($question);
        }
    });

    function add_questiontext() {
        var q = $sources.find(".questiontext").clone(true);
        $questions.append(q);
        post_order();
    }

    function add_questionchoices() {
        var q = $sources.find(".questionchoices").clone(true);
        $questions.append(q);
        post_order();
    }

    function post_order() {
        $questions.children(".question").each(function(i) {
            var $this = $(this);
            var j = i + 1;
            $this.find(".nb").html(j + ".");
            $this.removeClass("pair").removeClass("impair");
            if (j % 2 == 0) {
                $this.addClass("pair");
            } else {
                $this.addClass("impair");
            }
        });
    }

    function post_choice($question) {
        var nb = $question.find('.choices').children().length;
        var $select = $question.find('select[sname=max]');

        var max = $select.val();

        var options = [];
        for (var i = 1; i <= nb; i++) {
            options.push('<option value="' + i + '">' + i + '</option>');
        }

        $select.html(options.join(''));
        $select.val(max);
    }

    function json_export() {
        var survey = [];
        $questions.children(".question").each(function() {
            $this = $(this);
            var q = {};
            q.label = $this.find('input[sname=label]').val();
            q.description = $this.find('textarea[sname=description]').val();
            q.mandatory = ($this.find('input[sname=mandatory]').attr('checked')) ? true : false;
            if ($this.find('select[sname=max]').length == 1) {
                q.max = $this.find('select[sname=max]').val();
                q.choices = [];
                $this.find('.choices input[type="text"]').each(function() {
                    q.choices.push($(this).val());
                });
            }
            survey.push(q);
        });
        return survey;
    }
};