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

<form method="post" action="profil/licences/final" id="licences_raison" accept-charset="UTF-8" enctype="multipart/form-data">
  <input type="hidden" name="logiciel" value="{$logiciel}">
  <h2><span>Motivation de la demande</span></h2>
  <div class="formulaire">
    <div>
    {if $logiciel_rare}
      <span class="droite">
        <span class="warning">Vu le faible nombre de licences que nous possédons pour ce logiciel, il nous faut une raison valable pour te l'attribuer.</span>
      </span>
    {else}
      <span class="droite">
        <span class="warning">Tu ne figures pas dans la liste des personnes ayant droit à une licence dans le cadre du programme MSDNAA</span>
        <p>Seuls les étudiants sur le platâl peuvent faire une demande pour une license Microsoft dans le cadre MSDNAA. s'il s'agit d'une erreur, tu peux le signaler aux admin@windows.</p>
        <p>Si c'est le cas, indique la raison de ta demande :</p>
      </span>
    {/if}
    </div>
    <div>
      <span class="gauche">Raison :</span>
      <span class="droite">
        <textarea name="raison" id="licence_raison_text" rows='7' cols='50'></textarea>
      </span>
    </div>
    <div>
      <span class="boutons">
        <input type="submit" name="valid" value="Valider">
        <input type="submit" name="refus" value="Ne rien faire">
      </span>
    </div>
  </div>
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
