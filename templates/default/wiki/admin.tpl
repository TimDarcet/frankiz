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

<form enctype="multipart/form-data" method="post" action="wiki/admin/{$wiki->id()}">
    <input type="hidden" name="wid" value="{$wiki->id()}" />

    {$wiki->comments()}

    <table>
        <thead>
            <tr>
                <th>Version:
                    <p id="versions">
                        {section name=v start=1 loop=$wiki->count() step=1}
                            <a version="{$smarty.section.v.index}">{$smarty.section.v.index}</a>
                        {/section}
                        <a version="{$wiki->count()}" class="on">{$wiki->count()}</a>
                    </p>
                </th>
                <th>Nouvelle version</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td id="oldwriter">{$wiki->writer()|user}</td>
                <td>{$smarty.session.user|user}</td>
            </tr>
            <tr>
                <td id="oldwrote">{$wiki->wrote()|smarty:nodefaults}</td>
                <td></td>
            </tr>
            <tr>
                <td id="oldhtml">{$wiki->html()|smarty:nodefaults}</td>
                <td id="newcontentdisplay">{$wiki->html()|smarty:nodefaults}</td>
            </tr>
            <tr>
                <td id="oldcontent">
                    <textarea readonly="readonly">{$wiki->content()|smarty:nodefaults}</textarea>
                </td>
                <td>
                    <textarea name="newcontent" id="newcontent">{$wiki->content()|smarty:nodefaults}</textarea>
                </td>
            </tr>
        </tbody>
    </table>
    
    <input type="submit" value="Ajouter la nouvelle version" />
</form>

<script>wiki_preview.start($("#newcontent"), $("#newcontentdisplay"));</script>

{js src="wiki.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
