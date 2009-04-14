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

<h2 id='reseau_ip'><span>Infos diverses</span></h2>
<p>
  Normalement tu as l'ip {$xnet_ip.0} (car ta prise est la {$xnet_prise}).<br />
  {if count($xnet_ip) > 1}
  Tu as en plus fait rajouter les ips suivantes à tes ips autorisées.
  <ul>
    {foreach from=$xnet_ip item=ip name=ip_iter}
      {if not $smarty.foreach.ip_iter.first}
      <li>{$ip}</li>
      {/if}
    {/foreach}
  </ul>
  {/if}
</p>
{if not $xnet_match_ip}
<span class='warning'>
  Tu te connectes actuellement avec l'ip {$xnet_ip_current}. Ce ne devrait pas être le cas si tu te connectes depuis ton kasert.
</span>
{else}
<span class='note'>
  Tu utilises actuellement l'ip {$xnet_ip_current}.
</span>
{/if}
<br />
<span class='note'>
  Si tu souhaite rajouter une nouvelle ip, clique <a class='lien' href='profil/reseau/demande_ip'>ici</a>
</span>
{foreach from=$xnet_ip item=ip}
<form enctype='multipart/form-data' method='post' action='profil/net'>
  <h2>Modification du mot de passe Xnet <span class='adresse_ip'>({$ip})</span></h2>
  <div class='formulaire'>
    <input type='hidden' name='ip_xnet' value='{$ip}' />
    <div>
      <span class='droite'><span class='note'>Ton mot de passe doit contenir au moins 6 caractères</span></span>
    </div>
    <div>
      <span class='gauche'>Mot de passe:</span>
      <span class='droite'><input type='password' name='passwd' value='12345678' /></span>
    </div>
    <div>
      <span class='gauche'>Retapez le:</span>
      <span class='droite'><input type='password' name='passwd2' value='12345678' /></span>
    </div>
    <div>
      <span class='boutons'><input type='submit' name='changer_mdp_xnet' value='Changer' /></span>
    </div>
  </div>
</form>
{/foreach}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
