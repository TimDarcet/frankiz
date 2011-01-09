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

{if $last_upload}
    Image <img src="{$last_upload->src()|smarty:nodefaults}" width="140" height="105" /> bien envoyée
{/if}

Envoyer une image

<div>
    <form enctype="multipart/form-data" method="post" accept-charset="UTF-8" action="{$smarty.server.REQUEST_URI}">
        <ul>
            <li>
                <label>Groupe
                    <select name="group">
                        {foreach from=$groups item='group'}
                        <option value="{$group->id()}">{$group->label()}</option>
                        {/foreach}
                    </select>
                 </label>
            </li>
            <li><label>Fichier <input type="file" name="file" /></label></li>
            <li><label>Nom <input type="text" name="label"></input></label></li>
            <li><label>Description <textarea name="description"></textarea></label></li>
        </ul>
        <input type="submit" value="Envoyer" />
    </form>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
