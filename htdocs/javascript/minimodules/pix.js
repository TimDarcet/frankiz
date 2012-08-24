$(document).ready(
$("a[rel=pix_group]").each(
    function(){
        $(this).fancybox({
            'titlePosition' : 'inside',
            'href'          : this.name
        });
    })
);
