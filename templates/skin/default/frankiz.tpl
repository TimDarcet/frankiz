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
{include file="skin/common.header.tpl" more="skin/default/header.tpl"}
    <body>
        <div id="errorBox" title="Erreur"></div>

        {include file=skin/common.devel.tpl}
        <div id="header">
            <div id="logo">
                <a href="accueil"></a>
            </div>
        </div>
        <div id="center">
            <div class="fkz_droite">
                {include file="minimodule.tpl" module_name="fetes"}
                {include file="minimodule.tpl" module_name="lienTol"}
             {* {include file="minimodule.tpl" module_name="lienIK"} *}
                {include file="minimodule.tpl" module_name="lien_wikix"}
             {* {include file="minimodule.tpl" module_name="tour_kawa"} *}
             {* {include file="minimodule.tpl" module_name="sondages"} *}
                {include file="minimodule.tpl" module_name="qdj"}
             {* {include file="minimodule.tpl" module_name="qdj_hier"} *}
             {* {include file="minimodule.tpl" module_name="meteo"} *}
                {include file="minimodule.tpl" module_name="activites"}
                {include file="minimodule.tpl" module_name="lienspropositions"}
             {* {include file="minimodule.tpl" module_name="liens_perso"} *}
             {* {include file="minimodule.tpl" module_name="liens_contacts"} *}
             {* {include file="minimodule.tpl" module_name="liens_utiles"} *}
             {* {include file="minimodule.tpl" module_name="stats"} *}
                {include file="minimodule.tpl" module_name="anniversaires"}
             {* {include file="minimodule.tpl" module_name="virus"} *}
                <p class="valid">
                    <a href="http://validator.w3.org/check?uri=referer">
                    <span class="valid_html"></span>
                    </a>
                    <a href="http://jigsaw.w3.org/css-validator/check/referer">
                    <span class="valid_css"></span>
                    </a>
                </p>
            </div>
            <div id="navigation">
                {include file="skin/common.menu.tpl"}
            </div>
            <div id="module">
                {include file="content.tpl"}
            </div>
        </div>
        <div id="footer"></div>
    </body>
</html>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
