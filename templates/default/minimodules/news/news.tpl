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

<div id="news_list">
    <table>
        {foreach from=$minimodule.news item=news}
            <tr>
                <td title="Envoyée par {if $news.nickname != ''}{$news.nickname}{else}{$news.firstname} {$news.lastname}{/if}" >{if $news.group_name != null}<img src="data/groups/mini/{$news.group_name}" />{/if}</td>
                <td>{$news.title}</td>
            </tr>
        {/foreach}
    </table>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
