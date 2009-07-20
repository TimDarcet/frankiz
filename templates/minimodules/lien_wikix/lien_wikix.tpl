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

{if ip_internal()}
{assign var=wikix_url value="http://frankiz.polytechnique.fr/eleves/wikix"}
{else}
{assign var=wikix_url value="http://www.polytechnique.fr/eleves/wikix"}
{/if}

<form enctype="multipart/form-data" method="post" id="lien_wiki_x" action="{$wikix_url}/Special:Search">
  <div class="formulaire">
    <input type="hidden" name="go" value="Consulter" />
    <input type="text" id="lien_wiki_xsearch" name="search" value="" />
    <input type="submit" name="ok" value="Chercher"/>
  </div>
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
