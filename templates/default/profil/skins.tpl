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

<form enctype="multipart/form-data" method="post" action="profil/skin">
        <table>
            <tr>
                <th></th>
                <th>Frequence</th>
                <th>Nom</th>
                <th>Description</th>
            </tr>
            {foreach from=$skinsList item=someskin}
                <tr>
                    <td>
                        <input type="radio" name="skin" value="{$someskin.name}" 
                            {if $skin == $someskin.name} checked {/if} />
                    </td>
                    <td>{$someskin.frequence}</td>
                    <td>{$someskin.label}</td>
                    <td>{$someskin.description}</td>
                </tr>
            {/foreach}
        </table>
        <input type="submit" value="Changer de skin" />
    </fieldset>
</form>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
