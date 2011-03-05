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


<tr class="pair">
    <td>
        Titre :
    </td>
    <td>
        <input type='text' required name='title' value="{$item->title()}" placeholder="Titre de l'annonce" />
    </td>
</tr>

<tr>
    <td>
        Image :
    </td>
    <td>
        {if $item->image()}
            <div>
                Actuellement :
                <a fancy="fancy" href="{$item->image()|image:'full'}">
                    <img src="{$item->image()|image:'small'}" />
                </a>
            </div>
        {/if}
        {include file="uploader.tpl"|rel id="image"}
    </td>
</tr>

<tr class="pair">
    <td>
        Corps :
    </td>
    <td>
        {include file="wiki_textarea.tpl"|rel id="news_content" already=$item->content()|smarty:nodefaults placeholder="Corps de l'annonce" }
    </td>
</tr>

<tr>
    <td>
        Visible
    </td>
    {uniqid output='uniqid'}
    <td id="dates_{$uniqid}">
        de <input type="text" name="begin" value="" already="{$item->begin()|datetime:'m/d/Y H:i'}"
                  required {literal}pattern="(?=^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$).*"{/literal}/>
        à  <input type="text" name="end" value="" already="{$item->end()|datetime:'m/d/Y H:i'}"
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
            $begin.datetimepicker({minDate: begin, maxDate: "+7D", defaultDate: begin});
            $begin.datetimepicker('setDate', begin);
            $end.datetimepicker({minDate: end, maxDate: "+7D", defaultDate: end});
            $end.datetimepicker('setDate', end);
        });
        {/literal}
        </script>
    </td>
</tr>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
