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

<form enctype="multipart/form-data" method="post" action="activity/regular/new">
    {xsrf_token_field}
    <div class="module">
        <div class="head">
            <span class="helper" target="activity/regular/new"> </span>
            Nouvelle activité régulière
        </div>

        <div class="body">
            <table class="bicol">
                <tr class="pair">
                    <td width=20%>
                        Pour :
                    </td>
                    <td>
                        {include file="target_picker.tpl"|rel id="activity" group_perso=true only_admin=true}
                    </td>
                </tr>

                <tr>
                    <td>
                        Titre :
                    </td>
                    <td>
                        <input type='text' name='title' placeholder="Nom de l'activité" value="{$title_activity}" />
                    </td>
                </tr>

                <tr class="pair">
                    <td>
                        Description :
                    </td>
                    <td>
                        {include file="wiki_textarea.tpl"|rel id="activity_description" already=$description
                                placeholder="Description" }
                    </td>
                </tr>

                <tr>
                    <td>
                        Jours :
                    </td>
                    <td>
                        <label><input type="checkbox" name="days[]" value="Monday"/> Lundi <br/></label>
                        <label><input type="checkbox" name="days[]" value="Tuesday"/> Mardi <br/></label>
                        <label><input type="checkbox" name="days[]" value="Wednesday"/> Mercredi <br/></label>
                        <label><input type="checkbox" name="days[]" value="Thursday"/> Jeudi <br/></label>
                        <label><input type="checkbox" name="days[]" value="Friday"/> Vendredi <br/></label>
                        <label><input type="checkbox" name="days[]" value="Saturday"/> Samedi <br/></label>
                        <label><input type="checkbox" name="days[]" value="Sunday"/> Dimanche</label>
                    </td>
                </tr>

                <tr class="pair">
                    <td width=20%>
                        Horaires
                    </td>
                    <td>
                        de <input type="text" name="begin" id="begin_newreg"
                                  required {literal}pattern="(?=^[0-9]{2}:[0-9]{2}$).*"{/literal}/>
                        à  <input type="text" name="end" id="end_newreg"
                                  required {literal}pattern="(?=^[0-9]{2}:[0-9]{2}$).*"{/literal}/>
                        <script>{literal}
                            $(function() {
                                $("#begin_newreg").timepicker({});
                                $("#end_newreg").timepicker({});
                            });
                        {/literal}</script>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="send" value="Valider" onClick="return window.confirm('Voulez vous vraiment créer cette activité ?')"/>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
