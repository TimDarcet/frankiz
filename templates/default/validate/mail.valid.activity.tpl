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
{if $isok && $valid_origin}
Le groupe d'origine de ton activité vient d'être confirmé. L'activité va désormais passer pour validation au groupe destinataire.
Si tu veux accélérer le processus la prochaine fois, demande à être passé administrateur de ton groupe.
{if $comm != ''}

{$comm|smarty:nodefaults}
{/if}

{elseif $isok}
Ton activité vient d'être validée. Elle est dès à present visible.
{if $comm != ''}

{$comm|smarty:nodefaults}
{/if}


Merci de ta participation
{else}
Ton activité n'a pas été validée pour la raison suivante :
{$comm|smarty:nodefaults}


Désolé
{/if}

Cordialement,
{if $valid_origin}
Les administrateurs du groupe "{$origin->label()}"
{else}
Les administrateurs du groupe "{$targetGroup->label()}"
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
