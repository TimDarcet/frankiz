var show_id=0;

var pointg={
    "backward" : function() {
        show_id = (show_id+2)%3;
        this.load();
    },

    "forward" : function() {
        show_id = (show_id+1)%3;
        this.load();
    },

    "load" : function() {
        switch(show_id)
        {
        case 0:
        {
            $("#minimodule_pointg .barInfo").show();
            $("#minimodule_pointg .annonceInfo").hide();
            $("#minimodule_pointg .edtInfo").hide();
        break;
        }

        case 1:
        {
            $("#minimodule_pointg .barInfo").hide();
            $("#minimodule_pointg .annonceInfo").show();
            $("#minimodule_pointg .edtInfo").hide();
        break;
        }

        case 2:
        {
            $("#minimodule_pointg .barInfo").hide();
            $("#minimodule_pointg .annonceInfo").hide();
            $("#minimodule_pointg .edtInfo").show();
        break;
        }
        }
    }
};
