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

    <div class="msg_proposal"> Ta nouvelle activité régulière a été créée. </div>

{else}

{if $msg}
    <div class="msg_proposal"> 
        {$msg}
    </div>
{/if}


<form enctype="multipart/form-data" method="post" action="activity/regular/new">
    <div class="module">
        <div class="head">
           Nouvelle activité régulière
        </div>

        <div class="body">
            <table>
                <tr>
                    <td width=20%>
                        Pour :
                    </td>
                    <td>
                        {include file="target_picker.tpl"|rel id="activity"}
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

                <tr>
                    <td>
                        Description :
                    </td>
                    <td>
                        <textarea name='description' id="text_proposal"
                            placeholder="Description" rows=7 cols=50>{$description}</textarea>
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
                
                <tr>
                    <td width=20%>
                        Horaires
                    </td>
                    <td>
                        de <input type="text" name="begin" id="begin"
                                  required {literal}pattern="(?=^[0-9]{2}:[0-9]{2}$).*"{/literal}/>
                        à  <input type="text" name="end" id="end"
                                  required {literal}pattern="(?=^[0-9]{2}:[0-9]{2}$).*"{/literal}/>
                        <script>{literal}
                            $(function() {
                                $("#begin").timepicker({});
                                $("#end").timepicker({});
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
{/if}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
