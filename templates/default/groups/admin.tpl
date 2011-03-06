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

<div class="module admin_general">
    <div class="head">
        <span class="helper" target="groups/admin/general"> </span>
        {$group->label()} en bref
     </div>
     <div class="body">
        <form enctype="multipart/form-data" method="post" action="{$smarty.server.REQUEST_URI}">
        <table class="bicol">
            {if $smarty.session.user->isAdmin()}
                <tr class="fkzadmin">
                    <td>Nom "unix"</td>
                    <td>
                        <input type="text" name="name" value="{$group->name()}" />
                        <div class="warning">
                            /!\ Si tu ne sais pas ce que veut dire LDAP, PAM …<br />
                            Tu n'as pas envie de toucher à ce champ !<br />
                            (Et tu ne devrais pas avoir de droit d'admin sur le site)
                        </div>
                    </td>
                </tr>
            {/if}
            {if $smarty.session.user->isWeb()}
                <tr class="webmaster">
                    <td>Namespace</td>
                    <td>
                        <select name="ns">
                            {foreach from=$nss item='ns'}
                                <option value="{$ns}" {if $group->ns() == $ns}selected="selected"{/if}>{$ns}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            {/if}
            {if $smarty.session.user->isWeb()}
                <tr class="webmaster">
                    <td>Castes</td>
                    <td>
                        <ul>
                        {foreach from=$group->castes() item='caste'}
                            <li>
                                {$caste->rights()}&nbsp;: 
                                {include file="userfilter.tpl"|rel userfilter=$caste->userfilter()}
                            </li>
                        {/foreach}
                        </ul>
                    </td>
                </tr>
            {/if}
            <tr>
                <td>Nom</td>
                <td><input type="text" name="label" value="{$group->label()}" /></td>
            </tr>
            <tr class="pair">
                <td>Site Web</td>
                <td><input type="text" name="web" value="{$group->web()}" placeholder="http://" /></td>
            </tr>
            <tr>
                <td>E-Mail</td>
                <td><input type="text" name="mail" value="{$group->mail()}" /></td>
            </tr>
            <tr class="pair">
                <td>Description</td>
                <td>
                    {include file="wiki_textarea.tpl"|rel id="description" already=$group->description()|smarty:nodefaults}
                </td>
            </tr>
            {if $group->ns() != 'user'}
                <tr>
                    <td>Image</td>
                    <td><img src="{$group->image()|image:'small':'group'}" /> {include file="uploader.tpl"|rel id="image"}</td>
                </tr>
            {/if}
        </table>

        <input type="submit" name="update" value="Enregistrer">
        </form>
    </div>
</div>

<div class="module">
    <div class="head">
        <span class="helper" target="groups/admin/users"> </span>
        Membres de {$group->label()}
     </div>
     <div class="body admin_users">
        <div class="filters">
            Filtres:
            <form name="filters">
                <input type="hidden" id="gid" name="gid" value="{$group->id()}" />
                <input type="hidden" name="page" value="1" />
                <table>
                    <tr>
                        <td><label>Promo</label></td>
                        <td>{include file="groups_picker.tpl"|rel id="promo" ns="promo" check=-1 already=$user->defaultFilters()|filter:'ns':'promo' order="name"}</td>
                    </tr>
                    <tr>
                        <td><label>Droits</label></td>
                        <td><select name="rights">
                            <option value="everybody">Tous</option>
                            <option value="admin">Administrateurs</option>
                            <option value="member">Membres</option>
                            <option value="friend">Sympathisants</option>
                        </select></td>
                    </tr>
                    <tr>
                        <td><label>Nom</label></td>
                        <td><input type="text" name="name" value="" /></td>
                    </tr>
                </table>
            </form>
            <div class="pages"></div>
        </div>
        <table class="list">

        </table>
    </div>
</div>

{js src="group_admin.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
