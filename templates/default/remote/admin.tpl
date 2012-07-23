{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2012 Binet Réseau                                       *}
{*  http://br.binets.fr/                                                  *}
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

<form enctype="multipart/form-data" method="post" action="remote/admin/{$remote->id()}" class="profile">
    <div class="module">
        <div class="head">
            Authentification externe
        </div>
        <div class="body">
            <a href="remote/admin">Retour à la liste</a>
            <table>
                <tr>
                    <td width="20%">Site (URI de retour) :</td>
                    <td><input type='text' name='site' value="{$remote->site()}" /></td>
                </tr>
                <tr>
                    <td>Nom complet :</td>
                    <td><input type='text' name='label' value="{$remote->label()}" /></td>
                </tr>
                <tr>
                    <td>Clé privée :</td>
                    <td><input type='text' name='privkey' value="{$remote->privkey()}" /></td>
                </tr>
                <tr>
                    <td>Permissions :</td>
                    <td>
                        {assign var='remrights' value=$remote->rights()}
                        <input type='text' name='rights' value="{$remrights->flags()}" /><br />
                        Valeurs autorisées : {$remoterights_available}
                        {include file="wiki.tpl"|rel name='remote/rights'}
                    </td>
                </tr>
                <tr>
                    <td>Binets associés :</td>
                    <td>
                        {include file="groups_picker.tpl"|rel id="binets" ns="binet" check=-1 already=$remote->groups()|filter:'ns':'binet'}
                    </td>
                </tr>
                <tr>
                    <td>Groupes associés :</td>
                    <td>
                        {include file="groups_picker.tpl"|rel id="frees" ns="free" check=-1 already=$remote->groups()|filter:'ns':'free'}
                    </td>
                </tr>
            </table>

            <input type="submit" name="change_remote" value="Enregistrer">
            <p>
                <a href="remote/admin/{$remote->id()}/delete">
                    <div class="remove_element"></div>
                    Supprimer
                </a>
            </p>
        </div>
    </div>
</form>
