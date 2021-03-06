{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009-2013 Binet Réseau                                  *}
{*  http://br.binets.fr/                                                  *}
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

<tr>
    <td></td>
    <td>
        <table>
            <tr>
                <td>
                    Logiciel :
                </td>
                <td>
                    {$item->softwareName()}
                </td>
            <tr>
                <td>
                    Raison : 
                </td>
                <td>
                    {$item->reason()}
                </td>
            </tr>
            {if !$item->canGetFreeKey()}
            <tr>
                <td colspan="2">
                    <strong>Plus de clés disponibles !</strong>
                </td>
            </tr>
            {/if}
            {if !$item->userHasRights()}
            <tr>
                <td colspan="2">
                    <strong>L'utilisateur ne devrait normalement pas être autorisé à avoir une licence !</strong>
                </td>
            </tr>
            {/if}
            </table>
        </div>
    </td>
</tr>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
