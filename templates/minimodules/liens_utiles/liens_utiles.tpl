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

<ul class='fkz_liens'>
  <li class='fkz_liens'><a href="contact.php" accesskey="c">Contacter les élèves</a></li>
  {if !$session->est_interne()}
  <li class='fkz_liens'><a href="plan.php">Venir à l'X</a></li>
  {/if}
  {if $session->est_auth()}
  <li class='fkz_liens'><a href="http://de.polytechnique.fr/index.php?page=edt">Emploi du temps</a></li>
  <li class='fkz_liens'><a href="profil/licences">Licences Msdnaa</a></li>
  <li class='fkz_liens'><a href="http://poly.polytechnique.fr">Redirection des mails</a></li>
  {/if}
  {if $session->est_interne()}
  <li class='fkz_liens'><a href="http://ircserver.eleves.polytechnique.fr/">Accéder à l'IRC</a></li>
  {/if}
  <li class='fkz_liens'><a href="http://www.polytechnique.fr/eleves/binets/reseau">Docs BR</a></li>
  <li class='fkz_liens'><a href="http://www.polytechnique.fr/">Site de l'école</a></li>
  <li class='fkz_liens'><a href="http://www.edu.polytechnique.fr/">Site de la DE</a></li>
  <li class='fkz_liens'><a href="http://www.polytechnique.fr/sites/orientation4a/pages_orientation/">Orientation 4eme année</a></li>
  {if $session->est_interne() || $session->est_auth()}
  <li class='fkz_liens'><a href="http://intranet.polytechnique.fr/" accesskey="i">Intranet</a></li>
  {/if}
  <li class='fkz_liens'><a href="http://www.polytechnique.org/" accesskey="o">Polytechnique.org</a></li>
  <li class='fkz_liens'><a href="http://www.polytechnique.net/" accesskey="n">Polytechnique.net</a></li>
  {if $session->est_interne() || $session->est_auth()}
  <li class='fkz_liens'><a href="partenaires.php">Partenariats</a></li>
  {/if}
</ul>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
