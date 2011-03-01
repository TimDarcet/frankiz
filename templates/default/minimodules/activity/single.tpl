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


<div class="title {if $activity->participate()}present{else}away{/if}">
    <a href="activity/timetable/friends/{$activity->id()}">{$activity->title()}</a>
    {if $activity->participate()}
        <a onclick="activity.out({$activity->id()});" class="right"><span class="remove_participant"></span></a>
    {else}
        <a onclick="activity.present({$activity->id()});" class="right"><span class="add_participant"></span></a>
    {/if}
</div>
<div class="body">
    {if ($activity->hour_begin($day) == false) && ($activity->hour_end($day) == false)}
        Toute la journée
    {elseif $activity->hour_begin($day) == false}
        Jusqu'à {$activity->hour_end()}
    {elseif ($activity->hour_end($day) == false)}
        A partir de {$activity->hour_begin()}
    {else}
        de {$activity->hour_begin()} à {$activity->hour_end()}
    {/if}
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
