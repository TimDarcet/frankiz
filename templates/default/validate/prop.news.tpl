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
            <span class="helper" target="proposal/news"> </span>
            Rédaction d'une annonce
        </div>
        <div class="body">
            {include file="wiki.tpl"|rel name='proposal/news'}
            <table class="bicol">
                <tr class="pair">
                    <td>
                        J'écris au nom de
                    </td>
                    <td>
                        {include file="origin_picker.tpl"|rel id="origin_news_proposal" not_only_admin=true}
                    </td>
                </tr>

                <tr>
                    <td width=20%>
                        Annonce visible par
                    </td>
                    <td>
                        <i>Attention à bien choisir le groupe de visibilité: ton annonce est-elle destinée à tous les "Polytechniciens", seulement la promotion "2014 Polytechniciens", ou encore les personnes présentes "Sur le plâtal" y compris les non-polytechniciens ? Un bon choix accélère la validation :-)</i><br>
                        {include file="target_picker.tpl"|rel id="news" group_perso=false only_admin=false even_only_friend=true}
                    </td>
                </tr>

                <tr class="pair">
                    <td>
                        Titre
                    </td>
                    <td>
                        <input type='text' required name='title' value="" placeholder="Titre de l'annonce" />
                    </td>
                </tr>

                <tr>
                    <td>
                        Image
                    </td>
                    <td>
                        {include file="uploader.tpl"|rel id="image"}
                    </td>
                </tr>

                <tr class="pair">
                    <td>
                        Corps
                    </td>
                    <td>
                        <i>ATTENTION: Évite les caractères spéciaux (smileys, etc.) car ils ne sont pas pris en charge.</i>
                        {include file="wiki_textarea.tpl"|rel id="news_content" placeholder="Corps de l'annonce" }
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
                            limit_inf = new Date();
                            limit_inf.setMinutes(0);
                            var dates = $( "#begin, #end" ).datetimepicker({
                                minDate: limit_inf, maxDate: "+7D"});
                            $("#begin").datetimepicker('setDate', new Date());
                            var end = new Date();
                            end.setDate(end.getDate() + 1);
                            $("#end").datetimepicker('setDate', end);
                        });
                        {/literal}</script>
                    </td>
                </tr>

                <tr class="pair">
                    <td>
                        Commentaire pour l'administrateur
                    </td>
                    <td>
			<i>Profite de ce champ pour donner aux administrateurs les informations dont ils pourraient avoir besoin et qu'ils ignorent peut-être, cela accélèrera le traitement de ta demande.</i><br>
                        <textarea name="comment" rows=7 cols=50></textarea>
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
