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
{if $valid && $valid_origin}
Le groupe d'origine de ton annonce vient d'être confirmé. L'annonce va désormais passer pour validation au groupe destinataire.
Si tu veux accélérer le processus la prochaine fois, demande à être passé administrateur de ton groupe.
{if $comm != ''}

{$comm|smarty:nodefaults}
{/if}
{elseif $valid}
Ton annonce vient d'être validée. Tu peux la consulter à cette adresse: 

{$globals->baseurl}/news/mine
{if $comm != ''}

{$comm|smarty:nodefaults}
{/if}


{else}
{if $valid_origin}
L'administrateur du groupe au nom duquel tu voulais publier ton annonce l'a refusée pour la raison suivante :
{else}
Ton annonce n'a pas été validée pour la raison suivante :
{/if}
{$comm|smarty:nodefaults}


Voici le texte que tu avais proposé :
{$text|smarty:nodefaults}


Désolé
{/if}

Cordialement,
{if $valid_origin}
Les administrateurs du groupe "{$origin->label()}"
{else}
Les administrateurs du groupe "{$targetGroup->label()}"
{/if}

Ceci est un mail automatique, merci de ne pas y répondre. Pour toute question ou remarque, merci de contacter web@frankiz.net .
{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
