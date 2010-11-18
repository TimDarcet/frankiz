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

    <div class="msg_proposal"> Merci d'avoir proposé un mail commun. 
    Le responsable du groupe essayera de te le valider au plus tôt. </div>

{else}

<form enctype="multipart/form-data" method="post" action="proposal/mail/" id="form_mail_promo">

    <div class="info_proposal"> 
        Le texte du mail promo utilise le format wiki décrit dans l'<a href="wiki_help">aide wiki</a><br/>
        Pour toute remarque particulière, envoyer un mail à <a href="mailto:web@frankiz.polytechnique.fr">web@frankiz</a>      
        <div class="small">
            <label><input type="checkbox" name="no_wiki" id="box_mail_proposal" value="1" {if $nowiki}checked="checked"{/if} onchange="update_mail();" />
            coche cette case pour envoyer l'email en texte brut, sans formattage</label>
        </div> 
    </div>

    {if $msg}
        <div class="msg_proposal"> 
            {$msg}
        </div>
    {/if}

    <div class="box_proposal">
        <div class="title">
            Aperçu du corps de l'email
        </div>
        <div id="preview_proposal">
            {$body|miniwiki|smarty:nodefaults}
        </div>
    </div>
        
        
    <div class="mail_proposal box_proposal">
        <div class="title">
           Mail
        </div>
        <table>
            <tr>
                <td width=20%>
                    Destinataire :
                </td>
                <td>
                    {include file="groups_picker.tpl"|rel id="group_mail_proposal" ns="study" check=-1}
                </td>
            </tr>
            
            <tr>
                <td>
                    Sujet :
                </td>
                <td>
                    <input type='text' name='subject' value="{$subject}" />
                </td>
            </tr>
    
            <tr>
                <td>
                    Mail :
                </td>
                <td>
                    <textarea name='body' id="text_proposal" rows=30 cols=50>{$body}</textarea>
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
</form>
{/if}
    

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
