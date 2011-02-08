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

{if $origin}
    <tr>
        <td>
            Groupe d'origine :
        </td>
        <td>
            {assign var='origin' value=$item->origin()}
            {$origin|group}
        </td>
    </tr>
{/if}

<tr>
    <td>
        Titre :
    </td>
    <td>
        {$item->title()}
    </td>
</tr>
    
<tr>
    <td>
        Description :
    </td>
    <td>
        {$item->description()}
    </td>
</tr>
            
<tr>
    <td>
        Date :
    </td>
    <td>
        de {$item->begin()|datetime:'Y-m-d H:m'} à {$item->end()|datetime:'Y-m-d H:m'}
    </td>
</tr>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
