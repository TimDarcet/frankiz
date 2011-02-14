$(document).ready(function() {
    $('.news .title').click(function() {
        var news = $(this).closest('.news');
        if (news.hasClass('unread')) {
            var id = news.attr('nid');
            request('news/ajax/read/' + id + '/1');
        }

        if (news.hasClass('close')) {
            news.switchClass('close unread', 'open read', 100);
        } else {
            news.switchClass('open', 'close', 100);
        }
    });

    $('.open_all').click(function() {
        var unread_news = $(this).siblings('ul').first().children('li.unread');

        var ids = [];
        unread_news.each(function() {
            ids.push($(this).attr('nid'));
            $(this).addClass('open read');
            $(this).removeClass('close unread');
        });

        ids = ids.join(',');
        request('news/ajax/read/' + ids + '/1');
    });

    $('.news .star_switcher').click(function() {
        var news = $(this).closest('.news');
        if (news.hasClass('unstar')) {
            news.switchClass('unstar', 'star', 100);
            var id = $(this).closest('.news').attr('nid');
            request('news/ajax/star/' + id + '/1');
        } else {
            news.switchClass('star', 'unstar', 100);
            var id = $(this).closest('.news').attr('nid');
            request('news/ajax/star/' + id + '/0');
        }
    });

    // for news/admin
    $('.hide').hide();
    if ($("#news_show").html() == '\n')
        $("#news_show").hide();
    $("#news_admin input[name='admin_id']").change(function(){
        request({ "url": 'news/ajax/admin'
                ,"data": {'id': $("input[@name='admin_id']:checked").val()}
             ,"success": function(json) {
                    $("#news_show").html(json.news);
                    $("#news_admin #news_show").focusout(function(){
                        change();
                    });
                    $("#news_admin #news_show").change(function(){
                        has_changed = true;
                    });
                    $('.hide').hide();
             }
        });
        $("#news_show").show();
    });

});

var has_changed = false;

function change()
{
    if (has_changed)
    {
        has_changed = false;
        request({ "url": 'news/ajax/modify'
                ,"data": $('#news_admin').formToJSON()
             ,"failure":function(json) {
                        $(".msg_proposal").html('La requete n\'a pas pu être envoyée.');
                        $(".msg_proposal").show();
             }
             ,"success": function(json) {
                        if (json.success)
                            $(".msg_proposal").html('L\'annonce a été modifiée avec succès. <br/> ' +
                                    '<span class="small">Si vous avez changé le titre, ' +
                                    'rechargez la page pour qu\'il soit actualisé.<span>');
                        else
                            $(".msg_proposal").html('La date demandée n\'est pas valide.');
                        $(".msg_proposal").show();
                        setTimeout(function() {$(".msg_proposal").toggle(150);}, 3000);
             }
        });
    }
}
