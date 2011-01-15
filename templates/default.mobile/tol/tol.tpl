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


<div id="message" style="color:red"></div>

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
jQuery(document).ready( function() {
    
    $('#tol_results .more-button').click(function() {
        var loader = $(this).next('.more').children('.loader');
        $(this).next('.more').toggle('fast');
        if ($(loader).is(':empty')) {
            $(loader).addClass('loading');

            var img = new Image();
        
            $(img)
            .load( function() {
                $(this).hide();

                $(loader)
                .removeClass('loading')
                .append(this);

                $(this).fadeIn();
            })

            .error( function() {
                $(loader).text('No image found.');
            })

            .attr('src', $(loader).attr('src'));
        }
        return false;
    })
    .next('.more').hide();
});
</script>

{/literal}

{js src="tol.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
