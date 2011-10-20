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

<div class="target_picker" id="origin_picker_{$id}">
    <div>
        {target_picker user_groups='user_groups' fkz_groups='fkz_groups' own_group='own_group' even_only_friend=$even_only_friend}
        <select id="{$id}" name="target_group_{$id}" >
            {if !t($only_admin) || !$only_admin}
            <optgroup name="fkz" label="Frankiz">
                {foreach from=$fkz_groups item='group'}
                    <option description="{$group->description()}" value="{$group->id()}">{$group->label()}</option>
                {/foreach}
            </optgroup>
            {/if}
            {if t($group_perso) && $group_perso}
                <option own_group="own_group" description="Ne sera visible que par moi" value="{$own_group->id()}">Juste moi</option>
            {/if}
            {if $even_only_friend}
                <optgroup label="Sans validation">
            {/if}
            {if !$even_only_friend}
                <optgroup label="Mes groupes">
            {/if}
                {foreach from=$user_groups item='group'}
                    <option value="{$group->id()}">{$group->label()}</option>
                {/foreach}
            </optgroup>
            {if $even_only_friend}
                <optgroup label="Avec validation">
                    {foreach from=$only_friend item='group'}
                        <option value="{$group->id()}">{$group->label()}</option>
                    {/foreach}
                </optgroup>
            {/if}
        </select>

        <label><input type="checkbox" name="target_everybody_{$id}" checked="checked" />Public</label>
    </div>
    <div class="comments">
        
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
