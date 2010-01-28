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

<div class="head">
    Navigation
</div>
<div class="body">
    <ul id="nav_menu" class="level0">
        <li><a class="arrow" /><a {path_to_href_attribute path="accueil"} accesskey="a">Accueil</a>
            <ul class="level1">
                <li><a {path_to_href_attribute path="accueil/annonces"}>Annonces</a></li>
                <li><a {path_to_href_attribute path="accueil/activites"}>Activités</a></li>
            </ul>
        </li>
        <li><a class="arrow" /><a {path_to_href_attribute path="profil"} accesskey="c">Compte</a>
            {if isset($smarty.session.suid|smarty:nodefaults) }
            <span class="warning">ATTENTION, su en cours. Pour revenir à ta vraie identité, clique <a href="exit/">ici</a></span>
            {/if}
            <ul class="level1">
                {if $smarty.session.auth < AUTH_COOKIE }
                    <li><a {path_to_href_attribute path="login"} accesskey="l">Se connecter</a></li>
                {/if}
                {if $smarty.session.auth >= AUTH_COOKIE }
                    <li><a {path_to_href_attribute path="exit"} accesskey="l">Se déconnecter de {$smarty.session.user->displayName() }</a></li>
                    <li><a {path_to_href_attribute path="profil/fkz"}>Profil</a></li>
                    <li><a {path_to_href_attribute path="profil/minimodules"}>Minimodules</a></li>
                {/if}
                <li><a {path_to_href_attribute path="profil/skin"}>Skin</a></li>
            </ul>  
        </li>
        {if $smarty.session.auth >= AUTH_INTERNE }
        <li><a class="arrow" /><a {path_to_href_attribute path="tol"} accesskey="t">Trombino</a></li>
        {/if}
        {if $smarty.session.auth >= AUTH_COOKIE }
        <li><a class="arrow" /><a {path_to_href_attribute path="club"} >Binets</a>
            <ul id="nav_club" class="level1">
                {foreach from=$clubs_layout item=club}
                <li gid="{$club.gid}">
                    <a {path_to_href_attribute path="groups/show/"|cat:$club.name}>{$club.long_name}</a>
                </li>
                {/foreach}
            </ul>
        </li>
        <li><a class="arrow" /><a {path_to_href_attribute path="free"} >Groupes</a>
            <ul id="nav_free" class="level1">
                {foreach from=$free_layout item=free}
                <li gid="{$free.gid}">
                    <a {path_to_href_attribute path="groups/show/"|cat:$free.name}>{$free.long_name}</a>
                </li>
                {/foreach}
            </ul>
        </li>
        {/if}
        <li><a class="arrow" /><a {path_to_href_attribute path="contrib"}>Contribuer</a>
            <ul class="level1">
              <li>
                <a {path_to_href_attribute path="proposition/annonce"}>Proposer une annonce</a> 
              </li>
              <li>
                <a {path_to_href_attribute path="proposition/affiche"}>Proposer une activité</a>
              </li>
              <li>
                <a {path_to_href_attribute path="proposition/qdj"}>Proposer une qdj</a>
              </li>
              <li>
                <a {path_to_href_attribute path="proposition/sondage"}>Proposer un sondage</a>
              </li>
              <li>
                <a {path_to_href_attribute path="proposition/mail_promo"}>Demander un mail promo</a>
              </li>
            </ul>
        </li>
        <li><a href="xshare.php" accesskey="x">Télécharger</a></li>
        <li><a href="http://wikix.polytechnique.org" accesskey="w">WikiX</a></li>
        <li><a href="tol/binets/" accesskey="b">Binets</a></li>
        {if $smarty.session.auth >= AUTH_INTERNE }
        <li><a href="http://perso.frankiz/">Sites élèves</a></li>
        {else}
        <li><a href="siteseleves.php">Sites élèves</a></li>
        {/if}
        {if hasPerm('admin') }
        <li><a class="arrow" /><a {path_to_href_attribute path="gestion"} accesskey="g">Administration</a></li>
        {/if}
    </ul>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
