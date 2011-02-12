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


<tr>
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
        {include file="uploader.tpl"|rel id="image"}
    </td>
</tr>

<tr>
    <td>
        Corps :
    </td>
    <td>
        {include file="wiki_textarea.tpl"|rel id="news_content" already=$item->content() placeholder="Corps de l'annonce" }
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
            var begin = new Date('{/literal}{$item->begin()|datetime:'m/d/Y H:i'}{literal}');
            var end = new Date('{/literal}{$item->end()|datetime:'m/d/Y H:i'}{literal}');
            $("#begin").datetimepicker({minDate: new Date(), maxDate: "+7D", defaultDate: begin});
            $("#begin").datetimepicker('setDate', begin);
            $("#end").datetimepicker({ minDate: new Date(), maxDate: "+7D", defaultDate: end});
            $("#end").datetimepicker('setDate', end);
        });
        {/literal}</script>
    </td>
</tr>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
