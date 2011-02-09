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

{if $smarty.session.auth >= AUTH_COOKIE}
    <div class="account">
        <a href="tol?hruid={$smarty.session.user->login()}">
        <img src="{$smarty.session.user->image()|image:'big'|smarty:nodefaults}"
             title="{$smarty.session.user->displayName()}" />
        </a>
    </div>
{/if}

<ul>
    {if $smarty.session.auth >= AUTH_INTERNAL}
        <li><a {path_to_href_attribute path="news"}>annonces</a></li>
        <li><a {path_to_href_attribute path="activity"}>activités</a> / <a {path_to_href_attribute path="activity/timetable"}>edT</a></li>
        <li><a {path_to_href_attribute path="groups"}>groupes & binets</a></li>
        <li><a {path_to_href_attribute path="tol"} accesskey="t">trombino</a></li>
    {/if}
    {if $smarty.session.auth >= AUTH_COOKIE}
        <li><a {path_to_href_attribute path="admin"} accesskey="g">administration</a><li>
        <li><a {path_to_href_attribute path="exit"} accesskey="l">Se déconnecter</a></li>
    {else}
        <li class="log"><a {path_to_href_attribute path="login"} accesskey="l">Se connecter</a></li>
    {/if}
</ul>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
