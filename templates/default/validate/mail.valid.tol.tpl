{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010 Binet Réseau                                       *}
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

{if $text == null}
Ta photo tol vient d'être validée... Elle remplace dès à present visible.
Si tu as l'impression que ta photo n'a pas changé, n'oublie pas de recharger le cache de ton navigateur (Ctrl+F5 en général).

{$comm|smarty:nodefaults}


Cordialement,
Le Webmestre de Frankiz
{else}
Ta photo tol n'a pas été validée pour la raison suivante :
{$comm|smarty:nodefaults}


Désolé 

Cordialement,
Le Webmestre de Frankiz
{/if}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
