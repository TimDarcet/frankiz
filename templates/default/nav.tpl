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

<table><tbody>
<tr>
    <td>
        <a {path_to_href_attribute path="accueil"} accesskey="a">Accueil</a>
        <div class="subnav subnavwithoutjs">
            <div class="bkg" />
            <table class="body" style="height: 90px; font-size: 30px;">
                <tr>
                    <td><a {path_to_href_attribute path="accueil/annonces"}>Annonces</a></td>
                    <td><a {path_to_href_attribute path="accueil/activites"}>Activités</a></td>
                    <td><a {path_to_href_attribute path="newspaper/ik"}>InfoKès</a></td>
                </tr>
            </table>
        </div>
    </td>

    <td>
        <a {path_to_href_attribute path="profile"} accesskey="c">Compte</a>
        <div id="subnav_account" class="subnav subnavwithoutjs" style="font-size: 26px;">
            <div class="bkg" />
            <div class="body">
                <div>
                {if isset($smarty.session.suid|smarty:nodefaults) }
                    <span class="warning">ATTENTION, su en cours. Pour revenir à ta vraie identité, clique <a href="exit/">ici</a></span>
                {/if}
                {if $smarty.session.auth < AUTH_COOKIE }
                    <a {path_to_href_attribute path="login"} nosolo="true" accesskey="l">Se connecter</a>
                {/if}
                {if $smarty.session.auth >= AUTH_COOKIE }
                    <a {path_to_href_attribute path="exit"} nosolo="true" accesskey="l">Se déconnecter de {$smarty.session.user->displayName() }</a>
                {/if}
                </div>
                <table>
                    <tr>
                        {if $smarty.session.auth >= AUTH_COOKIE }
                            <td><a {path_to_href_attribute path="profile/fkz"}>Profil</a></td>
                            <td><a {path_to_href_attribute path="profile/minimodules"}>Minimodules</a></td>
                        {/if}
                        <td><a {path_to_href_attribute path="profile/skin"}>Skin</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </td>

    {if $smarty.session.auth >= AUTH_INTERNE }
        <td><a {path_to_href_attribute path="tol"} accesskey="t">Trombino</a></td>
    {/if}

    {if $smarty.session.auth >= AUTH_COOKIE }
        <td>
            <a {path_to_href_attribute path="groups"}>Binets</a>
        </td>
    {/if}

    <td><a {path_to_href_attribute path="laf"} accesskey="o">Objets trouvés</a></td>

    <td>
        <a {path_to_href_attribute path="contrib"}>Contribuer</a>
        <div class="subnav subnavwithoutjs">
            <div class="bkg" />
            <div class="body">
                Proposer :
                <table>
                    <tr>
                        <td><a {path_to_href_attribute path="proposition/annonce"}>une annonce</a></td>
                        <td><a {path_to_href_attribute path="proposition/annonce"}>un événement</a></td>
                        <td><a {path_to_href_attribute path="proposition/annonce"}>une QDJ</a></td>
                    </tr>
                    <tr>
                        <td><a {path_to_href_attribute path="proposition/annonce"}>un sondage</a></td>
                        <td><a {path_to_href_attribute path="proposition/annonce"}>un mail promo</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </td>

    <td><a href="xshare.php" accesskey="x">Télécharger</a></td>
    <td><a href="http://wikix.polytechnique.org" accesskey="w">WikiX</a></td>
    {if hasPerm('admin') }
        <td>
            <a {path_to_href_attribute path="admin"} accesskey="g">Administration</a>
            <div class="subnav subnavwithoutjs">
                <div class="bkg" />
                <table class="body">
                    <tr>
                        <td><a {path_to_href_attribute path="admin/groups"}>Groupes</a></td>
                        <td><a {path_to_href_attribute path="admin/images"}>Images</a></td>
                        <td><a {path_to_href_attribute path="admin/validate"}>Validations</a></td>
                    </tr>
                </table>
            </div>
        </td>
    {/if}
</tr>
</tbody></table>

{literal}
<script>

</script>
{/literal}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
