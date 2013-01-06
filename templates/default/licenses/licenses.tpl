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
<div class="module">
<div class="head">{$title}</div>
<div class="body">
    
			{include file="wiki.tpl"|rel name='licenses'}
            {if count($owned_licenses) != 0}
            <h3>Les licences suivantes t'ont déjà été attribuées :</h3>
            <ul>
                {foreach from=$owned_licenses item=licence}
                <li>
                    <form action="licenses/final" method="POST">
                        {$licence->softwareName()}
                        <input type="hidden" name="software" value="{$licence->software()}" />
                        <input type="hidden" name="id" value="{$licence->id()}" />
                        <input type="submit" name="resend" value="Recevoir à nouveau ma clé" />
                    </form>
                </li>
                {/foreach}
            </ul>
            {/if}
        <form action="licenses/cluf" method="POST">
            <h3>Les licences disponibles</h3>
			<select name="software">
            {foreach from=$softwares item=soft key=id}
                <option value="{$id}">{$soft}</option>
            {/foreach}
			</select>	
			<input type='submit' id='valid' titre='Envoyer'/>
		</form>
    </div>
</div>
{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
