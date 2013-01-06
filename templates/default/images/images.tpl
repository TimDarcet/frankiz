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

<p>
    <a href="images/upload">Envoyer une image</a>
</p>

Liste des images visibles par l'utilisateur courant

<table>
    <thead>
        <tr>
            <th>Group</th>
            <th>Small</th>
            <th>Label</th>
            <th>Size</th>
            <th>Mime</th>
            <th>x * y</th>
            <th>Seen</th>
            <th>Last-seen</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$images|order:'seen' item=image}
        <tr>
            <td>{$image->group()|group}</td>
            <td>{$image->html()|smarty:nodefaults}</td>
            <td>{$image->label()}</td>
            <td>{$image->size()|bytes_format}</td>
            <td>{$image->mime()}</td>
            <td>{$image->x()} * {$image->y()}</td>
            <td>{$image->seen()}</td>
            <td>{$image->lastseen()}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
