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


<div style="display:none">
{if $smarty.session.auth >= AUTH_COOKIE}
    TODO
        <a href="groups/unsubscribe/{$group->id()}">Quitter le groupe</a>
        <a href="groups/subscribe/{$group->id()}">Devenir {if !$group->priv()}membre{else}sympathisant{/if}</a>
{/if}
</div>

<div class="top">
    <img src="{$group->image()|image:'full'|smarty:nodefaults}" />
    <div class="www">{if $group->web()}<a href="{$group->web()}">{$group->web()}</a>{/if}</div>
    <div class="mail">{if $group->mail()}{$group->mail()}{/if}</div>
    <div class="description">{$group->description()|miniwiki}</div>
</div>

{if $smarty.session.auth >= AUTH_INTERNAL}
    <div class="bottom">
        <div class="users">
            <div class="filters">
                <form name="filters">
                <input type="hidden" id="gid" name="gid" value="{$group->id()}" />
                <label>Promo{include file="groups_picker.tpl"|rel id="promo" ns="promo" check=-1 already=$promos}</label>
                </form>
            </div>

            <ul class="rights">
                <li>
                Administrateurs:
                <ul class="admin">

                </ul>
                </li>
                <li>
                Membres:
                <ul class="member">

                </ul>
                </li>
            </ul>
        </div>
        
        <div class="news">
            <ul>
            {foreach from=$news item='new'}
                <li>
                    {$new->title()}
                </li>
            {/foreach}
            </ul>
        </div>
    </div>

    {js src="groups.js"}
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
