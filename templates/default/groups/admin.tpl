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
        {$group->label()} en bref
        <span class="helper" target="groups/admin/general" />
     </div>
     <div class="body">
        <form enctype="multipart/form-data" method="post" action="{$smarty.server.REQUEST_URI}">
        <table>
            {assign var='perms' value=$smarty.session.user->perms()}
            {if $perms->hasFlag('admin')}
                <tr>
                    <td>Nom "unix":</td>
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
            <tr>
                <td>Nom:</td>
                <td><input type="text" name="label" value="{$group->label()}" /></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td>
                    <div id="display">
                        {$group->description()|miniwiki:'title'|smarty:nodefaults}
                    </div>
                    <textarea name="description" class="description" id="description">{$group->description()}</textarea>
                    <script>
                        wiki_preview.start($("#description"), $("#display"));
                    </script>
                </td>
            </tr>
            <tr>
                <td>Image:</td>
                <td><img src="{$group->image()|image:'small':'group'}" /> {include file="uploader.tpl"|rel id="image"}</td>
            </tr>
        </table>

        <input type="submit" name="update" value="Enregistrer">
        </form>
    </div>
</div>

<div class="module admin_users">
    <div class="head">
        Membres de {$group->label()}
        <span class="helper" target="groups/admin/users" />
     </div>
     <div class="body">
        <table>
            <tr>
                <td class="users">
                    {*include file="users_picker.tpl"|rel id="users_picker" group=$group filters='["promo"]'*}
                </td>
                <td>
                    <select name="caste" onchange="">
                    {foreach from=$group->caste() item='caste'}
                        <option value="{$caste->id()}">{$caste->rights()}</option>
                    {/foreach}
                    </select>
                    <div id="caste_users">

                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

{js src="groups.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
