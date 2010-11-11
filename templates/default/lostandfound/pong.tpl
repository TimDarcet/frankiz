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

<form enctype="multipart/form-data" method="post" action="laf/">
    <label>Chercher : <input type="text" name="pong_obj" /></label>
    <input type="submit" name="chercher_pong" value="Chercher" />
</form>

{if isset($found|smarty:nodefaults)}    	
    {foreach from=$found item=result}
        <p class="prop_obj"><b>Objet :</b> {$result[4]} </p>
        <div class="prop_desc"><b>Circonstance :</b> {$result[5]}
        <form enctype="multipart/form-data" method="post" action="laf/{$result[0]}">
            <input type="submit" name="pong" value="Trouvé !" />
            {if $result[1]==$uid}
                <input type="submit" name="del_pong" value="Supprimer" />
            {/if}

        </form>
        </div>
    {/foreach}
{/if}
{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
