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

{js src=strophe.js}
{js src=strophe.muc.js}
{js src=jquery.tinyscrollbar.min.js}
{js src=jquery.tinyscrollbar.min.js}

<link type="text/css" href="css/{"chat.css"|rel}" rel="stylesheet" />

{literal}
<script id="message_template" type="text/x-jquery-tmpl">
  <tr class="message">
    <td class="sender" style="vertical-align: top;">
      {{if sender}}
        <img src="${sender.image}" title="${sender.label}" hruid="${sender.hruid}" align="texttop">
        <span style="display: none;">${sender.label}</span>
      {{/if}}
    </td>
    <td style="vertical-align: top; color: #707070;">${time}</td>
    <td class="text" style="vertical-align: top;">${text}</td>
  </tr>
</script>

<script id="presence_template" type="text/x-jquery-tmpl">
  <a href="tol/see/${sender.hruid}" style="padding: 1px;"><img src="${sender.image}" title="${sender.label}" hruid="${sender.hruid}" align="texttop"></a>
</script>
{/literal}

<div id="log"></div>

<div class="module chat" style="width: 75%; display: inline-block;">
  <div class="head">
    <span class="helper" target="chatroom">
    </span>
    Salon de discussion
  </div>
  <div class="body" id="chatbody">
    <span id="chatstatus">Pas connecté</span>
    <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
    <div class="viewport">
    <div class="overview">
    <table><tbody id="chatroom">
    </tbody></table>
    </div>
    </div>
    <form style="display: none;">
      <textarea id="toPost" style="width: 99%"></textarea>
      <input type="text" id="nick_field" value="{$jabber_nick}" style="display: block; display: none"></input>
      <input type="submit" id="post_button" value="poster" style="display: none;"></input>
    </form >
  </div>
</div>

<div class="minimodule" style="width: 20%; display: inline-block; vertical-align:top;">
  <div class="head">Présence</div>
  <div id='chatpresence' class="body"></div>
</div>
{js src=chat.js}

<script type="text/javascript">
  window.jabber_hruid = "{$jabber_hruid}";
  window.jabber_cookie = "{$jabber_cookie}";
  room_id = '{$jabber_room}';
  room = room_id + '@salons.chat.frankiz.net';
</script>

{* vim:set et sw=2 sts=2 sws=2: *}
