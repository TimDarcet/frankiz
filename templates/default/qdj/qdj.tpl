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

{js src="plugins/enhance.js"}
{js src="plugins/excanvas.js"}
{js src="plugins/visualize.jQuery.js"}

{js src="qdj.js"}

<div class="comment_qdj module">
    <div class="head">
        <span class="helper" target="qdj"> </span>
        Classement QDJ
    </div>
    <div class="body">

        <form enctype="multipart/form-data" method="post" action="qdj" id="qdj_form">
            {assign var='end' value=$begin_date|@count}
            <select name="period">
                <option value="now">
                    La période actuelle
                </option>
                <option value="all">
                    Tous les scores
                </option>
                {section name=form start=0 loop=$end}
                    <option value="{$smarty.section.form.index}">
                        {$begin_date[$smarty.section.form.index]|date_format} au
                        {$end_date[$smarty.section.form.index]|date_format}
                    </option>
                {/section}
            </select>
            <input type="submit" name="send" value="Afficher">
        </form>

        <div id="qdj_ranking">
            {include file="qdj/ranking.tpl"|rel}
        </div>
     </div>
</div>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
