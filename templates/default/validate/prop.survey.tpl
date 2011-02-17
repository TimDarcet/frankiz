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

    TODO

{else}

    <form enctype="multipart/form-data" method="post" action="proposal/survey">
    <div class="module survey_proposal">
        <div class="head">
           Création d'un sondage
        </div>
        <div class="body">
            <table>
                <tr>
                    <td>
                        J'écris au nom de:
                    </td>
                    <td>
                        {include file="origin_picker.tpl"|rel id="origin_survey_proposal"}
                    </td>
                </tr>

                <tr>
                    <td>
                        Annonce visible par:
                    </td>
                    <td>
                        {include file="target_picker.tpl"|rel id="survey" group_perso=false only_admin=false}
                    </td>
                </tr>

                <tr>
                    <td>
                        Titre :
                    </td>
                    <td>
                        <input type="text" required name="title" value="{$smarty.request.title}" placeholder="Titre du sondage" />
                    </td>
                </tr>

                <tr>
                    <td>Description:</td>
                    <td>
                        <textarea name="content">{$content}</textarea>
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
                            $("#begin").datetimepicker({minDate: new Date(), maxDate: "+7D" });
                            $("#begin").datetimepicker('setDate', new Date());
                            var end = new Date();
                            end.setDate(end.getDate() + 1);
                            $("#end").datetimepicker({ minDate: new Date(), maxDate: "+7D", defaultDate: end });
                            $("#end").datetimepicker('setDate', end);
                        });
                        {/literal}</script>
                    </td>
                </tr>

                <tr>
                    <td>Commentaire:</td>
                    <td>
                        <textarea name="comment">{$smarty.request.comment}</textarea>
                    </td>
                </tr>
            </table>

            <hr />

            <ul id="questions">
            
            </ul>
            <select id="add_question_type">
                <option value="text">Textuelle</option>
                <option value="choices">À choix</option>
            </select>
            <input id="add_question" name="add_question" value="Ajouter" type="button" />
            <div id="questions_sources" >
                <div class="question questiontext impair">
                    <div class="nb">1.</div>
                    <div class="rm">Supprimer</div>
                    <table>
                        <tr>
                            <td>Question</td>
                            <td><input type="text" name="label" value="" placeholder="Titre de la question"></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><textarea name="description" placeholder="Description de la question (si nécessaire)"></textarea></td>
                        </tr>
                        <tr>
                            <td>Obligatoire ?</td>
                            <td><input type="checkbox" name="mandatory" /></td>
                        </tr>
                    </table>
                </div>
                <div class="question questionchoices pair">
                    <div class="nb">2.</div>
                    <div class="rm">Supprimer</div>
                    <table>
                        <tr>
                            <td>Question</td>
                            <td><input type="text" name="label" class="label" value="" placeholder="Titre de la question"></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><textarea name="description" placeholder="Description de la question (si nécessaire)"></textarea></td>
                        </tr>
                        <tr>
                            <td>Obligatoire ?</td>
                            <td><input type="checkbox" name="mandatory" /></td>
                        </tr>
                        <tr>
                            <td>Maximum de réponses</td>
                            <td>
                                <select name="max">
                                    <option value="1">1</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Réponses</td>
                            <td>
                                <ul class="choices">
                                    <li><input type="text" value="" placeholder="Choix" /><input type="button" class="rm_choice" /></li>
                                </ul>
                                <input type="button" class="add_choice" value="" />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <input type="button" id="send" name="send" value="Créer le sondage" />
        </div>
    </div>
    </form>

    {js src="prop.survey.js"}

{/if}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
