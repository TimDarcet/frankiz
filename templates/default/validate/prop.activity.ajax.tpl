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
<div class="module">
    <div class="head">
        Création de nouvelles instances
    </div>

    <div class="body">
        <table>
            {foreach from=$days item=next_dates key=weekday}
                <tr>
                    <td width=20%>
                        {if $weekday == Monday}Lundi :
                        {elseif $weekday == Tuesday}Mardi :
                        {elseif $weekday == Wednesday}Mercredi :
                        {elseif $weekday == Thursday}Jeudi :
                        {elseif $weekday == Friday}Vendredi :
                        {elseif $weekday == Saturday}Samedi :
                        {elseif $weekday == Sunday}Dimanche :
                        {/if}
                    </td>
                    <td>
                        {foreach from=$next_dates item=day}
                            <span class="margin_right">
                                <label>
                                    <input type="checkbox" name="{$day}_regular_proposal"/>
                                    {$day}
                                </label>
                            </span>
                        {/foreach}
                    </td>
                </tr>
            {/foreach}

            <tr>
                <td width=20%>
                    autre :
                </td>
                <td>
                    <input type="checkbox" name="other_regular_proposal"/>
                    <input type="text" name="other_date" id="other_date"
                          {literal}pattern="(?=^[0-9]{4}-[0-9]{2}-[0-9]{2}$).*"{/literal}/>
                    <script>{literal}
                    $(function() {
                        limit_inf = new Date();
                        limit_inf.setMinutes(0);
                        $("#other_date").datepicker({minDate: limit_inf, maxDate: "+1Y", dateFormat: 'yy-mm-dd'});
                    });
                    {/literal}</script>
                </td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <input type="submit" name="send_reg" value="Valider" onClick="return window.confirm('Voulez vous vraiment proposer cette activité ?')"/>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="module"">
    <div class="head">
       Informations utilisées
    </div>

    <div class="body">
        <table>
            <tr>
                <td width=20%>
                    Durée
                </td>
                <td>
                    de <input type="text" name="begin" id="begin"
                              required {literal}pattern="(?=^[0-9]{2}:[0-9]{2}$).*"{/literal}/>
                    à  <input type="text" name="end" id="end"
                              required {literal}pattern="(?=^[0-9]{2}:[0-9]{2}$).*"{/literal}/>
                    <script>{literal}
                        $(function() {
                            var begin = new Date();
                            begin.setHours({/literal}{$activity->default_begin()|substr:0:2}{literal});
                            begin.setMinutes({/literal}{$activity->default_begin()|substr:3:2}{literal});
                            $("#begin").timepicker({defaultDate: begin});
                            $("#begin").timepicker('setDate', begin);
                            var end = new Date();
                            end.setHours({/literal}{$activity->default_end()|substr:0:2}{literal});
                            end.setMinutes({/literal}{$activity->default_end()|substr:3:2}{literal});
                            $("#end").timepicker({defaultDate: end});
                            $("#end").timepicker('setDate', end);
                        });
                    {/literal}</script>
                </td>
            </tr>

            <tr>
                <td>
                    Commentaire :
                </td>
                <td>
                    {include file="wiki_textarea.tpl"|rel id="activity_comment"
                        placeholder="Commentaire particulier (en plus de la description)" }
                </td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <input type="submit" name="send_reg" value="Valider" onClick="return window.confirm('Voulez vous vraiment proposer cette activité ?')"/>
                </td>
            </tr>
        </table>
    </div>    
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}