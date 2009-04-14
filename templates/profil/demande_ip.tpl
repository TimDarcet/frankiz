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

{if $nouvelle_demande}
<span class='note'>
  Nous avons bien pris en compte ta demande d'enregistrement de machine. Nous allons la traiter dans les plus brefs délais.
</span>
{else}
{if $demande_en_cours}
<span class='warning'>
  Tu as déja fait une demande d'enregistrement d'une nouvelle machine. Attends que le BR te valide la première pour en faire une seconde si cela est justifié.
</span>
{/if}
{/if}
<form enctype='multipart/form-data' method='post' id='demandeip' action='profil/reseau/demande_ip'>
  <span class='note'>
    Si tu as juste changé d'ordinateur, tu peux garder la même IP et la même configuration réseau. Tu n'as donc pas à demander une nouvelle IP !
  </span>
  <div class='formulaire'>
    <div>
      <span class='gauche'>
        Je fais cette demande parce que:
      </span>
      <span class='droite'>
        <input type='radio' name='type' value='1' checked='checked' /> J'ai installé un 2ème ordinateur dans mon casert et je souhaite avoir une nouvelle adresse IP pour cette machine.<br />
        <input type='radio' name='type' value='2' /> Autre raison (précise ci-dessous) :<br />
      </span>
    </div>
    <div>
      <span class='gauche'>
        Raison:
      </span>
      <span class='droite'>
        <textarea name='raison' rows='7' cols='50'></textarea>
      </span>
    </div>
    <div>
      <span class='boutons'>
        <input type='submit' name='demander' value='Demander une nouvelle IP' />
      </span>
    </div>
  </div>
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
