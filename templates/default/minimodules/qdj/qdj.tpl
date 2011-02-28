{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet Réseau                                       *}
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

{assign var='qdj' value=$minimodule.qdj}

<div class="navigation">
    <span class="before"><a onclick="qdj.backward();">&lt;-</a></span>
    <span class="after"><a onclick="qdj.forward();">-&gt;</a></span>
    <span class="date">{$qdj->date()|datetime:'Y-m-d'}</span>
</div>

<div class="qdj">
    <div class="question">{$qdj->question()}</div>
    <table>
        <tr class="answers">
            <td class="case1 jone">
                <a onclick="qdj.vote(1);">{$qdj->answer1()}</a>
            </td>
            <td class="case2 rouje">
                <a onclick="qdj.vote(2);">{$qdj->answer2()}</a>
            </td>
        </tr>
        <tr class="counts" {if !$qdj->hasVoted()}style="display: none"{/if}>
            <td class="case1 jone">
                {if $qdj->hasVoted()}
                    <div class="count" style="height: {$qdj->percentage1()}%">{$qdj->count1()}</div>
                {else}
                    <div class="count"></div>
                {/if}
            </td>
            <td class="case2 rouje">
                {if $qdj->hasVoted()}
                    <div class="count" style="height: {$qdj->percentage2()}%">{$qdj->count2()}</div>
                {else}
                    <div class="count"></div>
                {/if}
            </td>
        </tr>
    </table>

    <table class="last_votes">
        <tr>
            <td>
                Derniers votes :
            </td>
            <td class="votes_list">
                {foreach from=$minimodule.votes item=vote}
                    {$vote.rank}. {$vote.user} <br />
                {/foreach}
            </td>
        </tr>
    </table>
</div>

<div class="toModule">
    <table><tr>
        <td>
            <a href="qdj">Classement QDJ</a>
        </td>
        <td>
            <a href="proposal/qdj">Proposer une QDJ</a>
        </td>
    </tr></table>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}