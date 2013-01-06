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

<table>
    <tr>
        <td width="20%">
            Nom
        </td>
        <td width="60%">
            Détail
        </td>
        <td>
            Total (moyenne, écart type)
        </td>
    </tr>
    {foreach from=$results item=el}
        <tr>
            <td>
                {$el.user|user:'text'}
            </td>
            <td>
                <div class="qdj_cell">
                    <table class="graph">
                        <caption>Points</caption>
                        <thead>
                            <tr>
                                <td></td>
                                <th scope="col">
                                    1er
                                </th>
                                <th scope="col">
                                    2e
                                </th>
                                <th scope="col">
                                    3e
                                </th>
                                <th scope="col">
                                    13
                                </th>
                                <th scope="col">
                                    42
                                </th>
                                <th scope="col">
                                    69
                                </th>
                                <th scope="col">
                                    pi
                                </th>
                                <th scope="col">
                                    ip
                                </th>
                                <th scope="col">
                                    bonus
                                </th>
                                <th scope="col">
                                    qdj
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="40px">
                                    {$el.nb1}
                                </td>
                                <td width="40px">
                                    {$el.nb2}
                                </td>
                                <td width="40px">
                                    {$el.nb3}
                                </td>
                                <td width="40px">
                                    {$el.nb4}
                                </td>
                                <td width="40px">
                                    {$el.nb5}
                                </td>
                                <td width="40px">
                                    {$el.nb6}
                                </td>
                                <td width="40px">
                                    {$el.nb7}
                                </td>
                                <td width="40px">
                                    {$el.nb8}
                                </td>
                                <td width="40px">
                                    {$el.nb9}
                                </td>
                                <td width="40px">
                                    {$el.nb10}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
            <td>
                {$el.total} ({$el.average}, {$el.deviation})
            </td>
        </tr>
    {/foreach}
</table>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
