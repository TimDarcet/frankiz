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

<div class="fkz_qdj_question">{$minimodule.question}</div>
<div>
  {if $minimodule.compte1 + $minimodule.compte2 == 0}
    {assign var='rouje' value=0}
    {assign var='jone'  value=0}
  {else}
    {math assign='rouje' equation="(100 * x) / (x + y)" x=$minimodule.compte1 y=$minimodule.compte2 format="%.1f"}
    {math assign='jone'  equation="(100 * y) / (x + y)" x=$minimodule.compte1 y=$minimodule.compte2 format="%.1f"}
  {/if}
  <div class="fkz_qdj_rouje_reponse">
    <div class="col">
      <span class="blanc" style="height:{$jone}%"></span>
      <span class="rouje" style="height:{$rouje}%"></span>
      <br />
    </div>
    {$minimodule.reponse1}<br />
    {$minimodule.compte1} soit {$rouje}%<br />
  </div>
  <div class="fkz_qdj_jone_reponse">
    <div class="col">
      <span class="blanc" style="height:{$rouje}%"></span>
      <span class="jone"  style="height:{$jone}%"></span>
      <br />
    </div>
    {$minimodule.reponse2}<br />
    {$minimodule.compte2} soit {$jone}%<br />
  </div>
  <div class="fkz_end_qdj">
    <br />
    {if count($minimodule.votants)}
    <div class="fkz_qdj_dernier_votant">Derniers à répondre :</div>
    <ul class="fkz_qdj_last">
      {foreach from=$minimodule.votants item=votant name=foo}
      {if $smarty.foreach.foo.iteration <= 6}
      <li class="fkz_qdj_last">{$votant.ordre}. {$votant.eleve.surnom}</li>
      {/if}
      {/foreach}
    </ul>
    <br />
    <a class="class_qdj" href="qdj/">Classement QDJ</a>
    {/if}
  </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
