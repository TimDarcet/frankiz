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

{config_load file="mails.conf" section="licence_cle"}
{if $mail_part eq "head"}
  {from full=#from#}
  {subject text="[Frankiz] Licence pour $logiciel_nom"}
{/if}
{if $mail_part eq "html"}
  <b>Bonjour,</b>
  <p>La clé qui t'a été attribuée pour {$logiciel_nom} est :</p>
  {$cle}<br />
  {if $pub_domaine}
    <p>Avec {$logiciel_nom}, tu disposes maintenant d'une machine qui peut se connecter au domaine. <br />
      Tu trouveras dans l'infoBR les informations te permettant de mener à bien cette opération.<br />
      Grâce au domaine, le réseau de l'X est plus sûr et tes demandes d'assistance seront simplifiées et donc accélérées.</p>
  {/if}
  Très cordialement,<br />
  Le BR.
{/if}
{if $mail_part eq "text"}
  Bonjour,
  La clé qui t'a été attribuée pour {$logiciel_nom} est :
  {$cle}
  {if $pub_domaine}
    
    Avec {$logiciel_nom}, tu disposes maintenant d'une machine qui peut se connecter au domaine Windows.
    Tu trouveras dans l'infoBR les informations te permettant de mener à bien cette opération.
    Grâce au domaine, le réseau de l'X est plus sûr et tes demandes d'assistance seront simplifiées et donc accélérées.
  {/if}

  Très cordialement,
  Le BR
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
