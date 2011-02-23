$(function() {
    $('.news .title a').each(function() {
        $(this).height($(this).closest('td').height());
    });
    $('.news .title a').click(function() {
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

        return false;
    });

    $('.open_all').click(function() {
        var unread_news = $(this).siblings('ul').first().children('li.unread');

        var ids = [];
        unread_news.each(function() {
            ids.push($(this).attr('nid'));
            $(this).addClass('open read');
            $(this).removeClass('close unread');
        });

        if (ids.length > 0) {
            ids = ids.join(',');
            request('news/ajax/read/' + ids + '/1');
        }
    });

    $('[nid] .star_switcher').click(function() {
        var $news = $(this).closest('[nid]');
        var nid = $news.attr('nid');

        if ($news.hasClass('unstar')) {
            $news.switchClass('unstar', 'star', 100);
            request('news/ajax/star/' + nid + '/1');
        } else {
            $news.switchClass('star', 'unstar', 100);
            request('news/ajax/star/' + nid + '/0');
        }
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
