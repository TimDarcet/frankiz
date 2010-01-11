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

<div class="navigation">
    <span class="before"><a onclick="backward();"><-</a></span>
    <span class="after"><a onclick="forward();">-></a></span>
    <span class="date">{$minimodule.date}</span>
</div>

<div class="qdj">
    <div class="question">{$minimodule.question}</div>
    
    <table>
        <tr class="answers">
            <td class="case1">
                <a onclick="vote(1);">{$minimodule.answer1}</a>
            </td>
            <td class="case2">
                <a onclick="vote(2);">{$minimodule.answer2}</a>
            </td>
        </tr>
        <tr class="counts">
            <td class="case1">
                <div class="count"></div>
            </td>
            <td class="case2">
                <div class="count"></div>
            </td>
        </tr>
    </table>

    <div class="voters">Derniers à répondre :
        <ul>
            <li>2. Brice SLAMA</li>
            <li>1. Brice SLAMA</li>
        </ul>
    </div>
</div>
    
<div class="toModule">
    <a href="qdj">Classement QDJ</a>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}