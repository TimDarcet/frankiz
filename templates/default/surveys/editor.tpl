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

<div class="survey_editor" id="survey_editor_{$id}">
    <input type="hidden" id="{$id}" name="{$id}" value="" />

    <ul class="questions">
    </ul>

    <select id="add_question_type">
        <option value="text">Textuelle</option>
        <option value="choices">À choix</option>
    </select>

    <input sname="add_question" value="Ajouter" type="button" />


    <div class="questions_sources">
        <div class="question questiontext impair">
            <div class="nb">1.</div>
            <div class="rm">Supprimer</div>
            <table>
                <tr>
                    <td>Question</td>
                    <td><input type="text" sname="label" value="" placeholder="Titre de la question"></td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td><textarea sname="description" placeholder="Description de la question (si nécessaire)"></textarea></td>
                </tr>
                <tr>
                    <td>Obligatoire ?</td>
                    <td><input type="checkbox" sname="mandatory" /></td>
                </tr>
            </table>
        </div>
        <div class="question questionchoices pair">
            <div class="nb">2.</div>
            <div class="rm">Supprimer</div>
            <table>
                <tr>
                    <td>Question</td>
                    <td><input type="text" sname="label" class="label" value="" placeholder="Titre de la question"></td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td><textarea sname="description" placeholder="Description de la question (si nécessaire)"></textarea></td>
                </tr>
                <tr>
                    <td>Obligatoire ?</td>
                    <td><input type="checkbox" sname="smandatory" /></td>
                </tr>
                <tr>
                    <td>Maximum de réponses</td>
                    <td>
                        <select name="smax">
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
</div>

{js src="survey.editor.js"}
<script>
    survey_editor("{$id}");
</script>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
