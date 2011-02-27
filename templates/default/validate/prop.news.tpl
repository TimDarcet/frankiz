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

{if isset($envoye|smarty:nodefaults)}
    <div class="msg_proposal"> Merci d'avoir proposé une annonce. <br />
    Le responsable du groupe essayera de te la valider au plus tôt. </div>
{else}

{if t($msg)}
    <div class="msg_proposal"> 
        {$msg}
    </div>
{/if}

<form enctype="multipart/form-data" method="post" action="proposal/news" id="form_mail_promo">
    <div class="module news_proposal">
        <div class="head">
           Rédaction d'une annonce
        </div>
        <div class="body">
            <table>
                <tr>
                    <td>
                        J'écris au nom de:
                    </td>
                    <td>
                        {include file="origin_picker.tpl"|rel id="origin_news_proposal"}
                    </td>
                </tr>

                <tr>
                    <td width=20%>
                        Annonce visible par:
                    </td>
                    <td>
                        {include file="target_picker.tpl"|rel id="news" group_perso=false only_admin=false}
                    </td>
                </tr>

                <tr>
                    <td>
                        Titre :
                    </td>
                    <td>
                        <input type='text' required name='title' value="{$smarty.request.title}" placeholder="Titre de l'annonce" />
                    </td>
                </tr>

                <tr>
                    <td>
                        Image :
                    </td>
                    <td>
                        {include file="uploader.tpl"|rel id="image"}
                    </td>
                </tr>

                <tr>
                    <td>
                        Corps :
                    </td>
                    <td>
                        {include file="wiki_textarea.tpl"|rel id="news_content" already=$smarty.request.content placeholder="Corps de l'annonce" }
                    </td>
                </tr>

                <tr>
                    <td>
                        Visible
                    </td>
                    <td>
                        de <input type="text" name="begin" id="begin" value=""
                                  required {literal}pattern="(?=^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$).*"{/literal}/>
                        à  <input type="text" name="end" id="end" value=""
                                  required {literal}pattern="(?=^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$).*"{/literal}/>
                        <script>{literal}
                        $(function() {
                            var dates = $( "#begin, #end" ).datetimepicker({
                                minDate: new Date(), maxDate: "+7D"});
                            $("#begin").datetimepicker('setDate', new Date());
                            var end = new Date();
                            end.setDate(end.getDate() + 1);
                            $("#end").datetimepicker('setDate', end);
                        });
                        {/literal}</script>
                    </td>
                </tr>

                <tr>
                    <td>
                        Commentaire pour l'administrateur:
                    </td>
                    <td>
                        <textarea name="comment" rows=7 cols=50>{$smarty.request.comment}</textarea>
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
    </div>
</form>

{/if}

{js src="validate.js"}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}