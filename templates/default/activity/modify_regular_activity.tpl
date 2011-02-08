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
   Activité à modifier du groupe :
</div>

<div class="body">
    <form enctype="multipart/form-data" method="post" action="activity/regular/modify/{$activity->id()}" id="activity_modify">
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
                    <textarea name='description' id="text_proposal" rows=7 cols=50>{$activity->description()}</textarea>
                </td>
            </tr>
    
            <tr>
                <td>
                    Jours :
                </td>
                <td>
                    <label><input type="checkbox" name="days[]" value="Monday"
                        {if $activity->include_day('Monday')}checked{/if}/> Lundi <br/></label>
                    <label><input type="checkbox" name="days[]" value="Tuesday"
                        {if $activity->include_day('Tuesday')}checked{/if}/> Mardi <br/></label>
                    <label><input type="checkbox" name="days[]" value="Wednesday"
                        {if $activity->include_day('Wednesday')}checked{/if}/> Mercredi <br/></label>
                    <label><input type="checkbox" name="days[]" value="Thursday"
                        {if $activity->include_day('Thursday')}checked{/if}/> Jeudi <br/></label>
                    <label><input type="checkbox" name="days[]" value="Friday"
                        {if $activity->include_day('Friday')}checked{/if}/> Vendredi <br/></label>
                    <label><input type="checkbox" name="days[]" value="Saturday"
                        {if $activity->include_day('Saturday')}checked{/if}/> Samedi <br/></label>
                    <label><input type="checkbox" name="days[]" value="Sunday"
                        {if $activity->include_day('Sunday')}checked{/if}/> Dimanche</label>
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
                            var begin = new Date();
                            begin.setHours({/literal}{$activity->default_begin()|substr:0:2}{literal});
                            begin.setMinutes({/literal}{$activity->default_begin()|substr:3:2}{literal});
                            $("#begin").timepicker({defaultDate: end});
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
                <td></td>
                <td>
                    <input type="submit" name="modify" value="Modifier"/>
                </td>
            </tr>
        </table>
    </form>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
