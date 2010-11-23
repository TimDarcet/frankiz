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

{$wiki->name()}
{$wiki->comments()}

<table>
    <thead>
        <tr>
            <th>Version <input type="text" value="{$leftVersion}" /></th>
            <th>Last {$wiki->count()}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{$wiki->writer($leftVersion)|user}</td>
            <td>{$wiki->writer()|user}</td>
        </tr>
        <tr>
            <td>{$wiki->wrote($leftVersion)|smarty:nodefaults}</td>
            <td>{$wiki->wrote()|smarty:nodefaults}</td>
        </tr>
        <tr>
            <td>{$wiki->html($leftVersion)|smarty:nodefaults}</td>
            <td id="newcontentdisplay">{$wiki->html()|smarty:nodefaults}</td>
        </tr>
        <tr>
            <td>
                <textarea readonly="readonly">{$wiki->content($leftVersion)|smarty:nodefaults}</textarea>
            </td>
            <td>
                <textarea id="newcontent">{$wiki->content()|smarty:nodefaults}</textarea>
            </td>
        </tr>
    </tbody>
</table>

<input type="submit" value="Ajouter la nouvelle version" />

<script>wiki_preview.start($("#newcontent"), $("#newcontentdisplay"));</script>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
