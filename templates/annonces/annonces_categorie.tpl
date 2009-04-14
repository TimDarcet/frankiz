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

{foreach from=$annonces.$categorie.annonces item=annonce}
<div class="fkz_annonces_1">
  <div class="fkz_annonces_2">
    <div class="fkz_annonces_3">
      <div class="fkz_annonces_4">
        <div class="fkz_annonces_5">
          <div class="fkz_annonces_6">
            <div class="fkz_annonces">
	      <div class="fkz_annonces_titre"><b>
	        <span class="fkz_annonces_{$categorie}"></span>
		<span class="fkz_annonces_cat">({$categorie})</span>
		<span class="fkz_annonces_titre">{$annonce.titre}</span>
	      </div></b>
	      <div class="fkz_annonces_corps">
                {if $annonce.img}
                <span class="image" style="display:block;text-align:center">
		  <img src="http://frankiz/data/annonces/{$annonce.id}" alt="logo" />
		</span>
                {/if}
		{*{$annonce.contenu|wiki_vers_html}*}
		{$annonce.contenu}
		<p class="fkz_signature">{print_eleve_name eleve=$annonce.eleve show_promo=1}</p>
	      </div>
	    </div>
	  </div>
	</div>
      </div>
    </div>
  </div>
</div>
{/foreach}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
