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

<table>
    <tr>
        <th>Session</th>
        <th>User</th>
        <th>IP</th>
        <th>Host</th>
        <th>Forward IP</th>
        <th>Forward Host</th>
        <th>Browser</th>
        <th>Suid</th>
        <th>Flags</th>
    </tr>
    {foreach from=$sessions key='sid' item='session'}
        <tr>
            <td><a href="admin/logs/events/{$sid}">{$session.start|datetime}</a></td>
            <td>{$session.user|user}</td>
            <td>{$session.ip}</td>
            <td>{$session.host}</td>
            <td>{$session.forward_ip}</td>
            <td>{$session.forward_host}</td>
            <td>{$session.browser}</td>
            <td>{$session.suid}</td>
            <td>{$session.flags}</td>
        </tr>
    {/foreach}
</table>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
