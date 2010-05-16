{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet Réseau                                       *}
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

<div id="todo_list">
    <table>
        {foreach from=$minimodule.list item=todo}
            <tr>
                <td><div class="checkbox" {if $todo.checked}checked="checked"{/if} todo_id="{$todo.todo_id}" /></td>
                <td>{$todo.tobedone}</td>
            </tr>
        {/foreach}
    </table>
</div>

<div class="addTodo">
    <form action="javascript:todo.add()" method="get">
        <span class="clear" onclick="todo.clear()">Vider</span>
        <input class="add" type="submit" value="+"/>
        <input id="todo_tobedone" class="tobedone" type="text" value="" />
    </form>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}