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

<div class="threecols index">
    <div class="module">
        <div class="head"><span class="helper" target="admin/index/account"> </span>Compte</div>
        <div class="body">
            <ul>
                <li><a href="profile/account">Modifier mon compte</a></li>
                <li><a href="profile/mails">Mes mails</a></li>
                <li><a href="profile/skin">Changer l'habillage</a></li>
                <li><a href="profile/minimodules">Gérer les minimodules</a></li>
                <li><a href="profile/network">Gérer mes données réseau</a></li>
            </ul>
            {if $smarty.session.user->checkPerms('admin')}
                <ul>
                    <li><a href="wiki/admin">Administrer les wikis</a></li>
                </ul>
            {/if}
        </div>
    </div>

    <div class="module">
        <div class="head"><span class="helper" target="admin/index/proposal"></span>Contribuer</div>
        <div class="body">
            <ul>
                <li><a href="proposal/news">Annonce</a></li>
                <li><a href="proposal/activity">Activité</a></li>
                <li><a href="proposal/survey">Sondage</a></li>
                <li><a href="proposal/mail">Mail promo</a></li>
                <li><a href="proposal/qdj">Question Du Jour</a></li>
            </ul>
        </div>
    </div>

    <div class="module">
        <div class="head"><span class="helper" target="admin/index/groups"></span>Groupes & Binets</div>
        <div class="body">
            <ul>
            {foreach from=$admin_groups|order:'score' item='group'}
                <li class="group">
                    <img src="{$group->image()|image:'micro':'group'}" />
                    <div>
                        <div class="label">{$group|group}</div>
                        <div class="admin"><a href="groups/admin/{$group->id()}">Administrer</a></div>
                        {assign var='id' value=$group->id()}
                        {if t($validates.$id)}
                            <div class="valid">
                                <a href="admin/validate/{$group->id()}">{$validates.$id|@count} requêtes</a>
                            </div>
                        {/if}
                    </div>
                </li>
            {/foreach}
            </ul>
        </div>
    </div>

</div>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
