{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2012-2013 Binet Réseau                                  *}
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
    <div class="head">
        Authentification externe
    </div>
    <div class="body">
        <table border="1">
            <tr>
                <th></th>
                <th>Site</th>
                <th>Label</th>
                <th>Droits</th>
                <th>Groupes</th>
            </tr>
            {foreach from=$remotes item='r'}
                {assign var='remrights' value=$r->rights()}
                <tr>
                    <td>
                        <a href="remote/admin/{$r->id()}">
                            <div class="edit"></div>
                            Modifier
                        </a>
                    </td>
                    <td>{$r->site()}</td>
                    <td>{$r->label()}</td>
                    <td>{$remrights->flags()}</td>
                    <td>
                        {foreach from=$r->groups() item='g'}
                            {$g|group} {$g|group:'text'}<br />
                        {/foreach}
                    </td>
                </tr>
            {/foreach}
        </table>
        <a href="remote/admin/new">
            <div class="add_element"></div>
            Ajouter
        </a>
    </div>
</div>
