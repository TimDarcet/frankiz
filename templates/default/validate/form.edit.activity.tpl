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

<tr>
    <td>
        Titre :
    </td>
    <td>
        <input type='text' name='title' value="{$item->title()}" />
    </td>
</tr>
    
<tr>
    <td>
        Description :
    </td>
    <td>
        <textarea name='desc' rows=30 cols=50>{$item->desc()}</textarea>
    </td>
</tr>
            
<tr>
    <td>
        Image :
    </td>
    <td>
        A faire quand les classes de Riton seront pretes
    </td>
</tr>
            
<tr>
    <td>
        Date :
    </td>
    <td>
        <input type='text' name='date' value="{$item->date()}" />
    </td>
</tr>
            
<tr>
    <td>
        Toutes les semaines :
    </td>
    <td>
        <input type="checkbox" name="regular" id="regular_activity_proposal" {if $item->regular()}checked="checked"{/if}/>
    </td>
</tr>
            
<tr id="number_activity_proposal">
    <td>
        Nombre de semaines :
    </td>
    <td>
        <input type='text' name='number' value="{$item->number()}" />
    </td>
</tr>
   
<tr>
    <td>
        Heure de début :
    </td>
    <td>
        <input type='text' name='begin' value="{$item->begin()}" />
    </td>
</tr>
   
<tr>
    <td>
        Heure de fin :
    </td>
    <td>
        <input type='text' name='end' value="{$item->end()}" />
    </td>
</tr>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
