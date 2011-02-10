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

{if t($msg)}
    {foreach from=$msg item=message}
        <div class="msg">
            {$message|smarty:nodefaults}
        </div>
    {/foreach}
{/if}

{if t($err)}
    {foreach from=$err item=error}
        <div class="error">
            {$error|smarty:nodefaults}
        </div>
    {/foreach}
{/if}

<form enctype="multipart/form-data" method="post" action="profile/account" class="profile">
    <div class="module password">
        <div class="head">
            Changement du mot de passe
            <span class="helper" target="profile/account/password" />
        </div>
        <div class="body">
            <table>
                <tr>
                    <td width="20%">
                        Mot de passe :
                    </td>
                    <td class="form val">
                        <input id="p1" type='password' name='new_passwd1' required {literal}pattern="(?=^.{6,}$).*"{/literal}
                            title="Le mot de passe doit faire au moins 6 caractères."/>
                        <div class="validation">
                            minimum de 6 caractères.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        Retaper le mot de passe :
                    </td>
                    <td class="form val">
                        <input type='password' name='new_passwd2' required {literal}pattern="(?=^.{6,}$).*"{/literal}
                            title="Le mot de passe doit faire au moins 6 caractères."/>
                    </td>
                </tr>
            </table>

            <input type="submit" name="new_passwd" value="Enregistrer">
        </div>
    </div>


    <div class="module profile">
        <div class="head">
            Changement de la fiche trombino
            <span class="helper" target="profile/account/trombino" />
        </div>
        <div class="body">
            <table>
                <tr>
                    <td width="20%">
                        Nom :
                    </td>
                    <td>
                        {$user->fullName()}
                    </td>
                </tr>
                <tr>
                    <td>
                        Kasert :
                    </td>
                    <td>
                        <ul>
                        {foreach from=$user->rooms() item='room'}
                            <li>{$room->id()}</li>
                        {/foreach}
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>
                        Surnom :
                    </td>
                    <td class="form">
                        <input type='text' name='nickname' value="{$user->nickname()}" placeholder="Ton surnom"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Email :
                    </td>
                    <td class="form val">
                        <input type='email' name='bestalias' value="{$user->bestEmail()}" placeholder="Ton adresse mail"/>
                        <div class="validation">
                            email invalide
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        Portable :
                    </td>
                    <td class="form">
                        <input type='text' name='cellphone' value="{$user->cellphone()}"
                            placeholder="Ton portable" {literal}pattern="[ 0-9]*"{/literal}/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Format des mails :
                    </td>
                    <td>
                        <input type="radio" name="format" value="text" {if !$user->isEmailFormatHtml()}checked{/if}/> texte pur<br />
                        <input type="radio" name="format" value="html" {if $user->isEmailFormatHtml()}checked{/if}/> html
                    </td>
                </tr>
                <tr>
                    <td>
                        Commentaire :
                    </td>
                    <td class="form">
                        <textarea name='comment' placeholder="Commentaire personnel" rows=7 cols=50>{$user->comment()}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        Changer la photo tol :
                    </td>
                    <td class="form">
                        {include file="uploader.tpl"|rel id="image"}
                    </td>
                </tr>
            </table>

            <input type="submit" name="change_profile" value="Changer">
        </div>
    </div>
</form>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
