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

{assign var='castes' value=$result->castes()}
{assign var='groups' value=$castes->groups()}

<div class="base">
    {assign var='photo' value=$result->photo()}
    {assign var='original' value=$result->original()}
    <div class="img" photo="{if $photo}{$photo|image:'full'|smarty:nodefaults}{/if}"
                     original="{if $original}{$original|image:'full'|smarty:nodefaults}{/if}">
        {assign var='img' value=$result->image()}
        <a href="{$img|image:'full'|smarty:nodefaults}"><img src="{$img|image:'small'|smarty:nodefaults}" /></a>
    </div>
    <div class="sports">

    </div>
    <div class="name">{$result->firstname()} {$result->lastname()} - {$result->nickname()} - {$result->birthdate()|datetime:"d/m/Y"}</div>
    <div>
        {$result->cellphone()}
    </div>
    <div>
        <ul>
        {foreach from=$result->rooms() item='room'}
            <li>
            {$room->id()}
            {$room->phone()}
            <ul>
            {foreach from=$room->ips() item='ip'}
                <li>
                {$ip}
                </li>
            {/foreach}
            </ul>
            </li>
        {/foreach}
        </ul>
    </div>
    <div class="studies">
        <ul>
        {foreach from=$result->studies() item='study'}
            <li>
            {$study->year_in()}
            {$study->year_out()}
            {$study->promo()}
            {$study->forlife()}
            {assign var='formation' value=$study->formation()}
            {$formation->label()}
            <img src="{$formation->image()|image:'micro'|smarty:nodefaults}" />
            </li>
        {/foreach}
        </ul>
    </div>
    <div class="sports">
        <ul>
            {foreach from=$groups|filter:'ns':'sport'|order:'score' item='group'}
                <li>{$group|group}</li>
            {/foreach}
        </ul>
    </div>
    <div class="nationality">
        <ul>
            {foreach from=$groups|filter:'ns':'nationality'|order:'score' item='group'}
                <li>{$group|group}</li>
            {/foreach}
        </ul>
    </div>
    <hr />
</div>
<div class="more">
    <div class="binets">
        Binets:
        <ul>
            {foreach from=$groups|filter:'ns':'binet'|order:'score' item='group'}
                <li>{$result->rights($group)|@rights} {$group|group} {$result->comments($group)}</li>
            {/foreach}
        </ul>
    </div>
    <div class="free">
        Groupes:
        <ul>
            {foreach from=$groups|filter:'ns':'free'|order:'score' item='group'}
                <li>{$result->rights($group)|@rights} {$group|group} {$result->comments($group)}</li>
            {/foreach}
        </ul>
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
