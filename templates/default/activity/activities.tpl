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
    <div class="top" id="top_prop">
        <a href="proposal/activity">
            <span class="new_element"></span>
                Proposer une activité
            </a>
    </div>


    <div class="top" id="top_view">
        <a onclick="change_view('participate')" class="{if $view == 'participate'}current{/if}">
            <span class="alone"></span>
            Activités auxquelles je participe
        </a> <br />

        <a onclick="change_view('friends')" class="{if $view == 'friend'}current{/if}">
            <span class="group_ico"></span>
            Activités de mes groupes
        </a> <br />

        <a onclick="change_view('all')" class="{if $view == 'all'}current{/if}">
            <span class="world"></span>
            Toutes les activités
        </a>
    </div>

    <div id="get_place"></div>

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
        {include file='activity/activities_list.tpl'|rel activities=$activities}
    </div>
</div>

{js src="activities.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
