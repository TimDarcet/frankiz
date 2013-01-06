{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009-2013 Binet Réseau                                  *}
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

<div>
    <table>
        <tr>
            <th>Entité</th>
            <th>Occurences</th>
        </tr>
        <tr>
            <td>Users</td>
            <td>{$minimodule.users}</td>
        </tr>
        <tr>
            <td>Groups</td>
            <td>{$minimodule.groups}</td>
        </tr>
        <tr>
            <td>Castes</td>
            <td>{$minimodule.castes}</td>
        </tr>
        <tr>
            <td>News</td>
            <td>{$minimodule.news}</td>
        </tr>
        <tr>
            <td>Images</td>
            <td>{$minimodule.images}</td>
        </tr>
    </table>
    <a onclick="$(this).siblings('table').toggle()">Groupes</a>
    <table style="display: none">
        <tr>
            <th>Ns</th>
            <th>%</th>
            <th>Name</th>
            <th>Rights</th>
        </tr>
        {assign var='castes' value=$minimodule.user->castes()}
        {assign var='groups' value=$castes->groups()}
        {foreach from=$groups|order:'score' item='group'}
            <tr>
                <td>{$group->ns()}</td>
                <td>{$group->score()}</td>
                <td>{$group->name()}</td>
                <td>{$minimodule.user->rights($group)|@rights}</td>
            </tr>
        {/foreach}
    </table>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
