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


<div class="head">
   Activité Ponctuelle à modifier
</div>

<div class="body">
    <table>
        <tr>
            <td width=20%>
                Titre :
            </td>
            <td>
                <input type='text' name='title' value="{$activity->title()}" />
            </td>
        </tr>

        <tr>
            <td>
                Description :
            </td>
            <td>
                {include file="wiki_textarea.tpl"|rel id="activity_description" already=$activity->description()|smarty:nodefaults}
            </td>
        </tr>

        <tr>
            <td>
                Date
            </td>
            <td>
                du <input type="text" name="begin" id="begin" value=""
                    required {literal}pattern="(?=^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$).*"{/literal}/>
                à  <input type="text" name="end" id="end" value=""
                    required {literal}pattern="(?=^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$).*"{/literal}/>
                <script>{literal}
                $(function() {
                    limit_inf = new Date();
                    limit_inf.setMinutes(0);
                    var begin = new Date('{/literal}{$activity->begin()|datetime:'m/d/Y H:i'}{literal}');
                    var end = new Date('{/literal}{$activity->end()|datetime:'m/d/Y H:i'}{literal}');
                    var dates = $("#begin, #end").datetimepicker({ minDate:limit_inf, maxDate: "+1Y"});
                    $("#begin").datetimepicker('setDate', begin);
                    $("#end").datetimepicker('setDate', end);
                });
                {/literal}</script>
            </td>
        </tr>

        <tr>
            <td>
                <input type="submit" name="delete" value="Supprimer"/>
            </td>
            <td>
                <input type="submit" name="modify" value="Modifier"/>
            </td>
        </tr>
    </table>
</div>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
