{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet RÃ©seau                                       *}
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

<fieldset id="tol_searcher">
    <legend>Rechercher sur le trombino</legend>
    <form class="trombino" enctype="multipart/form-data" method="post" action="tol/">
    <fieldset>
    <ul>
        <li><label>Prénom<input type="text" name="firstname" value="" /></label></li>
        <li><label>Nom<input type="text" name="lastname" value="" /></label></li>
        <li><label>Surnom<input type="text" name="nickname" value="" /></label></li>
        <li><label>Nationalite<input type="text" name="nickname" value="" /></label></li>
    </ul>
    </fieldset>
    <fieldset>
    <ul>
        <li><label>Ecole<input type="text" name="firstname" value="" /></label></li>
        <li><label>Promo<input type="text" name="lastname" value="" /></label></li>
    </ul>
    </fieldset>
    <fieldset>
    <ul>
        <li><label>Binet<input type="text" name="firstname" value="" /></label></li>
    </ul>
    </fieldset>
    <fieldset>
    <ul>
        <li><label>Casert<input type="text" name="firstname" value="" /></label></li>
        <li><label>Tel<input type="text" name="firstname" value="" /></label></li>
        <li><label>IP<input type="text" name="firstname" value="" /></label></li>
    </ul>
    </fieldset>
    <fieldset>
        <input type="reset" name="effacer" value="Remise à Zéro" />
        <input type="submit" name="chercher" value="Chercher" />
    </fieldset>
    </form>
</fieldset>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
