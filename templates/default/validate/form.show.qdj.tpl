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

<div class="title">
    QDJ
</div>
<div class="qdj">
    <div class="question">
        {if $question} {$question} {else} {$item->question} {/if}
    </div>
    <table>
    <tr class="answers">
        <td class="answer1" width=50%>
            {if $answer1} {$answer1} {else} {$item->answer1} {/if}
        </td>
        <td class="answer2">
            {if $answer2} {$answer2} {else} {$item->answer2} {/if}
        </td>
    </tr>
    </table>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}