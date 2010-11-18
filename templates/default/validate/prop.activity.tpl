{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010 Binet Réseau                                       *}
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

{js src="validate.js"}

{if isset($envoye|smarty:nodefaults)}

    <div class="msg_proposal"> Merci d'avoir proposé une activité. <br />
    Le responsable du groupe essayera de te la valider au plus tôt. </div>

{else}

<div class="info_proposal"> 
    Pour toute remarque particulière, envoyer un mail à <a href="mailto:web@frankiz.polytechnique.fr">web@frankiz</a> 
</div>

{if $msg}
    <div class="msg_proposal"> 
        {$msg}
    </div>
{/if}

<form enctype="multipart/form-data" method="post" action="proposal/activity" id="form_mail_promo">
    <div class="news_proposal box_proposal">
        <div class="title">
           Activité
        </div>
        <table>
            <tr>
                <td width=20%>
                    Destinataire :
                </td>
                <td>
                    {include file="groups_picker.tpl"|rel id="group_activity_proposal" ns="binet" check=-1}
                </td>
            </tr>
            
            <tr>
                <td>
                    Titre :
                </td>
                <td>
                    <input type='text' name='title' value="{$title_activity}" />
                </td>
            </tr>
    
            <tr>
                <td>
                    Description :
                </td>
                <td>
                    <textarea name='desc' id="text_proposal" rows=7 cols=50>{$desc}</textarea>
                </td>
            </tr>
            
            <tr>
                <td>
                    Image :
                </td>
                <td>
                    A faire quand les classes de Riton seront pretes
                </td>
            </tr>
            
            <tr>
                <td>
                    Date :
                </td>
                <td>
                    {valid_date name="date" value=$date to=7}
                </td>
            </tr>
            
            <tr>
                <td>
                    Toutes les semaines :
                </td>
                <td>
                    <input type="checkbox" name="regular" id="regular_activity_proposal" {if $regular}checked="checked"{/if}/>
                </td>
            </tr>
            
            <tr id="number_activity_proposal">
                <td>
                    Combien de fois :
                </td>
                <td>
                    <select name='number'>
                        {section name=nbr start=1 loop=6}
                            <option value={$smarty.section.nbr.index} {if $smarty.section.nbr.index==$number} SELECTED {/if}>
                                {$smarty.section.nbr.index} semaines
                            </option>
                        {/section}
                    </select>
                </td>
            </tr>
            
            <tr>
                <td>
                    Heure de début :
                </td>
                <td>
                    <input type='text' name='begin' value="{$begin}" />
                </td>
            </tr>
            
            <tr>
                <td>
                    Heure de fin :
                </td>
                <td>
                    <input type='text' name='end' value="{$end}" />
                </td>
            </tr>
            
            <tr>
                <td></td>
                <td>
                    <input type="submit" name="send" value="Valider" onClick="return window.confirm('Voulez vous vraiment proposer cette activité ?')"/>
                </td>
            </tr>
        
        </table>
    </div>
</form>

{/if}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}