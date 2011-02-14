{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010 Binet Réseau                                       *}
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

{js src="activities.js"}

<div class="module" id="activity_show" style="display:none;">
    <div class="head">
        <span class="origin">
        </span>
        <span class="title">
        </span>
    </div>
    <div class="body">
        <div class="date">
        </div>
        <div class="time">
            de
            <span class="hour_begin">
            </span>
            à
            <span class="hour_end">
            </span>
        </div>
        <div class="msg">
        </div>
        <div class="participate">
        </div>
        <div class="section">
            Description :
        </div>
        <div class="description">
        </div>
        <div class="comment">
        </div>
        <div class="section participants_list">
            Participants :
            <span class="number">
            </span>
        </div>
        <div class="participants">
        </div>
    </div>
</div>

<div class="module activities" id="activities">
    <div class="head">
        <span class="helper" target="activities"> </span>
        Activités 
    </div>
    <div class="body" id="activities_list">
        {foreach from=$activities item=activities_day key=date}
            <div class="day">
                <div class="date">
                    {$date|date_format}
                </div>
                <div class="activities_day">
                    {foreach from=$activities_day item=activity key=id}
                        {$activities_day|order:'hour_begin':false}
                        <div class="activity {if $activity->participate()}star{else}unstar{/if}" aid="{$activity->id()}">
                            <span class="star_switcher" onclick="switch_participate({$activity->id()})">&emsp;</span>
                            {$activity->hour_begin()} à {$activity->hour_end()} :
                            {if $activity->origin()}
                                {assign var='origin' value=$activity->origin()}
                                <a href="groups/see/{$origin->name()}">
                                    <img src="{$origin->image()|image:'micro'|smarty:nodefaults}" 
                                        title="{$origin->label()}""/>
                                </a>
                            {else}
                                {assign var='writer' value=$activity->writer()}
                                <a href="tol/?hruid={$writer->login()}">
                                <img src="{$writer->image()|image:'micro'|smarty:nodefaults}" 
                                     title="{$writer->displayName()}"/>
                                </a>
                            {/if} 
                            <b>{$activity->title()}</b> 
                        </div>
                    {/foreach}
                </div>
            </div>
        {/foreach}
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
