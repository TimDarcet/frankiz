{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet Réseau                                       *}
{*  http://www.polytechnique.fr/eleves/binets/reseau/                     *}
{*                                                                        *}
{*  This program is free software; you can redistribute it and/or modify  *}
{*  it under the terms of the GNU General Public License as published by  *}
{*  the Free Software Foundation; either version 2 of the License, or     *}
{*  (at your option) any later version.                                   *}
{*                                                                        *}
{*  This program is distributed in the hope that it will be useful,       *}
{*  but WITHOUT ANY WARRANTY; without even the implied warranty of        *}
{*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *}
{*  GNU General Public License for more details.                          *}
{*                                                                        *}
{*  You should have received a copy of the GNU General Public License     *}
{*  along with this program; if not, write to the Free Software           *}
{*  Foundation, Inc.,                                                     *}
{*  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA               *}
{*                                                                        *}
{**************************************************************************}

<div class="trombino">
    {if isset($results|smarty:nodefaults)}
    <div id="tol_results">
        {foreach from=$results item=result}
        {include file="tol/sheet.tpl"|rel result=$result}
        {/foreach}
    </div>
    {else}
        {include file="tol/search.tpl"|rel}
    {/if}
</div>

{literal}
<script>
$(function() {
    $('#tol_results .more-button').click(function() {
        $(this).siblings('.more').toggle('fast');
        return false;
    });

    $('#tol_results .show-photo').click(function() {
        var showphoto = $(this);
        var photo = $(this).children('.img');

        $(photo).addClass('loading');
        $(photo).attr('src', '');
        var typephoto = $(photo).attr('type');

        var urlphotobig = $(this).closest('[photobig]').attr('photobig');
        var urlphotomicro = $(this).closest('[photomicro]').attr('photomicro');
        var img = new Image();

        $(img)
        .load( function() {
            $(this).hide();
            $(this).addClass('img');
            $(this).attr('style', '');

            if (typephoto == 'big') {
                $(this).attr('type', 'micro');
                
            } else {
                $(this).attr('type', 'big');
            }

            $(photo).remove();
            $(showphoto).append(this);
        })

        .error( function() {
            $(photo).text('No image found.');
        })
        if (typephoto == 'big') {
            $(img).attr('src', urlphotomicro);
            var name = $(showphoto).siblings('.name');
            $(showphoto).insertBefore($(name));
        } else {
            $(img).attr('src', urlphotobig);
            var name = $(showphoto).siblings('.name');
            $(showphoto).insertAfter($(name));
        }

        return false;
    })
});
</script>
{/literal}

{js src="tol.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
