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

<div>
{if $minimodule.news|@count > 0}
    <table>
        {foreach from=$minimodule.news item=news}
            <tr class="{if $news->star()}star{else}unstar{/if}">
                <td class="origin">
                    {if $news->origin()}
                        {assign var='origin' value=$news->origin()}
                        {if $origin->image()}
                            {$origin|group}
                        {else}
                            {$origin|group:'text'}
                        {/if}
                    {else}
                        {$news->writer()|user}
                    {/if}
                </td>
                <td class="title"><a href="news/new/{$news->id()}">{$news->title()}</a></td>
            </tr>
        {/foreach}
    </table>
{else}
    <div class="empty">Rien de neuf !</div>
{/if}
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
