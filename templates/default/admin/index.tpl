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
        <div class="head"><span class="helper" target="admin/index/account"></span>Compte</div>
        <div class="body">
            <ul class="bicol">
                <li class="pair"><a href="profile/account">Mon profil</a></li>
                <li class="impair"><a href="profile/password">Mot de passe</a></li>
                <li class="pair"><a href="profile/defaultfilters">Filtre par défaut</a></li>
                <li class="impair"><a href="profile/minimodules">Mes minimodules</a></li>
                <li class="pair"><a href="profile/network">Mes données réseau</a></li>
                <li class="impair"><a href="profile/feed">Mes flux</a></li>
                <li class="pair"><a href="profile/skin">Choix de l'habillage</a></li>
            {if $licensesDisplay}<li class="pair"><a href="licenses">Licences MSDNAA</a></li>{/if}
            </ul>
            {if $smarty.session.user->isWeb()}
                <ul class="webmaster">
                    <li class="{if $licensesDisplay}im{/if}pair"><a href="profile/admin/account">Créer un compte</a></li>
                </ul>
                <ul class="webmaster">
                    <li class="{if !$licensesDisplay}im{/if}pair"><a href="wiki/admin">Les zones wikis</a></li>
                </ul>
            {/if}
            {if $smarty.session.user->isAdmin()}
                <ul class="fkzadmin">
                    <li class="{if $licensesDisplay}im{/if}pair"><a href="admin/logs/sessions">Log des sessions</a></li>
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
                <li><a href="proposal/mail">Mail promo</a></li>
                <li><a href="proposal/qdj">Question Du Jour</a></li>
            </ul>
        </div>
    </div>

    <div class="module">
        <div class="head"><span class="helper" target="admin/index/groups"></span>Groupes & Binets</div>
        <div class="body">
            <ul class="bicol">
            {if $admin_groups|count == 0}
                <li>Tu n'administres aucun binet</li>
            {/if}
            {foreach from=$admin_groups|order:'score' item='group' name='foo'}
                <li class="group {if $smarty.foreach.foo.index % 2}pair{else}impair{/if}">
                    <table><tr>
                    {if $smarty.foreach.foo.index % 2}
                        <td class="img">
                            <img src="{$group->image()|image:'micro':'group'}" />
                        </td>
                    {/if}
                    <td>
                        <div class="label">{$group|group:'text'}</div>
                        <div class="admin">
                            <a href="groups/admin/{$group->id()}">Administrer</a>
                            {assign var='id' value=$group->id()}
                            {if t($validates.$id)}
                                <a class="warning" href="admin/validate/{$group->id()}">{$validates.$id|@count} requêtes</a>
                            {/if}
                        </div>
                    </td>
                    {if $smarty.foreach.foo.index % 2 == 0}
                        <td class="img">
                            <img src="{$group->image()|image:'micro':'group'}" />
                        </td>
                    {/if}
                    </tr></table>
                </li>
            {/foreach}
            </ul>
            {if $smarty.session.user->isWeb()}
            <hr />
                <div>
                    <a href="groups/insert">Créer un groupe</a>
                </div>
            {/if}
        </div>
    </div>

</div>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
