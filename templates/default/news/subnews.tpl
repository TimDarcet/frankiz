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

<ul>
    {foreach from=$collection|order:'begin' item=news}
        {assign var='begin' value=$news->begin()}
        {assign var='age' value=$begin->age()}
        {assign var='target' value=$news->target()}
        {assign var='targetGroup' value=$target->group()}
        <li class="news {if $selected_id == $news->id()}open{else}close{/if} {if $news->read()}read{else}unread{/if} {if $news->star()}star{else}unstar{/if} {if $age->invert}tocome{/if}"
            nid="{$news->id()}">
            <div class="infos">
                <table><tr>
                    {if $logged}
                        <td>
                            <div class="star_switcher" title="Suivre l'annonce"></div>
                        </td>
                    {/if}
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
                    <td class="title">
                        <a name="news_{$news->id()}" href="news/{$news->id()}">{$news->title()}</a>
                    </td>
                    <td class="date">
                        {$news->begin()|age}
                    </td>
                </tr></table>
            </div>
            <div class="content">
                <div class="body">
                    {if $news->image()}
                        <a href="{$news->image()|image:'full'}" fancy="fancy"><img class="image" src="{$news->image()|image:'big'}" /></a>
                    {/if}
                    {$news->content()|miniwiki:'title'|smarty:nodefaults}
                    <br class="clear" />
                </div>
                <div class="under">
                    <table><tr>
                        {if $user|hasRights:$targetGroup:'admin' || $user->isWeb()}
                            <td class="admin{if !($user|hasRights:$targetGroup:'admin')} webmaster{/if}">
                                <a href="news/admin/{$news->id()}"><div class="edit"></div>Modifier</a>
                            </td>
                        {/if}
                        <td class="target">
                            Visible par « {$targetGroup->label()} »
                            <span class="target_rights">
                                {if $target->isRights('restricted')}
                                    (Restreint)
                                {elseif $target->isRights('everybody')}
                                    (Public)
                                {else}
                                    (Spécial)
                                {/if}
                            </span>
                            du <span title="{$news->begin()|datetime}">{$news->begin()|datetime:'d/m'}</span>
                            {if $news->begin()|datetime:'d/m' != $news->end()|datetime:'d/m'}
                                au <span title="{$news->end()|datetime}">{$news->end()|datetime:'d/m'}</span>
                            {/if}
                            {if ($logged && !($user->hasRights($targetGroup)))}
                                <div><strong>Voir les annonces de « {$targetGroup->label()} » dans mon fil principal en </strong>
                                <span class="rights friend"></span>
                                <a onclick="return confirm(areyousure);" href="groups/subscribe/{$targetGroup->name()}?token={xsrf_token}">devenant sympathisant</a></div>
                            {/if}
                        </td>
                        {if $smarty.session.auth >= AUTH_INTERNAL}
                        <td class="writer">
                            {$news->writer()|user:'text'}{$news->writer()|user}
                        </td>
                        {/if}
                    </tr></table>
                </div>
            </div>
        </li>
    {/foreach}
</ul>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
