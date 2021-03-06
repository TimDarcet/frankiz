{**************************************************************************}
{*  Copyright (C) 2004-2013 Binet Réseau                                  *}
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
    {foreach from=$rooms item=rooms_list}
        <div class="head">
            Bataclan
        </div>
        <div class="body">
            <table border="0">
                {foreach from=$rooms_list item=room}
                    <tr class="rooms">
                        <td>
                            {foreach from=$room->groups() item=group}
                                {$group|group}
                            {/foreach}
                        </td>
                        <td>
                            <a href="rooms/see/{$room->id()}">{$room->id()}</a> {$room|room:phone}
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    {/foreach}
</div>
