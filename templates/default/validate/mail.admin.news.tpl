{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010-2013 Binet Réseau                                  *}
{*  http://br.binets.fr/                                                  *}
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
Bonjour,
{if $valid_origin}
{$user->displayname()|smarty:nodefaults} a demandé à ce que son annonce "{$title|smarty:nodefaults}" soit au nom du groupe :
{$origin->label()}

Pour valider ou non cette demande va sur la page suivante :

{$globals->baseurl}/admin/validate/{$origin->name()}

Tu reçois ce mail, car tu es un administrateur du groupe "{$origin->label()}".
Si tu veux que cette personne puisse écrire directement des annonces au nom du groupe, donne lui les droits d'administrateur.
{else}
{$user->displayname()|smarty:nodefaults} a demandé la validation d'une annonce :
{$title|smarty:nodefaults}

Pour valider ou non cette demande va sur la page suivante :

{$globals->baseurl}/admin/validate/{$targetGroup->name()}

Tu reçois ce mail, car tu es un administrateur du groupe "{$targetGroup->label()}"
{/if}


Cordialement,
Les Webmestres de Frankiz

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
