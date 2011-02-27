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

    {if isset($auto|smarty:nodefaults)}
        <div class="msg_proposal">
            Ton activité a été automatiquement validée. Elle est dès à présent visible.
        </div>

    {else}
        <div class="msg_proposal"> Ton activité a été proposée. <br />
        Le responsable du groupe essaiera de te la valider au plus vite</div>
    {/if}

{else}

{if $msg}
    <div class="msg_proposal"> 
        {$msg}
    </div>
{/if}


<div class="module activity_prop choice">
    <div class="head">
        Type d'activité
    </div>
    <div class="body">
        <ul>
            <li>
                <a onclick="activity_show('new_reg')">Nouvelle activité régulière</a>
            </li>
            <li>
                <a onclick="activity_show('reg')">Nouvelle scéance d'une activité régulière</a>
            </li>
            <li>
                <a onclick="activity_show('new')">Activité ponctuelle</a>
            </li>
        </ul>
    </div>
</div>

<form enctype="multipart/form-data" method="post" action="proposal/activity">
    <div class="module activity_prop reg">
        <div class="head">
            <span class="helper" target="proposal/activity"></span>
            Activité régulière
        </div>
        <div class="body">
            <table>
                <tr>
                    <td>
                        Sélectionner :
                    </td>
                    <td>
                        {foreach from=$regular_activities item=activity}
                            <input type="radio" name="regular_activity_proposal" value="{$activity->id()}" 
                                {if $choice_regular == $activity->id()}checked{/if}>
                            {$activity->title()}
                            (<a href="activity/regular/modify/{$activity->id()}">modifier</a>)
                            <br />
                        {/foreach}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="hide activity_prop" id="old_activity_proposal">
    </div>
</form>


<form enctype="multipart/form-data" method="post" action="proposal/activity">
    <div class="module activity_prop new" id="new_activity_proposal">
        <div class="head">
           Activité Ponctuelle
        </div>
        <div class="body">
            <table>
                <tr>
                    <td width=20%>
                        Au nom de :
                    </td>
                    <td>
                        {include file="origin_picker.tpl"|rel id="origin_activity_proposal"}
                    </td>
                </tr>

                <tr>
                    <td width=20%>
                        Visible par:
                    </td>
                    <td>
                        {include file="target_picker.tpl"|rel id="activity" group_perso=true only_admin=false}
                    </td>
                </tr>

                <tr>
                    <td>
                        Titre :
                    </td>
                    <td>
                        <input type='text' required name='title' value="{$title_activity}" placeholder="Nom de l'événement"/>
                    </td>
                </tr>

                <tr>
                    <td>
                        Description :
                    </td>
                    <td>
                        <textarea name='desc' id="text_proposal" placeholder="Description" rows=7 cols=50>{$desc}</textarea>
                    </td>
                </tr>

                <tr>
                    <td>
                        Date
                    </td>
                    <td>
                        de <input type="text" name="begin" id="begin" value=""
                            required {literal}pattern="(?=^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$).*"{/literal}/>
                        à  <input type="text" name="end" id="end" value=""
                            required {literal}pattern="(?=^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$).*"{/literal}/>
                        <script>{literal}
                        $(function() {
                            var dates = $( "#begin, #end" ).datetimepicker({
                                minDate: new Date(),
                                maxDate: "+31D",
                                onSelect: function( selectedDate ) {
                                    var option = this.id == "begin" ? "minDate" : "maxDate";
                                    dates.not( this ).datepicker( "option", option, selectedDate );
                                }
                            });
                            $("#begin").datetimepicker('setDate', new Date());
                            $("#end").datetimepicker('setDate', new Date());
                        });
                        {/literal}</script>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="send_new" value="Valider" onClick="return window.confirm('Voulez vous vraiment proposer cette activité ?')"/>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>

<div class=" activity_prop new_reg">
    {include file="activity/new_regular_activity.tpl"|rel}
</div>

{/if}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}