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

<ul>
    {if $smarty.session.auth < AUTH_INTERNAL}
        <li><a href="contact">Contacter les élèves</a></li>
        <li><a href="plan.php">Venir à l'X</a></li>
        <li><a href="partnerships">Partenariats</a></li>
    {/if}
    {if $smarty.session.auth >= AUTH_INTERNAL}
        <li><a href="http://www.polytechnique.edu">Site de l'École</a></li>
        <li><a href="http://www.etudes.polytechnique.edu">Site de la DE</a></li>
        <li><a href="http://enex.polytechnique.fr">ENEX</a></li>
        <li><a href="http://www.polytechnique.fr/sites/orientation4a/pages_orientation/">Orientation 4eme année</a></li>
    {/if}
    {if $smarty.session.auth >= AUTH_COOKIE}

    {/if}
</ul>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
