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

<div class="options">
    <table><tr>
        <td>
            <div class="option actions">
                Actions :
                <ul>
                    <li class="open_all_unread"><a>Ouvrir toutes les annonces non-lues</a></li>
                    <li class="read_all"><a>Tout marquer comme lu</a></li>
                    <li class="open_all"><a>Tout ouvrir</a></li>
                    <li class="close_all"><a>Tout fermer</a></li>
                </ul>
            </div>
        </td>
        <td>
            <div class="option display">
                Vue :
                <ul>
                    <li><a href="news">Annonces courantes</a></li>
                    <li><a href="news/mine">Les annonces que j'ai rédigées</a></li>
                </ul>
            </div>
        </td>
        <td>
            <div class="option codes">
                Code couleur :
                <ul>
                    <li><div class="code unread"></div>Non-lue</li>
                    <li><div class="code read"></div>Lue</li>
                    <li><div class="code star"></div>Suivie</li>
                    {if $view == 'mine'}
                        <li title="visibles seulement par les administrateurs avant leur publication"><div class="code tocome"></div>À venir</li>
                    {/if}
                </ul>
            </div>
        </td>
    </tr></table>
</div>

<div class="list">
    {include file="news/subnews.tpl"|rel collection=$news}
</div>

{js src="news.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
