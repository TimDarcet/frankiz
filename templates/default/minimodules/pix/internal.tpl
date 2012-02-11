<ul id="pix_last" >
{foreach from=$minimodule.photos item='photo'}
    <li>
        <a rel="pix_group" title="{$photo.text}" name="{$photo.imgFull}" href="{$photo.urlPage}" ><img src="{$photo.imgThumb}" title="{$photo.title}" alt="{$photo.title}" /></a>
    </li>
{/foreach}
</ul>
<div id="pix_archive"><a href="http://pix/photos/">Toutes les photos</a></div>
