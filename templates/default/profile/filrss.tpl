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

{if !$user->hash_rss()}

    <div class="rss">
        Tu viens de cliquer sur le lien d'activation des fils RSS.
        Les fils RSS du site ne sont pas activés dans tes préférences. <br/>
        <p>Tu peux le faire tout de suite en cliquant sur Activer.</p>

        <form enctype='multipart/form-data' method='post' action='profile'>
            <input type="submit" value="Retour" />
            <input type="submit" name="act_rss" value="Activer" onclick="this.form.action='profile/rss'" />
        </form>
    </div>
    
{else}
    {if $success}
        <div class="msg">
            Ton fil RSS est activé
        </div>
    {/if}
    <div class="rss">
        Voici les adresses du flux RSS :
        <ul>
            <li>
                Annonces :
                <a href="news/rss/{$user->login()}/{$user->hash_rss()}/rss.xml" class="feed">&nbsp;</a>
            </li>
            <li>
                Activités :
                <a href="activity/rss/{$user->login()}/{$user->hash_rss()}/rss.xml" class="feed">&nbsp;</a>
            </li>
        </ul>
        <p>
            Tu peux le désactiver en allant dans Préférences et en cliquant sur « désactiver les fils RSS ».
        </p>
        <p>
            Attention : désactiver, puis réactiver le fil RSS en change l'adresse.
        </p>
        <p>
            [<a href="profile">retour à la page du compte</a>]
        </p>
    </div>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
