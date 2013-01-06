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

<table>
  {foreach from=$smarty.session.liens_perso key=k item=lien}
  <form method='post' action='profil/liens_perso/del'>
    <tr>
      <td>
        <input type='hidden' name='lien_perso' value='{$k}' />
        {$lien}
      </td>
      <td>
        <input type='submit' value='Supprimer' />
      </td>
    </tr>
  </form>
  {/foreach}
  <form method='post' action='profil/liens_perso/add'>
    <tr>
      <td>
        <input type='text' name='lien_perso'>
      </td>
      <td>
	<input type='submit' value='Ajouter' />
      </td>
    </tr>
  </form>
</table>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
