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

<table>
    <tr>
    	<th>Ns</th>
        <th>%</th>
        <th>Name</th>
        <th>Rights</th>
    </tr>
    {assign var='dev_null' value=$minimodule.groups->order('frequency')}
    {foreach from=$minimodule.groups item=group}
        <tr>
            <td>{$group->ns()}</td>
            <td>{$group->frequency()}</td>
            <td>{$group->name()}</td>
            {assign var='rights' value=$user->rights($group)}
            <td>{$rights->flags()}</td>
        </tr>
    {/foreach}
</table>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
