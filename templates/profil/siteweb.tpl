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

{assign var=siteweb_path value="`$globals->paths->pagespersos`/`$smarty.session.loginpoly`-`$smarty.session.promo`"}

{if isset($demande_ext|smarty:nodefaults)}
<span class='note'>Ta demande a été enregistrée, elle sera validée dans les meilleurs délais.</span>
{/if}

{if isset($siteweb_updated|smarty:nodefaults)}
<span class='note'>Ton site web a été mis à jour.</span>
{/if}

<span class='note'>
  Tu peux soumettre des archives .zip, .tar.gz, .tar ou .tar.bz2 qui seront décompressées.<br />
  Tu remplacera ainsi l'intégralité de ton site perso. <br />
  Attention, tu es limité à 10Mo. <br />
</span>

<form method='post' enctype='multipart/form-data' action='profil/siteweb/upload'>
  <div class='formulaire'>
    <input type='hidden' id='MAX_FILE_SIZE' value='10000000' />
    <span class='gauche'>Ton site :</span>
    <span class='droite'><input type='file' name='file' /></span>
    <span class='boutons'><input type='submit' value='Upload' /></span>
  </div>
</form>

{if is_dir($siteweb_path)}
<span class='note'>Nous te conseillons de sauvegarder ton site avant d'uploader le nouveau en cas de problème.</span>
<a href='profil/siteweb/download/zip'>Télécharger en zip</a><br />
<a href='profil/siteweb/download/tar.gz'>Télécharger en tar.gz</a><br />

<span class='note'>
  Si tu souhaites que ton site apparaisse sur la liste des sites élèves visibles de l'extérieur, clique sur le bouton "Extérieur". Cette demande est soumise à validation.<br />
  Dans tous les cas, ton site sera listé sur la liste des sites perso accessibles pour les gens loggués.
</span>
<form method='post' action='profil/siteweb/demande_ext'>
  <input type='submit' value='Extérieur' />
</form>
{/if}

{if is_dir($siteweb_path)}
<h2>Contenu actuel du site web.</h2>
{print_dir dir=$siteweb_path}
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
