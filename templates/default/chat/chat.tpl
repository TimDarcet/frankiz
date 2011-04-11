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

{literal}
<script id="message_template" type="text/x-jquery-tmpl">
  <tr class="message">
    <td class="sender">
      {{if sender}}
        <img src="${sender.image}" title="${sender.label}" hruid="${sender.hruid}" align="texttop">
	<span style="display: none;">${sender.label}</span>
      {{/if}}
    </td>
    <td>${time}</td>
    <td class="text">${text}</td>
  </tr>
</script>
{/literal}

<div id='log'></div>

<div id="section">
  <div class="module chat">
    <div class='head'>
      <span class='helper' target='chatroom'>
      </span>
      Salon de discussion
    </div>
    <div class='body'>
      <table><tbody id="room">
      </tbody></table>
      <form >
        <textarea id="toPost" style="width: 99%"></textarea>
	<input type="submit" id="join_button" value="rejoindre" style="display: inline;"></input>
	<input type="text" id="nick_field" value="{$jabber_nick}" style="display: inline; display: none"></input>
	<input type="submit" id="post_button" value="poster" style="display: inline; float: right;"></input>
      </form >
    </div>
  </div>
</div>

{js src=chat.js}

<script type="text/javascript">
  window.jabber_hruid = "{$jabber_hruid}";
  window.jabber_connected = false;
  window.jabber_cookie = "{$jabber_cookie}";
  room_id = '{$jabber_room}';
  room = room_id + '@salons.chat.frankiz.net';
  
  {literal}
  window.hruids = {}

  function avatar(nick, hruid, callback){
    //console.log('avatar hruid'+hruid+' nick:'+nick)
    
    $.ajax({
      url: 'chat/ajax/avatar',
      data:{'json': '{"hruid": "'+hruid+'"}'},
      //cache: true,
      success: function(msg){
        msg = $.parseJSON(msg);
        window.msg = msg;
	callback(msg.src);
        //$('#room img[hruid='+hruid+']').attr('src', msg.src)
      }
    });

  }
  
  function join(pseudo) {
    cb = function(){
      Strophe._connectionPlugins['muc'].join(room,pseudo,message_handler,presence_handler,null);
      window.jabber_nick = pseudo;
      $('#join_button').attr('disabled','true');
    }
    if (window.jabber_connected) {
      leave(cb);
    } else {
      cb();
    }
  }

  function leave(callback) {
    cb = callback ? callback : null
    Strophe._connectionPlugins['muc'].leave(room,window.jabber_nick,cb);
  }

  function post(message) {
    Strophe._connectionPlugins['muc'].message(room,null,message);
  }
  
  //from : sender jid, possibly null
  //nick : sender nick
  //message: sent message
  function get(from, nick, message, date) {
    //console.log('get from:'+from+' nick:'+nick+' message:'+message)
    if (from==null) {
      console.log('Strange, got message from: ' + from + ' saying ' + message)
      return
    }
    if (date) {
      at = date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds()
      if ( date.toLocaleDateString() != (new Date()).toLocaleDateString() ) {
        at = "" + date.getDate()+'/'+(date.getMonth()<9?'0':'')+(date.getMonth()+1)+' ' + at
      }
    } else
      at = ""
    a = $('#message_template').tmpl({sender: {image: "", hruid: from, label: nick}, text: message, time: at})
    cb_g = function(){var e = a; return function(src){$("img", e).attr('src', src)}}
    cb = cb_g();
    if(from)
      avatar(nick, from, cb)
    else
      avatar(nick, null, cb)
    a.appendTo('#room');
  }
  
  
  function message_handler(o) {
    //console.log('Message_handler')
    //console.log(o)
    from = o.getAttribute("from")
    if (!from) {
      console.log("Huh!? no from!")
      return
    }
    var roomjid, nick, name, service
    t = from.split('/');
    roomjid = t[0]
    nick = t[1]
    t = roomjid.split('@')
    name = t[0]
    service=t[1]
    // name==room_id
    // I expect only one
    if ($('delay[from]', o).attr('from'))
      from = $('delay[from]', o).attr('from').split('/')[0].split('@')[0]
    else if (nick in window.hruids)
      from = window.hruids[nick]
    else
      from = null

    if ($('x[xmlns=jabber:x:delay]', o)) {
      raw = $('x[xmlns=jabber:x:delay]', o).attr('stamp')
      date = new Date($('x[xmlns=jabber:x:delay]', o).attr('stamp'))
      if ( date == "Invalid Date" ) {
        alt = raw.substring(0,4) + '-' + raw.substring(4,6) + '-' + raw.substring(6)
        date = new Date(alt)
      }
    } else
      date = null
    
    message = $('body', o).text()
    get(from, nick, message, date)
    return true;
  }
  
  function presence_handler(o){
    console.log('presence_handler')
    console.log(o)
    from = o.getAttribute("from")
    if (!from) {
      console.log("Huh!? no from!")
      return
    }

    if ( $('error[code=409]', o).length > 0 ) {
      window.jabber_nick += '_';
      Strophe._connectionPlugins['muc'].changeNick(room, window.jabber_nick);
      return;
    }

    var roomjid, nick, name, service
    nick = from.split('/')[1].split('@')[0]
    jid = $("item[jid]", o).attr("jid")
    if(!jid){
      text = $("error text", o).text()
      if(!text)
        console.log('Humpf, no jid and no text.')
      else{
        window.chat_avatar = {}
        alert(text)
      }
      return
    }
    hruid = jid.split('/')[0].split('@')[0]
    console.log('Got '+nick+'('+hruid+')')

    window.hruids[nick] = hruid
    //fetch_avatar(nick, hruid)
  }
  
  
  $("#join_button").click(function(){
     nickField = $("#nick_field");
     join(nickField.val());
     //fetch_avatar(nickField.val(), window.jabber_hruid);
     return false;
  });
  
  $("#post_button").click(function(){
      textField = $('#toPost');
      post(textField.val());
      textField.val("");
      return false;
  });
  {/literal}

</script>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
