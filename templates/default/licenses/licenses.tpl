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
    {if count($owned_licenses) != 0}
    <h2>Les licences suivantes t'ont déjà été attribuées :</h2>
    <ul>
        {foreach from=$owned_licenses item=licence}
        <li>{$licence->softwareName()}
            <form action="licenses/final" method="POST">
                <input type="hidden" name="software" value="{$software}" />
                <input type="submit" name="resend" value="Recevoir à nouveau ma clé" />
            </form>
        </li>
        {/foreach}
    </ul>
    {/if}
    <form action="licenses/cluf" method="POST">
			<note>Dans le cadre de l'accord MSDNAA, chaque étudiant de polytechnique à le droit à une version de Windows XP Pro, une de Windows Vista Business ainsi qu'une de Windows Seven Professional gratuites, légales et attibuées à vie</note>
			<note>Si tu as besoin d'une clé pour un logiciel téléchargé sur ftp://enez/, et qu'il n'est pas proposé dans la liste, envoi un mail aux <a href="mailto:msdnaa-licences@frankiz.polytechnique.fr">Admins Windows</a>.</note>
			<p>Les licences disponibles</p>
			<select name="software">
            {foreach from=$softwares item=soft key=id}
                <option value="{$id}">{$soft}</option>
            {/foreach}
			</select>	
			<input type='submit' id='valid' titre='Envoyer'/>
		</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
