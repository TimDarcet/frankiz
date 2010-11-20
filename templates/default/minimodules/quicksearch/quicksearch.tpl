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

<div class="formulaire">
Trombino:
    <form enctype="multipart/form-data" method="post" action="tol/">
        <input type="text" name="free" value="" />
        <input type="submit" name="ok" value="Chercher" />
    </form>
</div>

<div class="formulaire">
Wikix:
    <form enctype="multipart/form-data" method="post" action="http://wikix.polytechnique.org/eleves/wikix/Sp%C3%A9cial:Recherche">
        <input name="go" value="Consulter" type="hidden">
        <input name="search" value="" type="text">
        <input name="ok" value="Chercher" type="submit">
    </form>
</div>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
