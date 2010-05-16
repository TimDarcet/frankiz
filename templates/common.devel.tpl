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

{if #globals.debug#}
@@BACKTRACE@@

{if $validate}
  <div id="dev">
    @HOOK@
    Validation&nbsp;:
    <a href="http://jigsaw.w3.org/css-validator/validator?uri={#globals.baseurl#}/valid.html">CSS</a>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    références&nbsp;:
    <a href="http://www.w3schools.com/xhtml/xhtml_reference.asp">XHTML</a>
    <a href="http://www.w3schools.com/css/css_reference.asp">CSS2</a>
  </div>
{/if}
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}

