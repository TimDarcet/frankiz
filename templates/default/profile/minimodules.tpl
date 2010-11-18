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

<div class="profil_minimodules">
    <p>
        {include file="wiki.tpl"|rel name='profile/minimodules'}
    </p>
    <table>
        <tr>
            <th>
                ?
            </th>
            <th>
                Frequence
            </th>
            <th>
                Nom
            </th>
             <th>
                Description
            </th>
        </tr>
        {foreach from=$minimodules item=minimodule}
        <tr>
            <td>
                <input name="{$minimodule.name}" type="checkbox" {if $minimodule.activated}checked="checked"{/if} onclick="{literal}if (!this.checked) {removeMinimodule(this.name, this);} else {addMinimodule(this.name, this);}{/literal}"/>
            </td>
            <td>            
                {$minimodule.frequence}
            </td>
            <td>            
                {$minimodule.label}
            </td>
            <td>            
                {$minimodule.description}
            </td>
        </tr>
        {/foreach}
    </table>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
