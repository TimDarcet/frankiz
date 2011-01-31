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

{$group->label()}

<div id="display">
    {$group->description()|miniwiki:'title'|smarty:nodefaults}
</div>
<textarea id="textarea">
    {$group->description()}
</textarea>

<script>
    wiki_preview.start($("#textarea"), $("#display"));
</script>

<table>
    <tr>
        <td class="users">
            {include file="users_picker.tpl"|rel id="users_picker" group=$group filters='["promo"]'}
        </td>
        <td>
            <select name="caste" onchange="">
            {foreach from=$group->caste() item='caste'}
                <option value="{$caste->id()}">{$caste->rights()}</option>
            {/foreach}
            </select>
            <div id="caste_users">

            </div>
        </td>
    </tr>
</table>

{js src="groups.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
