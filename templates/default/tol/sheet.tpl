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

<div class="base">
    {assign var='photo' value=$result->photo()}
    {assign var='original' value=$result->original()}
    <div class="img" photo="{if $photo}{$photo->src(2)|smarty:nodefaults}{/if}"
                     original="{if $original}{$original->src(2)|smarty:nodefaults}{/if}">
        {assign var='img' value=$result->image()}
        <a href="{$img->src(2)|smarty:nodefaults}"><img src="{$img->src()|smarty:nodefaults}" /></a>
    </div>
    <div class="sports">

    </div>
    <div class="name">{$result->firstname()} {$result->lastname()} - {$result->nickname()}</div>
    <div>

    </div>
    <hr />
</div>
<div class="more">
    <div class="associations">
        Binets:
        <ul>
            {assign var='castes' value=$result->castes()}
            {assign var='groups' value=$castes->groups()}
            
            {foreach from=$groups|order:'score' item='group'}
                <li>{$group->score()} {$group->label()} {$result->rights($group)|@rights} </li>
            {/foreach}
        </ul>
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
