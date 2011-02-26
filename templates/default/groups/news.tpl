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

{assign var='begin' value=$news->begin()}
{assign var='age' value=$begin->age()}
<tr class="{if $news->read()}read{else}unread{/if} {if $news->star()}star{else}unstar{/if} {if $age->invert}tocome{/if}">
    <td class="origin">
        {$news->writer()|user}
    </td>
    <td class="title" {if $age->invert}title="Cette annonce n'apparait qu'aux administrateurs car sa date de parution est future"{/if}>
        <a href="news/{$news->id()}">{$news->title()}</a>
    </td>
    <td class="date">
        {$news->begin()|age}
    </td>
</tr>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
