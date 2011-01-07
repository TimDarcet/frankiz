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
    {include file="wiki.tpl"|rel name='profile/minimodules'}
</p>

<ul class="objects">
    {foreach from=$minimodules item=minimodule}
    <li {if $minimodule.activated}class="on"{/if}>
        <p class="frequency">Popularité: {math equation="100 * x / y" x=$minimodule.frequency y=$total format="%d"}%</p>
        <p class="label">{$minimodule.label}</p>
        <p class="description">{$minimodule.description}</p>
        <div class="change">
                <div class="checkbox"
                     {if $minimodule.activated} checked="checked"{/if}
                     onclick="
                        var name = '{$minimodule.name}';
                        {literal}
                        if (!($(this).attr('disabled') == 'disabled')) {
                            if ($(this).attr('checked') == 'checked') {
                                $(this).removeAttr('checked');
                                removeMinimodule(name, $(this), $(this).closest('li'));
                            } else {
                                $(this).attr('checked', 'checked');
                                addMinimodule(name, $(this), $(this).closest('li'));
                            }
                        }
                        {/literal}"
                />
        </div>
    </li>
    {/foreach}
</ul>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
