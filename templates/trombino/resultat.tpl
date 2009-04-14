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

{css_block class='fkz_trombino_eleve'}
<h3 class='nom'>{$eleve.prenom} {$eleve.nom}</h3>
<div class='fkz_trombino_photo'>
  <a href="tol/photo/{$eleve.promo}/{$eleve.login}">
    <img height='122' src='tol/photo/img/{$eleve.promo}/{$eleve.login}' />
  </a>
</div>
<div class='fkz_trombino_infos'>
  <p class='telephone'>
    Tel: {$eleve.tel} Port: {$eleve.port}
  </p>
  <p class='mail'>
    Mail: <a href='mailto:{$eleve.mail}'>{$eleve.mail}</a>
  </p>
  <p class='casert'>
    Casert: {$eleve.piece_id}
  </p>
  <p class='section'>
    Section: {$eleve.section}
  </p>
  <p class='nation'>
    Nation: {$eleve.nation}
  </p>
</div>
<div class='fkz_trombino_section'>
  <a href='tol/?section={$eleve.section_id}'>
    <img height='84' width='63' alt='{$eleve.section}' src='skins/xhtml-default/images/sections/{$eleve.section|lower}{if $eleve.promo % 2 eq 0}0{else}1{/if}.jpg' />
  </a>
</div>
<div class='fkz_trombino_infos2'>
  <p class='promo'>
    {$eleve.promo}
  </p>
  <p class='surnom'>
    {$eleve.surnom}
  </p>
  <p class='date_naissance'>
    {$eleve.date_nais|date_format:"%D"}
  </p>
</div>
<div class='binets'>
  {if hasPerm('admin')}
  Prise: {$eleve.prise}
  <ul>
    {foreach from=$eleve.prise_log item=log}
    <li>
      {$log.ip} ({$log.dns}) - Client xNet : {$log.client}
      <ul>
        {foreach from=$log.mac_log item=mac}
	<li>
	  {$mac.time} : 
	  <a href='tol/?chercher&amp;mac={$mac.id}'>{$mac.id}</a>
	  <em>{$mac.constructeur}</em>
	</li>
	{/foreach}
      </ul>
    </li>
    {/foreach}
  </ul>
  <br />
  {/if}
  Binets:
  <ul>
    {foreach from=$eleve.binets item=binet}
    <li>
      <a href='tol/?binet={$binet.id}'>{$binet.nom}</a>
      <em>({$binet.remarque})</em>
    </li>
    {foreachelse}
    <li>Aucun</li>
    {/foreach}
  </ul>
</div>
<div>
  <p>{$eleve.commentaire}</p>
</div>
<a class='lien' href='https://www.polytechnique.org/profile/{$eleve.prenompolyorg}.{$eleve.nompolyorg}.{$eleve.promo}'>Fiche sur polytechnique.org</a><br />
{if hasPerm('tol')}
<a class='lien' href='admin/user.php?id={$eleve.id}'>Administrer {$eleve.prenom} {$eleve.nom}</a><br />
<a class='lien' href='admin/su/{$eleve.id}'>Prendre l'identité de {$eleve.prenom} {$eleve.nom}</a><br />
{/if}
{/css_block}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
