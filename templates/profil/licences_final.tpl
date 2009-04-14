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

<h2><span>Envoi de la clé de ta licence</span></h2>
{if $already_has}
  <p class="warning">Tu as déjà une clé pour ce logiciel. Tu peux visualiser la liste de tes licences sur <a href="profil/licences">cette page</a>.</p>
{elseif $already_asked}
  <p class="warning">Tu as déjà fait une demande pour ce logiciel. Le BR va t'envoyer ta clé de licence prochainement.</p>
{else}
  <p class="note">Ta demande a bien été prise en compte. Le BR va bientôt t'envoyer ta nouvelle clé pour {$logiciel_nom}.</p>
{/if}
<span><a href="profil/licences">Retour à la liste des licences</a></span>
{print_r($mail)}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
