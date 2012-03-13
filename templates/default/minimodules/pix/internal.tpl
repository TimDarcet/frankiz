<ul id="pix_last" >
{foreach from=$minimodule.photos key=index item='photo' name=count}
    <li>
        <a rel="pix_group" title="{$photo.text}" name="{$photo.imgFull}" href="{$photo.urlPage}" >{if $smarty.foreach.count.index < $minimodule.nMontrees}<img src="{$photo.imgThumb}" title="{$photo.title}" alt="{$photo.title}" />{/if}</a>
    </li>
{/foreach}
</ul>
<div id="pix_archive"><a href="http://pix/photos/">Toutes les photos</a></div>
