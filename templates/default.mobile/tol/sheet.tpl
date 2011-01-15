{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet RÃ©seau                                       *}
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
<div class="result">
    <a href="#" class="more-button">+</a>
    <div class="infos">
        {$result->firstname()} {$result->lastname()}
        {if $result->nickname()}
            ({$result->nickname()})
        {/if}
        {if $result->cellphone()}
            <br />{$result->cellphone()}
        {/if}
    </div>

    <div class="more">
        {assign var='img' value=$result->image()}
        <a class="photo" href="{$img->src(2)|smarty:nodefaults}" src="{$img->src()|smarty:nodefaults}"></a>
        <div class="associations">
            Groupes :
            <ul class="group-list">
                {assign var='castes' value=$result->castes()}
                {assign var='groups' value=$castes->groups()}

                {foreach from=$groups|order:'score' item='group'}
                <li>
                    <a href="groups/see/{$group->bestId()}">{$group->label()}</a>
                </li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
