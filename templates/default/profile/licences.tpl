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

<h2><span>Mes Licences</span></h2>
  <table class="liste">
    <tr>
      <td class="entete">Logiciel</td>
      <td class="entete">Licence</td>
      <td class="entete"> </td>
    </tr>
    {foreach from=$licences item=licence}
    <tr>
      <td class="element">{$licence.nom_logiciel}</td>
      <td class="element">{$licence.cle}</td>
      <td class="element">{if !$licence.attrib}Demande en attente{/if}</td>
    </tr>
    {/foreach}
  </table>

<h2><span>Demande de licence</span></h2>
<p class="note">Dans le cadre de l'accord MSDNAA, chaque étudiant de polytechnique a le droit à une version de Windows XP Pro et une de Windows Vista Business gratuites, légales et attibuées à vie.<br />
Si tu as besoin d'une clé pour un logiciel téléchargé sur ftp://enez/, et qu'il n'est pas proposé dans la liste, envoi un mail aux <a href="mailto:msdnaa-licences@frankiz.polytechnique.fr">Admins Windows</a>.</p>

<form method="post" action="profil/licences/cluf" id="licences_liste_logiciels">
  <h3>Tu peux demander une licence pour :</h3>
  <div class="formulaire">
    <div>
      <span class="gauche">Demander une licence pour :</span>
      <span class="droite">
        <select name="logiciel">
        {foreach from=$logiciels key=k item=logiciel}
          <option value="{$k}">{$logiciel}</option>
        {/foreach}
        </select>
      </span>
    </div>
    <div>
      <span class="boutons">
        <input type="submit" name="valid" value="Demander">
      </span>
    </div>
  </div>
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
