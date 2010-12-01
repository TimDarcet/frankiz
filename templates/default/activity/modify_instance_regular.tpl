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
   Activité Régulière
</div>

<table>
    <tr>
        <td width=20%>
            Titre :
        </td>
        <td>
            {$activity->title()}
        </td>
    </tr>

    <tr>
        <td>
            Description :
        </td>
        <td>
            {$activity->description()}
        </td>
    </tr>

    <tr>
        <td>
            Commentaire :
        </td>
        <td>
            <textarea name='comment' id="text_proposal" rows=7 cols=50>{$activity->comment()}</textarea>
        </td>
    </tr>

    <tr>
        <td>
            Début :
        </td>
        <td>
            <input type='text' name='begin' value="{$activity->begin()|datetime:'Y-m-d H:i:s'}" />
        </td>
    </tr>

    <tr>
        <td>
            Fin :
        </td>
        <td>
            <input type='text' name='end' value="{$activity->end()|datetime:'Y-m-d H:i:s'}" />
        </td>
    </tr>

    <tr>
        <td>
            Privé :
        </td>
        <td>
            {if $activity->priv()}oui{else}non{/if}
        </td>
    </tr>

    <tr>
        <td></td>
        <td>
            <input type="submit" name="modify" value="Modifier" class="hide"/>
            <input type="submit" name="delete" value="Supprimer"/>
        </td>
    </tr>

</table>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}