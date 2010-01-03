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

<div class="fkz_module" id="liensnavigation">
    <div class="fkz_titre">
        <span id="liensnavigation_logo"></span>
        Navigation
    </div>
    <div class="fkz_module_corps">
       <ul class="fkz_liens">
            <li class="fkz_liens"><a href="accueil/" accesskey="0">Accueil</a></li>
            <li>
                {if isset($smarty.session.suid|smarty:nodefaults) }
                <span class='warning'>ATTENTION, su en cours. Pour revenir à ta vraie identité, clique <a href="exit/">ici</a></span>
                {/if}
                <ul class="fkz_liens">
                {if $smarty.session.auth < AUTH_COOKIE }
                <li class="fkz_liens"><a href="login/" accesskey="l">Se connecter</a></li>
                {/if}
                
                {if $smarty.session.auth >= AUTH_COOKIE }
                Bienvenue {$smarty.session.user->displayName() }.
                <li class="fkz_liens"><a href="profil/" accesskey="p">Préférences</a></li>
                <li class="fkz_liens"><a href="exit/" accesskey="l">Se déconnecter</a></li>
                {/if}
                </ul>  
            </li>
            <li class="fkz_liens"><a href="annonces/" accesskey="a">Annonces</a></li>
            <li class="fkz_liens"><a href="activites/">Activités</a></li>
            {if $smarty.session.auth >= AUTH_INTERNE }
            <li class="fkz_liens"><a href="tol/" accesskey="t">Trombino</a></li>
            {/if}
            <li class="fkz_liens"><a href="xshare.php" accesskey="x">Télécharger</a></li>
            <li class="fkz_liens"><a href="http://wikix.polytechnique.org" accesskey="w">WikiX</a></li>
            <li class="fkz_liens"><a href="tol/binets/" accesskey="b">Binets</a></li>
            {if $smarty.session.auth >= AUTH_INTERNE }
            <li class="fkz_liens"><a href="http://perso.frankiz/">Sites élèves</a></li>
            {else}
            <li class="fkz_liens"><a href="siteseleves.php">Sites élèves</a></li>
            {/if}
            {if hasPerm('admin') }
                <li class="fkz_liens"><a href="gestion/" accesskey="g">Administration</a></li>
            {/if}
        </ul>
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
