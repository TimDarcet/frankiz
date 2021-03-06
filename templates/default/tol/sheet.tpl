{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009-2013 Binet Réseau                                  *}
{*  http://br.binets.fr/                                                  *}
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

    {assign var='birthdate' value=$result->birthdate()}
    {assign var='photo' value=$result->photo()}
    {assign var='original' value=$result->original()}
    <div class="img" photo="{if $photo}{$photo|image:'full'|smarty:nodefaults}{/if}"
                     original="{if $original}{$original|image:'full'|smarty:nodefaults}{/if}">
        {assign var='img' value=$result->image()}
        <a ><img src="{$img|image:'big'|smarty:nodefaults}" /></a>
    </div>

    <div class="name">
        {$result->fullName()}
        {if $user->isWeb() or $user->isAdmin()}
            (
            {if $user->isAdmin()}
                <a class="warning" href="admin/su/{$result->hruid()}">su</a> |
            {/if}
            <a class="warning" href="profile/admin/account/{$result->hruid()}">administrer</a>)
        {/if}
    </div>

    <div class="nickname">{$result->nickname()}</div>

    {if $birthdate->isValid}
        <div class="birthdate">
            <span>{$birthdate|datetime:"d/m/Y"} </span> ({$birthdate|age})
        </div>
    {/if}

    <div class="sports">
        <ul>
            {foreach from=$groups|filter:'ns':'sport'|order:'score' item='group'}
                <li><img src="{$group->image()|image:'small'}" title="{$group->label()}" /></li>
            {/foreach}
        </ul>
    </div>

    {if $result->email()}
        <div class="email">
            Email&nbsp;: <a href="mailto:{$result->bestEmail()}">{$result->bestEmail()}</a>
        </div>
    {/if}

    {if $result->cellphone()}
        <div class="cellphone">
            Portable&nbsp;: <a href="tel:{$result->cellphone()}">{$result->cellphone()}</a>
        </div>
    {/if}

    <div class="caserts">
        Casert&nbsp;:
        <ul>
        {foreach from=$result->rooms() item='room'}
            <li>
                {$room|room:phone}
                <ul class="ips">
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
            {if $study->promo() != 0}
            <li>
                {assign var='formation' value=$study->formation()}
                <img title="{$formation->label()}" src="{$formation->image()|image:'micro'|smarty:nodefaults}" />
                <span class="promo {if $study->promo() % 2 == 0}rouje{else}jone{/if}"
                      title="{$study->year_in()} ->{$study->year_out()}">{$study->promo()}</span>
            </li>
            {/if}
        {/foreach}
        </ul>
    </div>

    <div class="nationality">
        Nationalité :
        <ul>
            {foreach from=$groups|filter:'ns':'nationality'|order:'score' item='group'}
                <li>{$group|group:'text'}</li>
            {/foreach}
        </ul>
    </div>
    <hr />
</div>
<div class="more">
    {if count($groups|filter:'ns':'binet') > 0}
        <div class="binets">
            Binets :
            <ul>
                {foreach from=$groups|filter:'ns':'binet'|order:'score' item='group'}
                    <li>
                        <span class="meta">
                            {$group|group}
                            {$result->rights($group)|@rights}
                        </span>
                        {if $result->isMe($smarty.session.user)}
                            <span>
                                {grpvisibility user=$result group=$group}
                            </span>
                        {/if}
                        <span>
                            {$group|group:'text'}
                            <span class="comments">{$result->comments($group)}</span>
                        </span>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/if}
    {if count($groups|filter:'ns':'free') > 0}
        <div class="free">
            Groupes:
            <ul>
                {foreach from=$groups|filter:'ns':'free'|order:'score' item='group'}
                    <li>
                        <span class="meta">
                            {$group|group}
                            {$result->rights($group)|@rights}
                        </span>
                        {if $result->isMe($smarty.session.user)}
                            <span>
                                {grpvisibility user=$result group=$group}
                            </span>
                        {/if}
                        <span>
                            {$group|group:'text'}
                            <span class="comments">{$result->comments($group)}</span>
                        </span>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/if}
    {if count($groups|filter:'ns':'course') > 0}
        <div class="free">
            Cours:
            <ul>
                {foreach from=$groups|filter:'ns':'course'|order:'score' item='group'}
                    <li>
                        <span class="meta">
                            {$group|group}
                            {$result->rights($group)|@rights}
                        </span>
                        {if $result->isMe($smarty.session.user)}
                            <span>
                                {grpvisibility user=$result group=$group}
                            </span>
                        {/if}
                        <span>
                            {$group|group:'text'}
                            <span class="comments">{$result->comments($group)}</span>
                        </span>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/if}
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
