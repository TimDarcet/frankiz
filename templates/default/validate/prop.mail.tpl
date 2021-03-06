{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010-2013 Binet Réseau                                  *}
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

{js src="validate.js"}

{if isset($envoye|smarty:nodefaults)}

    <div class="msg_proposal"> Merci d'avoir proposé un mail. 
    Le responsable du groupe essayera de te le valider au plus tôt. </div>

{else}

<form enctype="multipart/form-data" method="post" action="proposal/mail/" id="form_mail_promo">
    {if t($msg)}
        <div class="msg_proposal"> 
            {$msg}
        </div>
    {/if}
        
    <div class="module mail">
        <div class="head">
            <span class="helper" target="proposal/mail"></span>
            Mail
        </div>
        <div class="body">
            <table class="bicol">
                <tr class="pair">
                    <td>
                        J'écris au nom de
                    </td>
                    <td>
                        {include file="origin_picker.tpl"|rel id="origin_mail_proposal"}
                    </td>
                </tr>

                <tr>
                    <td width=20%>
                        Destinataire :
                    </td>
                    <td>
                        <label>
                            <input id="choice_promo" type="radio" name="type_mail_proposal" checked value="promo">
                            Mail à une promo
                        </label><br />
                        <label>
                            <input id="choice_group" type="radio" name="type_mail_proposal" value="group">
                            Mail aux membres sur le plâtal d'un groupe
                        </label><br />
                        <div id="promo_proposal" class="type_proposal">
                            <select name="promos[]" multiple="1">
                                {foreach from=$formations item=form}
                                    {foreach from=$form->platalyears() item=promo}
                                        <option value="{$form->id()}_{$promo}">
                                            {$form->label()} {$promo}
                                        </option>
                                    {/foreach}
                                {/foreach}
                            </select>
                            (Tu peux sélectionner plusieurs promotions avec la touche Ctrl)
                        </div>
                        <div id="group_proposal" class="type_proposal">
                            {include file="target_picker.tpl"|rel id="mail" group_perso=false only_admin=true no_friendbox=true}
                            <script>
                                $("#group_proposal .comments").hide();
                            </script>
                        </div>
                    </td>
                </tr>

                <tr class="pair">
                    <td>
                        Sujet :
                    </td>
                    <td>
                        <input type='text' name='subject' value="{$subject}" placeholder="Sujet du mail"/>
                    </td>
                </tr>

                <tr>
                    <td>
                        Mail :
                    </td>
                    <td>
                         {include file="wiki_textarea.tpl"|rel id="mail_body" already=$body placeholder="Corps du mail" }
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="send" value="Envoyer" onClick="return window.confirm('Voulez vous vraiment envoyer ce mail ?')"/>
                    </td>
                </tr>

            </table>
        </div>
    </div>
</form>
{/if}
    

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
