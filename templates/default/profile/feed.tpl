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

<div class="module">
    <div class="head">
        Flux
    </div>
    {if !$user->hash_rss()}
        <div class="body">
            {if isset($desactivated|smarty:nodefaults)}
                <p>Tes flux ont été coupés.</p>
            {/if}

            Tu viens de cliquer sur le lien d'activation des flux d'exportation comprenant les fils RSS et le fil iCalendar.
            Les flux du site ne sont pas activés dans tes préférences. <br/>
            <p>Tu peux le faire tout de suite en cliquant sur Activer.</p>

            <form enctype='multipart/form-data' method='post' action='admin'>
                <input type="submit" value="Retour" />
                <input type="submit" name="act_rss" value="Activer" onclick="this.form.action='profile/feed'" />
            </form>
        </div>

    {else}
        {if $success}
            <p>
                Tes flux d'exportation sont activés
            </p>
        {/if}
        
        <div class="body">
            Voici les adresses des flux :
            <ul>
                <li>
                    RSS :
                    <ul>
                        <li>
                            Annonces :
                            <a href="news/rss/{$user->login()}/{$user->hash_rss()}/rss.xml" class="feed">
                                <span class="rss"></span>
                            </a>
                        </li>
                        <li>
                            Activités :
                            <a href="activity/rss/{$user->login()}/{$user->hash_rss()}/rss.xml" class="feed">
                                <span class="rss"></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    iCalendar :
                        <ul>
                            <li>
                                mes activités :
                                <a href="activity/icalendar/participate/{$user->login()}/{$user->hash_rss()}/ical.ics" class="feed">
                                    <span class="cal"></span>
                                </a>
                            </li>
                            <li>
                                mes groupes :
                                <a href="activity/icalendar/friends/{$user->login()}/{$user->hash_rss()}/ical.ics" class="feed">
                                    <span class="cal"></span>
                                </a>
                            </li>
                        </ul>
                </li>
            </ul>
            <p>
                <form enctype='multipart/form-data' method='post' action='profile/feed'>
                    Désactiver les flux :
                    <input type="submit" name="des_rss" value="Désactiver" />
                </form>
            </p>
            <p>
                Attention : désactiver, puis réactiver les flux en change les adresses.
            </p>
        </div>
    {/if}
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
