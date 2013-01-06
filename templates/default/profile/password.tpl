{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009-2013 Binet Réseau                                  *}
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

{if $msg}
    {foreach from=$msg item=message}
        <div class="msg">
            {$message|smarty:nodefaults}
        </div>
    {/foreach}
{/if}

{if $err}
    {foreach from=$err item=error}
        <div class="error">
            {$error|smarty:nodefaults}
        </div>
    {/foreach}
{/if}

<form enctype="multipart/form-data" method="post" action="profile/password" class="profile">
    <div class="module password">
        <div class="head">
            <span class="helper" target="profile/password"></span>
            Changement du mot de passe
        </div>
        <div class="body">
            <table>
                <tr>
                    <td width="22%">
                        Mot de passe&nbsp;:
                    </td>
                    <td class="form val">
                        <input id="p1" type='password' name='new_passwd1' required {literal}pattern="(?=^.{6,}$).*"{/literal}
                               autocomplete="off"  title="Le mot de passe doit faire au moins 6 caractères."/>
                        <div class="validation">
                            minimum de 6 caractères.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        Retaper le mot de passe&nbsp;:
                    </td>
                    <td class="form val">
                        <input type='password' name='new_passwd2' required {literal}pattern="(?=^.{6,}$).*"{/literal}
                               autocomplete="off" title="Le mot de passe doit faire au moins 6 caractères."/>
                    </td>
                </tr>
            </table>

            <input type="submit" name="new_passwd" value="Enregistrer">
        </div>
    </div>
</form>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
