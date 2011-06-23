<ul id="pix_last">
{foreach from=$minimodule.photos item='photo'}
        <li><a href="{$photo.link1}/"><img src="{$photo.link2}" title="{$photo.title}" alt="{$photo.title}" /></a></li>
{/foreach}
</ul>
<div id="pix_archive"><a href="http://pix/photos/">Toutes les photos</a></div>