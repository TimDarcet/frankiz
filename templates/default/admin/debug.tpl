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

{literal}
<style type="text/css">
    #content ul {
        padding:-top: 0;
    }

    #collapse_debug ul ul {
        display: none;
    }
</style>
{/literal}

<div class="module">
    <div class="head">
        Session
    </div>
    <div class="body">
        <ul>
        {foreach from=$session key='k' item='s'}
            <li>
                <span onclick="$(this).siblings('div').toggle();">{$k}</span>
                <div style="display:none" id="collapse_debug">{$s|smarty:nodefaults}</div>
            </li>
        {/foreach}
        </ul>
    </div>
</div>

{literal}
<script>
    $(function() {
        $("#collapse_debug").find('span').click(function() {
            console.log(this);
            $(this).siblings('ul').toggle();
        });
    });
</script>
{/literal}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
