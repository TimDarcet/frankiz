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

{literal}
<script id="activity_template" type="text/x-jquery-tmpl">
    <div class="head">
        <div class="close_show"></div>
        <span class="origin">
            {{if origin}}
                <a href="groups/see/${origin.name}">
                    {{if origin.image}}
                        <img src="${origin.image}" title="${origin.label}">
                    {{else}}
                        [ ${origin.label} ]
                    {{/if}}
                </a>
            {{else}}
                <a href="tol/see/${writer.login}">
                    <img src="${writer.photo}" title="${writer.displayName}">
                </a>
            {{/if}}
        </span>
        <span class="title">${title}</span>
    </div>
    <div class="body">
        {{if begin.toLocaleDateString() == end.toLocaleDateString()}}
            <div class="one_day">
                <div class="date">
                    ${begin.toLocaleDateString()}
                </div>
                <div class="time">
                    de
                    <span class="hour_begin">
                        ${begin.toLocaleTimeString()}
                    </span>
                    à
                    <span class="hour_end">
                        ${end.toLocaleTimeString()}
                    </span>
                </div>
            </div>
        {{else}}
            <div class="several_days">
                <div class="begin">
                    du
                    <span class="date">
                        ${begin.toLocaleDateString()}
                    </span>
                    à
                    <span class="hour">
                        ${begin.toLocaleTimeString()}
                    </span>
                </div>
                <div class="end">
                    au
                    <span class="date">
                        ${end.toLocaleDateString()}
                    </span>
                    à
                    <span class="hour">
                        ${end.toLocaleTimeString()}
                    </span>
                </div>
            </div>
        {{/if}}

        <div class="msg">
        </div>

        {{if participate}}
            <div class="out">
                <a><span class="remove_participant"></span>Se désinscrire</a>
            </div>
        {{else}}
            <div class="present">
                <a><span class="add_participant"></span>S'inscrire</a>
            </div>
        {{/if}}

        <div class="section">
            Description :
        </div>
        <div class="description">
            {{html description}}
        </div>
        <div class="comment">
        </div>

        <div class="section participants_list">
            Participants :
            <span class="number">
                ${participants.length}
            </span>
        </div>
        <div class="participants">
            ${participants.join(', ')}
        </div>

        <div class="misc">
            {{if isWriter}}
                <div class="mail">
                    <a href="activity/participants/${id}"><div class="mail_ico"></div>Mail</a>
                </div>
            {{else}}
                <div class="participants_link">
                    <a href="activity/participants/${id}"><div class="group_ico"></div>Participants</a>
                </div>
            {{/if}}
            {{if canEdit}}
                <div class="admin">
                    <a href="activity/modify/${id}"><div class="edit"></div>Modifier</a>
                </div>
            {{/if}}
        </div>
    </div>
</script>
{/literal}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
