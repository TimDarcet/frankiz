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
{*  This program is distributed in the hope tha0t it will be useful,       *}
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

<table>
    <tr>
        <td class="logo">
            <div class="minimodules_zone ui-sortable" id="remove_minimodule_zone">
                <a href="home/"> </a>
            </div>
        </td>

    {if $quick_validate|@count > 0 || $quick_requests|@count > 0}
        <td id="quick_validate">
            {include file="quick_validate.tpl"|rel}
        </td>
    {/if}

        <td class="links">
            <ul>
                    <li><a {path_to_href_attribute path="news"}>annonces</a></li>
                    <li><a {path_to_href_attribute path="activity"}>activités</a></li>
                    <li><a title="Emploi du Temps" {path_to_href_attribute path="activity/timetable"}>edT</a></li>
                    <li><a {path_to_href_attribute path="groups"}>groupes & binets</a></li>
                {if $smarty.session.auth >= AUTH_INTERNAL}
                    <li><a {path_to_href_attribute path="tol"} accesskey="t">trombino</a></li>
                {/if}
                {if $smarty.session.auth >= AUTH_COOKIE}
                    <li><a {path_to_href_attribute path="admin"} accesskey="g">administration</a></li>
                {/if}
            </ul>
            <ul class="log">
                {smartphone}
                    <li>
                        <a href="profile/skin/resmartphone">Site Smartphone</a>
                    </li>
                {/smartphone}
                {if $smarty.session.auth >= AUTH_COOKIE}
                    <li><a {path_to_href_attribute path="exit"} accesskey="l">Se déconnecter</a></li>
                {else}
                    <li><a {path_to_href_attribute path="login"} accesskey="l">Se connecter</a></li>
                {/if}
            </ul>
        </td>

        <td class="account {if $smarty.session.auth < AUTH_COOKIE}empty{/if}">
            {if $smarty.session.auth >= AUTH_COOKIE}
                <a href="tol/see/{$user->login()}">
                <img src="{$user->image()|image:'small'|smarty:nodefaults}"
                     title="{$user->displayName()}" />
                </a>
            {/if}
        </td>
    </tr>
</table>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
