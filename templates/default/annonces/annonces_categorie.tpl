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

<div class="annonces">
    <div class="head">
        <span class="fkz_annonces_{$categorie}"></span>
        <span class="fkz_annonces_cat">({$categorie})</span>
        <span class="fkz_annonces_titre">{$annonce.title}</span>
        {if $logged}
        <span class="fkz_annonces_hideshowlink">
        {if $annonce.show}
        <a href="annonces/hide/{$annonce.id}">Masquer</a>
        {else}
        <a href="annonces/show/{$annonce.id}">Afficher</a>
        {/if}
        </span>
        {/if}
    </div>
    {if $annonce.show}
    <div class="body">
        {if $annonce.img}
        <span class="image" style="display:block;text-align:center">
        <img src="http://frankiz/data/annonces/{$annonce.id}" alt="logo" />
        </span>
        {/if}
        {*{$annonce.content|wiki_vers_html}*}
        {$annonce.content}
        <p class="fkz_signature">{print_eleve_name eleve=$annonce.eleve show_promo=1}</p>
    </div>
    {/if}
</div>

{/foreach}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
