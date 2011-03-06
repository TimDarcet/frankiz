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
                        {$writer|user}
                    {/if}

                    <a href="activity/timetable/{$visibility}/{$activity->id()}">
                        <b> {$activity->title()} </b>
                    </a>
                </div>
            {/foreach}
        </div>
    </div>
{/foreach}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
