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


<div class="title">
   Activité : {$activity->title()}
</div>

<table>
    <tr>
        <td width=20%>
            Participants :
        </td>
        <td>
            {$activity->participants()|@count}
        </td>
    </tr>
    {foreach from=$activity->participants() item=participant}
        <tr>
            <td>
                Nom :
            </td>
            <td>
                {$participant->displayName()}
            </td>
        </tr>
    {/foreach}
</table>

{assign var='writer' value=$activity->writer()}
{if $writer->id() == $user->id()}
    <div class="subtitle">
       Envoyer un mail aux inscrits
    </div>
    <table>
        <tr>
            <td width=20%>
                Contenu :
            </td>
            <td>
                <textarea name="mail_body" rows=7 cols=50></textarea>
            </td>
        </tr>
        <tr>
            <td width=20%>
                Envoyer :
            </td>
            <td>
                <input type="submit" name="mail" value="Valider"/>
            </td>
        </tr>
    </table>
{/if}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}