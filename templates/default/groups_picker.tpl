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

<div class="groups_picker" id="groups_picker_{$id}">
    <div class="empty">
        Sélectionner
    </div>

    <ul class="selected">
        {if t($already)}
            {foreach from=$already item='group'}
                <li gid="{$group->id()}"><img src="{$group->image()|image:'micro'|smarty:nodefaults}" />{$group->label()}</li>
            {/foreach}
        {/if}
    </ul>

    <div class="searcher">
        <input type="text" name="filter" value="" />
    </div>

    <ul class="list">
    </ul>

    <input auto="auto" type="text" id="{$id}" name="{$id}" value="" />
</div>

<script>
    groups_picker("{$id}", "{$ns}", {$check|default:"-1"}, "{if t($order)}{$order}{else}score{/if}");
</script>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
