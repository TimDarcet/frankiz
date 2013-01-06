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

<tr>
    <td>
        Titre :
    </td>
    <td>
        <input type='text' name='title' value="{$item->title()}" />
    </td>
</tr>
    
<tr>
    <td>
        Description :
    </td>
    <td>
        {include file="wiki_textarea.tpl"|rel id="description" already=$item->description()|smarty:nodefaults}
    </td>
</tr>

<tr>
    <td>
        Date :
    </td>
    {uniqid output='uniqid'}
    <td id="dates_{$uniqid}">
        de <input type="text" name="begin" id="begin" value="" already="{$item->begin()|datetime:'m/d/Y H:i'}"
                  required {literal}pattern="(?=^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$).*"{/literal}/>
        à  <input type="text" name="end" id="end" value="" already="{$item->end()|datetime:'m/d/Y H:i'}"
                  required {literal}pattern="(?=^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$).*"{/literal}/>
        <script>
        {literal}
        $(function() {
            {/literal}
            var $begin = $("#dates_{$uniqid} [name=begin]");
            var $end = $("#dates_{$uniqid} [name=end]");
            {literal}

            var begin = new Date($begin.attr("already"));
            var end = new Date($end.attr("already"));

            limit_inf = new Date(begin);
            limit_inf.setMinutes(0);

            limit_sup = new Date(end);
            limit_sup.setMinutes(0);

            $begin.datetimepicker({minDate: limit_inf, maxDate: "+1Y", defaultDate: begin});
            $begin.datetimepicker('setDate', begin);
            $end.datetimepicker({minDate: limit_sup, maxDate: "+1Y", defaultDate: end});
            $end.datetimepicker('setDate', end);
        });
        {/literal}
        </script>
    </td>
</tr>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
