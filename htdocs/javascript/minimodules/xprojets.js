// Preload des images
if (document.images) {
    img1 = new Image();
    img1.src = "css/default/images/Xprojets_button_back.png";
    img2 = new Image();
    img2.src = "css/default/images/Xprojets_button_cursor.png";
}

$(function() {
    function update_cursor($parent, value) {
        var $curs = $parent.children(".curs");
        var $back = $parent.children(".back");
        var position = value ? $back.width() - $curs.width() : 0;
        $curs.animate({left: position+"px"}, 500, "swing");
    }

    $("#minimodule_xprojets input.button_on_off").each(function() {
        //création de l'élement contenant le tout
        $(this).parent().prepend($("<div></div>")
            .css("display", "inline")
            .css("position", "relative")
            //création de l'image de fond
            .append($("<img></img>")
                .attr("src", "css/default/images/Xprojets_button_back.png")
                .css("position", "relative")
                .addClass("back")
            )
            //création de l'image représentant le cache
            .append($("<img></img>")
                .attr("src", "css/default/images/Xprojets_button_cursor.png")
                .css("position", "absolute")
                .css("top", "-14px")
                .addClass("curs")
            )
            .click(function() {
                var $input = $(this).parent().children("input:checkbox");
                var val = !$input.attr("checked");
                $input.attr("checked", val);

                // Trouve quel bouton est modifié
                var param = 0;
                if ($input.attr("id") == "xprojets_int") param = 1;
                else if ($input.attr("id") == "xprojets_cdp") param = 2;
                if (param > 0) {
                    $.ajax({
                        type: "GET",
                        url: "http://www.xprojets.com/intervenant/frankiz/set.php",
                        data: { i: xprojets_hash, c: xprojets_data, p: param, v: val?1:0 },
                        async: false,
                        error: function(msg) {
                            alert( "Error !: " + msg );
                        }
                    });
                }
                update_cursor($(this), val);
            })
        );
        $(this).hide();
        $(this).change(function() {
            $(this).parent().children("div").first.click();
        });
    });

    // Récupération des données
    $.ajax({
        type: "GET",
        url: "http://www.xprojets.com/intervenant/frankiz/get.php",
        data: { i: xprojets_hash, c: xprojets_data },
        async: false,
        error:function(msg) {
           alert( "Erreur de chargement des préférences Xprojets : " + msg );
        },
        success: function(msg) {
            var elem = msg.split("///");
            $("#xprojets_int").attr("checked", parseInt(elem[0]) != 0);
            update_cursor($("#xprojets_int").parent().children("div"), parseInt(elem[0]) != 0);
            $("#xprojets_cdp").attr("checked", parseInt(elem[1]) != 0);
            update_cursor($("#xprojets_cdp").parent().children("div"), parseInt(elem[1]) != 0);
            if(elem[2] != "") {
                $("#xprojets_texte").html(elem[2]);
            } else {
                $("#xprojets_texte").hide();
            }
        }
    });
});
