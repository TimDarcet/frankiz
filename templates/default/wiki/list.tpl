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


<p>
    <input type="text" id="newwiki" value="" />
    <input type="button" value="Créer un wiki" onclick="{literal}if ($('#newwiki').val()) document.location='wiki/admin/' + $('#newwiki').val();{/literal}" />
</p>

<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Versions</th>
            <th>Commentaires</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$wikis item='wiki'}
        <tr>
            <td><a href="wiki/admin/{$wiki->id()}">{$wiki->name()}</a></td>
            <td>{$wiki->count()}</td>
            <td>{$wiki->comments()}</td>
        </tr>
        {/foreach}
    </tbody>
</table>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
