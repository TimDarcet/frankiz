{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet RÃ©seau                                       *}
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

<fieldset>
    <legend>Objets trouvés</legend>
    <form class="trombino" enctype="multipart/form-data" method="post" action="laf/">
        <label>Chercher : <input type="text" name="pong_obj" /></label>
        <input type="submit" name="chercher_pong" value="Chercher" />
    </form>

    {if isset($found|smarty:nodefaults)}
    <ul id="laf_pong">
    <table>
    	<tr><th> Objet </th> <th> Circonstances </th> <th> Propriétaire </th> <th> Suppression de l'objet </th></tr>
        {foreach from=$found item=result}
            <tr> <td> {$result[4]} </td> <td> {$result[5]} </td> <td> 
    			<form class="trombino" enctype="multipart/form-data" method="post" action="laf/{$result[0]}">
    				<input type="submit" name="pong" value="Trouvé !" />
   				</form>
 				</td> <td> 
    			<form class="trombino" enctype="multipart/form-data" method="post" action="laf/{$result[0]}">
    				<input type="submit" name="del_ong" value="Supprimer" />
   				</form>
 				</td>
			</tr>
        {/foreach}
    </table>
    </ul>
    {/if}
</fieldset>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}