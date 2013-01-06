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

<form enctype="multipart/form-data" method="post" action="profile/admin/account/{if !$add}{$userEdit->hruid()}{/if}" class="profile">
    <div class="module profile">
        <div class="head">
            <span class="helper" target="profile/admin/account"></span>
            {if !$add}Fiche trombino de {$userEdit|user:text}{/if}
            {if $add}Ajout d'un utilisateur{/if}
        </div>
        <div class="body">
            <table>
                {if $logged && $user->isAdmin()}
                    <tr class="fkzadmin">
                        <td width="20%">
                            Hruid :
                        </td>
                        <td class="form">
                            <input type='text' name='hruid' 
			           {if !$add}value="{$userEdit->hruid()}" {/if}
			           {if $add}placeholder="Optionnel"{/if}
		            />
                            <div class="warning">
                                /!\ Si tu ne sais pas ce que veut dire LDAP, PAM …<br />
                                Tu n'as pas envie de toucher à ce champ !<br />
                                (Et tu ne devrais pas avoir de droit d'admin sur le site)
                            </div>
                        </td>
                    </tr>
                {/if}
                <tr>
                    <td>
                        Surnom :
                    </td>
                    <td class="form">
                        <input type='text' name='nickname' {if !$add}value="{$userEdit->nickname()}" {/if}placeholder="Ton surnom"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Nom :
                    </td>
                    <td class="form">
                        <input type='text' name='lastname' {if !$add}value="{$userEdit->lastname()}" {/if}/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Prénom :
                    </td>
                    <td class="form">
                        <input type='text' name='firstname' {if !$add}value="{$userEdit->firstname()}" {/if}/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Anniversaire :
                    </td>
                    <td class="form">
                        {assign var='b' value=$userEdit->birthdate()}
                        <input type='text' name='birthdate' id='birthdate' {if !$add}value="{$b->format("Y-m-d")}" {/if}/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Sexe :
                    </td>
                    <td class="form">
                        <select name='gender'>
                            <option value="man" {if !$add}{if ($userEdit->gender() == 'man')}selected="selected" {/if}{/if}>Homme</option>
                            <option value="woman" {if !$add}{if ($userEdit->gender() == 'woman')}selected="selected" {/if}{/if}>Femme</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        Email :
                    </td>
                    <td class="form val">
                        <input type='email' name='bestalias' {if !$add}value="{$userEdit->bestEmail()}" {/if}placeholder="Ton adresse mail"/>
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
                        <input type='text' name='cellphone' {if !$add}value="{$userEdit->cellphone()}"{/if}
                            placeholder="Ton portable" {literal}pattern="[ 0-9]*"{/literal}/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Skin :
                    </td>
                    <td class="form">
                        <input type='text' name='skin' {if !$add}value="{$userEdit->skin()}"{/if} {if $add}value="default"{/if}
                            />
                    </td>
                </tr>
                <tr>
                    <td>
                        Format des mails :
                    </td>
                    <td>
                        <input type="radio" name="format" value="text" {if !$add}{if !$userEdit->isEmailFormatHtml()}checked{/if}{/if}/> texte pur<br />
                        <input type="radio" name="format" value="html" {if !$add}{if $userEdit->isEmailFormatHtml()}checked{/if}{/if}/> html
                    </td>
                </tr>
                <tr>
                    <td>
                        Commentaire :
                    </td>
                    <td class="form">
                        <textarea name='comment' placeholder="Commentaire personnel" rows=7 cols=50>{if !$add}{$userEdit->comment()}{/if}</textarea>
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
                <tr>
                    <td>
                        Nouveau mot de passe :
                    </td>
                    <td class="form">
                        <input type="password" name="password" placeholder="Conserver l'ancien" />
                    </td>
                </tr>
                {if $add}
                <tr>
                    <td width="20%">
                        <label for="addCommonMinimodules">Copier les minimodules :</label>
                    </td>
                    <td class="form">
                        <input type='checkbox' name='addCommonMinimodules' id="addCommonMinimodules" checked="checked"/>
                    </td>
                </tr>
                {/if}
            </table>

            <input type="submit" name="change_profile" value="Enregistrer" {if $add}onclick="return confirm(areyousure);"{/if} />
        </div>
    </div>
</form>

{if !$add}
{if $logged && $user->isAdmin()}
<div class="module profile">
    <div class="head">
        <span class="helper" target="profile/admin/account/perms"></span>
        Permissions
    </div>
    <div class="body fkzadmin">
        <table>
        {foreach from=$perms item='perm'}
            <tr>
                <td width="20%">
                    {$perm} :
                </td>
                <td>
                    <form enctype="multipart/form-data" method="post" action="profile/admin/account/{$userEdit->hruid()}" class="profile">
                        <input type="hidden" name="perm" value="{$perm}" />
                        {if ($userEdit->checkPerms($perm))} oui <input type="submit" name="del_perm" value="Retirer" />{/if}
                        {if (!$userEdit->checkPerms($perm))} non <input type="submit" name="add_perm" value="Ajouter" />{/if}
                    </form>
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>
{/if}

<div class="module profile">
    <div class="head">
        <span class="helper" target="profile/admin/account/studies"></span>
        Formations
    </div>
    <div class="body">
        <table>
                <tr>
                    <th width="10%">Formation</th>
                    <th width="10%">Entrée</th>
                    <th width="10%">Sortie</th>
                    <th width="10%">Promo</th>
                    <th>forlife</th>
                    <th></th>
                    <th></th>
                </tr>
        {foreach from=$userEdit->studies(true) item='study'}
            <form enctype="multipart/form-data" method="post" action="profile/admin/account/{$userEdit->hruid()}" class="profile">
                <tr>
                    <td>
                        {assign var='formation' value=$study->formation()}
                        {$formation->label()}
                    </td>
                    <td>
                        <input type="text" name="year_in" value="{$study->year_in()}" style="width:50px;" />
                    </td>
                    <td>
                        <input type="text" name="year_out" value="{$study->year_out()}" style="width:50px;" />
                    </td>
                    <td>
                        <input type="text" name="promo" value="{$study->promo()}" style="width:50px;" />
                    </td>
                    <td>
                        {$study->forlife()}
                    </td>
                    <td>
                        <input type="hidden" name="forlife" value="{$study->forlife()}" />
                        <input type="hidden" name="formation_id" value="{$formation->id()}" />
                        <input type="submit" name="upd_study" value="Modifier" />          
                    </td>
                    <td>
                        <input type="submit" name="del_study" value="Supprimer" onclick="return confirm(areyousure);" />          
                    </td>
                </tr>
            </form>
        {/foreach}
           <form enctype="multipart/form-data" method="post" action="profile/admin/account/{$userEdit->hruid()}" class="profile">
                <tr>
                    <td>
                        <select  name="formation_id">
                        {foreach from=$formations item='f'}
                            <option value="{$f.formation_id}" >{$f.label}</option>
                        {/foreach}
                        </select>
                    </td>
                    <td>
                        <input type="text" name="year_in" style="width:50px;" />
                    </td>
                    <td>
                        <input type="text" name="year_out" style="width:50px;" />
                    </td>
                    <td>
                        <input type="text" name="promo" style="width:50px;" />
                    </td>
                    <td>
                        <input type="text" name="forlife" />
                    </td>
                    <td>
                        <input type="submit" name="add_study" value="Ajouter" />          
                    </td>
                </tr>
            </form>
        </table>
    </div>
</div>

<div class="module profile">
    <div class="head">
        <span class="helper" target="profile/admin/account/kasert"></span>
        Chambres
    </div>
    <div class="body">
        <table>
        {foreach from=$userEdit->rooms() item='room'}
            <form enctype="multipart/form-data" method="post" action="profile/admin/account/{$userEdit->hruid()}" class="profile">
                <tr>
                    <td width="20%">
                        <input type="hidden" name="rid" value="{$room->id()}" />
                        {$room->id()} ({$room|room})
                    </td>
                    <td>                                
                        <input type="submit" name="del_room" value="Supprimer" onclick="return confirm(areyousure);" />
                    </td>
                </tr>
            </form>
        {/foreach}
            <form enctype="multipart/form-data" method="post" action="profile/admin/account/{$userEdit->hruid()}" class="profile">
                <tr>
                    <td width="20%">
                        <input type="text" name="rid" />
                    </td>
                    <td>                                
                        <input type="submit" name="add_room" value="Ajouter" />
                    </td>
                </tr>
            </form>
        </table>
    </div>
</div>

<div class="module profile">
    <div class="head">
        <span class="helper" target="profile/admin/account/nationalities"></span>
        Nationalités
    </div>
    <div class="body">
        <table>
        {foreach from=$user_nationalities item='nationality'}
            <form enctype="multipart/form-data" method="post" action="profile/admin/account/{$userEdit->hruid()}" class="profile">
                <tr>
                    <td width="20%">
                        <input type="hidden" name="name" value="{$nationality->name()}" />
                        {$nationality->label()}
                    </td>
                    <td>                                
                        <input type="submit" name="del_group" value="Supprimer" onclick="return confirm(areyousure);" />
                    </td>
                </tr>
            </form>
        {/foreach}
            <form enctype="multipart/form-data" method="post" action="profile/admin/account/{$userEdit->hruid()}" class="profile">
                <tr>
                    <td width="20%">
                        <select name="name">
                            {foreach from=$nationalities item='nationality'}
                                <option value="{$nationality->name()}">{$nationality->label()}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td>                                
                        <input type="submit" name="add_group" value="Ajouter" />
                    </td>
                </tr>
            </form>
        </table>
    </div>
</div>

<div class="module profile">
    <div class="head">
        <span class="helper" target="profile/admin/account/sports"></span>
        Sports
    </div>
    <div class="body">
        <table>
        {foreach from=$user_sports item='sport'}
            <form enctype="multipart/form-data" method="post" action="profile/admin/account/{$userEdit->hruid()}" class="profile">
                <tr>
                    <td width="20%">
                        <input type="hidden" name="name" value="{$sport->name()}" />
                        {$sport->label()}
                    </td>
                    <td>                                
                        <input type="submit" name="del_group" value="Supprimer" onclick="return confirm(areyousure);" />
                    </td>
                </tr>
            </form>
        {/foreach}
            <form enctype="multipart/form-data" method="post" action="profile/admin/account/{$userEdit->hruid()}" class="profile">
                <tr>
                    <td width="20%">
                        <select name="name">
                            {foreach from=$sports item='sport'}
                                <option value="{$sport->name()}">{$sport->label()}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td>                                
                        <input type="submit" name="add_group" value="Ajouter" />
                    </td>
                </tr>
            </form>
        </table>
    </div>
</div>
{/if}
{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
