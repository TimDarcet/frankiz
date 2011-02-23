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

<div class="open_all">
    Ouvrir toutes les annonces non-lues
</div>

<ul>
    {foreach from=$collection|order:'begin' item=news}
        <li class="news close {if $news->read()}read{else}unread{/if} {if $news->star()}star{else}unstar{/if}"
            nid="{$news->id()}">
            <div class="infos">
                <table><tr>
                    <td>
                        <div class="star_switcher" title="Suivre l'annonce"></div>
                    </td>
                    <td class="origin">
                        {if $news->origin()}
                            {$news->origin()|group}
                        {else}
                            {$news->writer()|user}
                        {/if}
                    </td>
                    {assign var='target' value=$news->target()}
                    {assign var='targetGroup' value=$target->group()}
                    <td class="title" title="Visible par '{$targetGroup->label()}'">
                        <a href="news/see/{$news->id()}"><div>{$news->title()}</div></a>
                    </td>
                    <td class="date">
                        {$news->begin()|age}
                    </td>
                </tr></table>
            </div>
            <div class="content">
                <div class="body">
                    {if $news->image()}
                        <img class="image" src="{$news->image()|image:'small'|smarty:nodefaults}" />
                    {/if}
                    {$news->content()|miniwiki:'title'|smarty:nodefaults}
                    <br class="clear" />
                </div>
                {canEdit target=$news->target()}
                    <div class="admin">
                        <a href="news/admin/{$news->id()}"><div class="edit"></div>Modifier</a>
                    </div>
                {/canEdit}
                <div class="writer">
                    Rédigée par {$news->writer()|user}
                </div>
            </div>
        </li>
    {/foreach}
</ul>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
