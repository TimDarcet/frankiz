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

    <div class="msg_proposal"> Merci d'avoir proposé une annonce. <br />
    Le responsable du groupe essayera de te la valider au plus tôt. </div>

{else}

<div class="info_proposal"> 
    Le texte de l'annonce utilise le format wiki décrit dans l'<a href="wiki_help">aide wiki</a><br/>
    Pour toute remarque particulière, envoyer un mail à <a href="mailto:web@frankiz.polytechnique.fr">web@frankiz</a> 
</div>

{if $msg}
    <div class="msg_proposal"> 
        {$msg}
    </div>
{/if}

<div class="box_proposal">
    <div class="title">
        Aperçu du corps de l'annonce
    </div>
    <div id="preview_proposal">
        {$content|miniwiki|smarty:nodefaults}
    </div>
</div>


<form enctype="multipart/form-data" method="post" action="proposal/news" id="form_mail_promo">
    <div class="news_proposal box_proposal">
        <div class="title">
           Annonce
        </div>
        <table>
            <tr>
                <td width=20%>
                    Destinataire :
                </td>
                <td>
                    {include file="groups_picker.tpl"|rel id="group_news_proposal" ns="binet" check=-1}
                </td>
            </tr>
            
            <tr>
                <td>
                    Groupe d'origine :
                </td>
                <td>
                    {include file="groups_picker.tpl"|rel id="origin_news_proposal" ns="binet" check=-1}
                </td>
            </tr>
            
            <tr>
                <td>
                    Titre :
                </td>
                <td>
                    <input type='text' name='title' value="{$title_news}" />
                </td>
            </tr>
    
            <tr>
                <td>
                    Contenu :
                </td>
                <td>
                    <textarea name='content' id="text_proposal" rows=30 cols=50>{$content}</textarea>
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
                    Dernier jour :
                </td>
                <td>
                    {valid_date name="end" value=$end to=7}
                </td>
            </tr>

            <tr>
                <td>
                    Commentaire :
                </td>
                <td>
                    <textarea name="comment" rows=7 cols=50>{$comment}</textarea>
                </td>
            </tr>
            
            <tr>
                <td></td>
                <td>
                    <input type="submit" name="send" value="Valider" onClick="return window.confirm('Voulez vous vraiment proposer cette annonce ?')"/>
                </td>
            </tr>
        
        </table>
    </div>
</form>

{/if}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}