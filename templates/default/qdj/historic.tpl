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

<div class="module">
    <div class="head">
        Historique des QDJs
    </div>
    <div class="body">
        {foreach from=$qdjs item=qdj}
            <div class="qdj_item">
                <div class="date">
                    QDJ du {$qdj->date()|datetime:'Y-m-d'}
                </div>
                <div class="question">
                    {$qdj->question()}
                </div>
                <div class="answer">
                    - {$qdj->answer1()} ({$qdj->count1()} soit {$qdj->percentage1()}%)
                </div>
                <div class="answer">
                    - {$qdj->answer2()} ({$qdj->count2()} soit {$qdj->percentage2()}%)
                </div>
            </div>
        {/foreach}
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
