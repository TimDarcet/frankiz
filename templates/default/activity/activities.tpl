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

<div class="activity_right">
    <div class="top">
         <a href="proposal/activity">Proposer une activité</a>
    </div>

    <div class="module" id="activity_show" style="display:none;">
        <div class="head">
            <span class="origin">
            </span>
            <span class="title">
            </span>
        </div>
        <div class="body">
            <div class="one_day">
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
            </div>
            <div class="several_days">
                <div class="begin">
                    du
                    <span class="date">
                    </span>
                    à
                    <span class="hour">
                    </span>
                </div>
                <div class="end">
                    au
                    <span class="date">
                    </span>
                    à
                    <span class="hour">
                    </span>
                </div>
            </div>
            <div class="msg">
            </div>
            <div class="present">
                <a onclick=""><span class="add_participant"></span>S'inscrire</a>
            </div>
            <div class="out">
                <a onclick=""><span class="remove_participant"></span>Se désinscrire</a>
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
            <div class="misc">
                <div class="mail">
                    <a href=""><div class="mail_ico"></div>Mail</a>
                </div>
                <div class="participants_link">
                    <a href=""><div class="group_ico"></div>Participants</a>
                </div>
                <div class="admin">
                    <a href=""><div class="edit"></div>Modifier</a>
                </div>
            </div>
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
                    {foreach from=$activities_day|order:'hour_begin':false item='activity' key='id'}
                        <div class="activity {if $activity->participate()}star{else}unstar{/if}" aid="{$activity->id()}">
                            <span class="star_switcher" onclick="switch_participate({$activity->id()})">&emsp;</span>
                            {canEdit target=$activity->target()}
                                <a href="activity/modify/{$activity->id()}"><div class="edit"></div></a>
                            {/canEdit}

                            {assign var='writer' value=$activity->writer()}
                            {if $writer->id() == $smarty.session.user->id()}
                                <a href="activity/participants/{$activity->id()}"><div class="mail_ico"></div></a>
                            {/if}

                            {$activity->hour_begin()} à {$activity->hour_end()}
                            {if !$activity->hour_end($date)}
                                ({$activity->end()|datetime:'d/m'})
                            {/if} :

                            {if $activity->origin()}
                                {assign var='origin' value=$activity->origin()}
                                <a href="groups/see/{$origin->name()}">
                                    <img src="{$origin->image()|image:'micro'|smarty:nodefaults}" 
                                        title="{$origin->label()}""/>
                                </a>
                            {else}
                                <a href="tol/?hruid={$writer->login()}">
                                <img src="{$writer->image()|image:'micro'|smarty:nodefaults}" 
                                     title="{$writer->displayName()}"/>
                                </a>
                            {/if}

                            <a href="activity/timetable/{$activity->id()}">
                                <b> {$activity->title()} </b>
                            </a>
                        </div>
                    {/foreach}
                </div>
            </div>
        {/foreach}
    </div>
</div>

{js src="activities.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
