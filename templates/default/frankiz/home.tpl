{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009-2013 Binet Réseau                                  *}
{*  http://br.binets.fr/                                                  *}
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


<div class="accueil">
    {if $smarty.session.auth == AUTH_PUBLIC}
        {include file="wiki.tpl"|rel name='home/public'}
    {else}
        {if $postit_news}
        <div class="postit minimodule">
            <div class="head">
                Post-it
            </div>
            <div class="body">
                {$postit_news->content()|miniwiki:'title'|smarty:nodefaults}
            </div>
        </div>
        {/if}
        <table>
            <tr>
                <td class="{if ($MiniModules_COL_LEFT|@count) == 0}empty{else}full{/if}">
                    <ul id="COL_LEFT" class="minimodules_zone">
                        {foreach from=$MiniModules_COL_LEFT item=minimodule}
                            {include file="minimodule.tpl"|rel minimodule=$minimodule}
                        {/foreach}
                    </ul>
                </td>
                <td class="{if ($MiniModules_COL_MIDDLE|@count) == 0}empty{else}full{/if}">
                    <ul id="COL_MIDDLE" class="minimodules_zone">
                        {foreach from=$MiniModules_COL_MIDDLE item=minimodule}
                            {include file="minimodule.tpl"|rel minimodule=$minimodule}
                        {/foreach}
                    </ul>
                </td>
                <td class="{if ($MiniModules_COL_RIGHT|@count) == 0}empty{else}full{/if}">
                    <ul id="COL_RIGHT" class="minimodules_zone">
                        {foreach from=$MiniModules_COL_RIGHT item=minimodule}
                            {include file="minimodule.tpl"|rel minimodule=$minimodule}
                        {/foreach}
                    </ul>
                </td>
            </tr>
        </table>
        <br class="clear" />
    {/if}
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
