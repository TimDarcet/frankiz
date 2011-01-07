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


{if $smarty.session.auth < AUTH_COOKIE}
    <a {path_to_href_attribute path="login"} nosolo="true" accesskey="l">Se connecter</a>
{/if}

{if $smarty.session.auth >= AUTH_COOKIE}
    {assign var='face' value=$smarty.session.user->image()}
    <img src="{$face->src()|smarty:nodefaults}" class="face" />
    <div>
        <p class="name">{$smarty.session.user->displayName()}</p>
        <ul>
            <li><a {path_to_href_attribute path="profile"} accesskey="c">Compte</a></li>
            <li><a {path_to_href_attribute path="exit"} nosolo="true" accesskey="l">Se déconnecter</a></li>
        </ul>
    </div>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
