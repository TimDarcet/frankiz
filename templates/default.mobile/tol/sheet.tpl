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
{assign var='photo' value=$result->photo()}
{assign var='original' value=$result->original()}

<div class="result sheet">
    <a href="#" class="more-button">+</a>
    <div class="infos">
        {assign var='photo' value=$result->photo()}

        {assign var='img' value=$result->image()}
        <a class="show-photo" photobig="{if $photo}{$photo|image:'full'|smarty:nodefaults}{/if}" photomicro="{if $photo}{$photo|image:'micro'|smarty:nodefaults}{/if}"><img type="micro" class="img" src="{$img|image:'micro'|smarty:nodefaults}" /></a>

        <span class="name"><b>{$result->firstname()} {$result->lastname()}</b>
        {if $result->nickname()}
        ({$result->nickname()})
        {/if}
        </span>
        
        <div style="clear:left"></div>
        {foreach from=$result->rooms() item='room'}
            {$room->id()}
        {/foreach}
        {foreach from=$groups|filter:'ns':'sport'|order:'score' item='group'}{$group->label()} {/foreach}
        {foreach from=$result->studies() item='study'}{$study->promo()}{/foreach}
        <br>
        Tel&nbsp;:
        {if $room->phone()}{$room->phone()}{/if}
        {if $result->cellphone()}
        &nbsp;-&nbsp;{$result->cellphone()}
        {/if}
    </div>

    <div class="more">
        {$result->birthdate()|age} ({$result->birthdate()|datetime:"d/m/Y"})
        <br>
        <a href="mailto:{$result->bestEmail()}">{$result->bestEmail()}</a>
        <br>
        {foreach from=$groups|filter:'ns':'nationality'|order:'score' item='group'}{$group|group:'text'}{/foreach}
        
        {if count($groups|filter:'ns':'binet') > 0}
        <div class="binets">
            <div class="title">Binets</div>
            <ul>
                {foreach from=$groups|filter:'ns':'binet'|order:'score' item='group'}
                <li class="biglinks">
                    {$group|group:'text'}<br><span class="comments">{$result->comments($group)}</span>
                </li>
                {/foreach}
            </ul>
        </div>
        {/if}
        
        {if count($groups|filter:'ns':'free') > 0}
        <div class="free">
            <div class="title">Groupes</div>
            <ul>
                {foreach from=$groups|filter:'ns':'free'|order:'score' item='group'}
                <li class="biglinks">
                    {$group|group:'text'} <span class="comments">{$result->comments($group)}</span>
                </li>
                {/foreach}
            </ul>
        </div>
        {/if}
    </div>

</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
