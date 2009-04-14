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

{config_load file=mails.conf section=siteweb_ext}
{if $mail_part eq "head"}
{from full=#from#}
{to full=#to#}
{subject text="[Frankiz] Demande de page perso de `$prenom` `$nom`"}
{elseif $mail_part eq "html"}
{$prenom} {$nom} a demandé que sa page perso apparaisse sur la liste des sites personnels. <br/>
<br />
Pour valider ou non cette demande, va sur la page suivante :<br />

<div align='center'><a href='{$globals->baseurl}/admin/valid_pageperso.php'>{$globals->baseurl}/admin/valid_pageperso.php</a></div>
<br />
<br />
Cordialement,<br />
Les Webmestres de Frankiz<br />
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
