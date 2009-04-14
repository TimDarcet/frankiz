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
  <div class="fkz_qdj_rouje">
    <a href="?qdj={$minimodule.date}&amp;vote=1">{$minimodule.reponse1}</a>
  </div>
  <div class="fkz_qdj_jone">
    <a href="?qdj={$minimodule.date}&amp;vote=2">{$minimodule.reponse2}</a>
  </div>
  <div class="fkz_end_qdj">
    <br />
    {if count($minimodule.votants)}
    <div class="fkz_qdj_dernier_votant">Derniers à répondre :</div>
    <ul class="fkz_qdj_last">
      {foreach from=$minimodule.votants item=votant name=foo}
      {if $smarty.foreach.foo.iteration <= 6}
      <li class="fkz_qdj_last">{$votant.ordre} {$votant.eleve.surnom}</li>
      {/if}
      {/foreach}
    </ul>
    {/if}
    <a class="class_qdj" href="qdj/">Classement QDJ</a>
  </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
