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

{if $logged}
    <div class="options">
        <table><tr>
            <td>
                <div class="option actions">
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
                    <ul>
                        <li><a href="news/new" {if $view == 'new'}class="current_view"{/if}>Non-lues & suivies</a></li>
                        <li><a href="news/current" {if $view == 'current'}class="current_view"{/if}>En cours</a></li>
                        <li><a href="news/mine" {if $view == 'mine'}class="current_view"{/if}>Annonces que j'ai rédigées</a></li>
                        <li><a href="news/other" {if $view == 'other'}class="current_view"{/if}>Annonces des autres binets</a></li>
                    </ul>
                </div>
            </td>
            <td>
                <div class="option new">
                    <a href="proposal/news"><span class="new_element"></span> Rédiger une annonce</a>
                </div>
                <div class="option codes">
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
{/if}

<div class="list">
    {include file="news/subnews.tpl"|rel collection=$news}
</div>

<script>
    window.location = String(window.location).replace(/\#.*$/, "") + "#news_{$selected_id}";
</script>
{js src="news.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
