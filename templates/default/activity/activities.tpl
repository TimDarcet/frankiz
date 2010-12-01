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

<div id="activities_list" >
    {foreach from=$activities item=activities_day key=date}
        <div class="day">
            <div class="date">
                {*Hack because strtotime and date_formate do not consider timestamps the same way*}
                {math equation="s + 3600" s=$date|strtotime assign=date_new}
                {$date_new|date_format}
            </div>
            <div class="activities">
                {foreach from=$activities_day item=activity key=id}
                    {$activities_day|order:'hour_begin':false}
                    {assign var='target' value=$activity->target()}
                    <div class="activity" aid="{$activity->id()}">
                        {$activity->hour_begin()} à {$activity->hour_end()} : [{$target|group}] <b>{$activity->title()}</b>
                    </div>
                {/foreach}
            </div>
        </div>
    {/foreach}
</div>

<div id="activity_show" style="display:none;">
    <div class="first">
        [<span class="target">
        </span>]
        <span class="title">
        </span>
    </div>
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

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}