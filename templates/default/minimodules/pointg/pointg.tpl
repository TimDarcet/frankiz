{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010 Binet Réseau                                      *}
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

<div class="barInfo">
    <h3 class="navigation">
        <span class="left">
        <a href="#" onclick="pointg.backward();return false"> &lt;-- </a>
    </span>
        <span class="titre_fenetre">
        Classement des Bars
    </span>
    <span class="right">
        <a href="#" onclick="pointg.forward();return false"> --&gt; </a>
    </span>
    </h3>
    {if $minimodule.bars|@count > 0}
    <h4 class="question">Prêt pour être le 1er Bar??</h4>
    <div class="display">
        <table>
            <tr>
                <th class="classement">N°</th>
                <th class="bars">Bars</th>
                <th class="score">Score(à dévoiler)</th>
            </tr>
            {foreach from=$minimodule.bars item='bar'}
            <tr>
                <td class="classement">{$bar.classement}</td>
                <td class="bars">{$bar.bars}</td>
            <td class="score">***</td>
            </tr>
            {/foreach}
        </table>
    </div>
    <h4 id="More" title="D'autres infos"><a href="http://www.pointgamma.com/moduleFKZ/classement.php" target=_blank>Plus d'information sur le classement</a></h4>
    <div class="display" id="later_display" style="display:none"></div>
    {/if}

    {if $minimodule.bars|@count == 0}
    <h4>Pas de bar pour instant.</h4>
    {/if}
</div>

<div class="annonceInfo" style="display:none">
    <h3 class="navigation">
        <span class="left">
        <a href="#" onclick="pointg.backward();return false"> &lt;-- </a>
    </span>
        <span class="titre_fenetre">
        Annonces PG
    </span>
    <span class="right">
        <a href="#" onclick="pointg.forward();return false"> --&gt; </a>
    </span>
    </h3>
    {if $minimodule.annonces_pointg|@count > 0}
    <h4 class="question">Point Gamma 2012 s'approche!</h4>
    <div class="display">
        {foreach from=$minimodule.annonces_pointg item='annonce'}
    <table>
        <tr>
        <td class="numero_annonce">{$annonce.numero}</td>
        <td class="titre_annonce"><a href="#" onclick="$('#contenu_annonce_{$annonce.numero}').slideToggle();return false">{$annonce.titre}</a></td>
    </tr>
    </table>
        <div class="contenu_annonce" id = 'contenu_annonce_{$annonce.numero}' style="display:none">
        {$annonce.text}
        </div>
        {/foreach}
    </div>
    {/if}

    {if $minimodule.annonces_pointg|@count == 0}
    <h4> Pas d'annonces pour instant.</h4>
    {/if}
</div>

<div class="edtInfo" style="display:none">
    <h3 class="navigation">
        <span class="left">
        <a href="#" onclick="pointg.backward();return false"> &lt;-- </a>
    </span>
        <span class="titre_fenetre">
        EDT des Créneaux Préventes
    </span>
    <span class="right">
        <a href="#" onclick="pointg.forward();return false"> --&gt; </a>
    </span>
    </h3>
    {if $minimodule.preventes_pointg|@count >0}
    <h4 class="question">Shotgun <a href="http://www.pointgamma.com/moduleFKZ/edt.php" target=_blank>les Préventes</a> au hasard!</h4>
    <div class="display">
        <table>
            <tr>
                <th class="time">Jour</th>
                <th class="ecole">Ecole</th>
            </tr>
            {foreach from=$minimodule.preventes_pointg item='prevente'}
            <tr>
                <td class="time">{$prevente.time}</td>
            <td class="ecole">{$prevente.ecole}</td>
            </tr>
            {/foreach}
        </table>
    </div>
    {/if}
    {if $minimodule.preventes_pointg|@count==0}
    <h4> Pas d'informations pour instant.</h4>
    {/if}
    <h4 class="inscription">Inscris toi sur <a href="mailto:prevente.pg2012@gmail.com">prevente.pg2012@gmail.com</a> sans attendre !</h4>
    <h4 class="separation">--------------------------------------------------------------</h4>
    <h4 class="shotgun">Shotgun ton créneau sécu <a href="https://docs.google.com/spreadsheet/ccc?key=0Ape0OeLRpaaJdHNtVUoxemxJcVpLcEc0OHlRNjgtR3c#gid=0" target=_blank>ici</a>!</h4>
</div>

<p class="info_fin">Binet Point Gamma</a>.</p>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
