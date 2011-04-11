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
  window.jabber_nick = "{$jabber_nick}";
  window.jabber_cookie = "{$jabber_cookie}";
  room_id = '{$jabber_room}';
  room = room_id + '@salons.chat.frankiz.net';
  
  {literal}
  // nick -> src, callbacks when src is available
  window.chat_avatar = {};

  function avatar(nick, callback){
    if ( ! (nick in window.chat_avatar)) 
      window.chat_avatar[nick]={};
    if ('src' in window.chat_avatar[nick]) {
      callback(window.chat_avatar[nick]['src']);
    } else if ('callbacks' in window.chat_avatar[nick]) {
      window.chat_avatar[nick]['callbacks'].push(callback);
    } else {
      window.chat_avatar[nick]['callbacks'] = [callback];
    }
  }
  
  function fetch_avatar(nick, hruid){
    $.ajax({
      url: 'chat/ajax/avatar',
      //type:'POST',
      data:{'json': '{"hruid": "'+hruid+'"}'},
      success: function(msg){
        msg = $.parseJSON(msg);
        window.msg = msg;
        //console.log(msg);
        if(!(nick in window.chat_avatar))
          window.chat_avatar[nick]={};
        window.chat_avatar[nick]['src'] = msg.src;
        if('callbacks' in window.chat_avatar[nick]){
          for(var i = 0; i<window.chat_avatar[nick]['callbacks'].length; i++){
	    //bugged
            //window.chat_avatar[nick]['callbacks'][i](msg.src);
	    $('#room img[title='+nick+']').attr('src', msg.src)
	  }
	  delete(window.chat_avatar[nick]['callbacks'])
	} 
      }
    });
  }

  function join(pseudo) {
    Strophe._connectionPlugins['muc'].join(room,pseudo,message_handler,presence_handler,null);
  }

  function post(message) {
    Strophe._connectionPlugins['muc'].message(room,null,message);
  }
  
  function get(from, nick, message) {
    a = $('#message_template').tmpl({sender: {image: "", label: nick}, text: message})
    cb_g = function(){return function(src){$("img", a).attr('src', src)}}
    cb = cb_g();
    avatar(nick, cb)
    a.appendTo('#room');
  }

  function message_handler(o) {
    from = o.getAttribute('from').split('@')[0];
    // from==room_id 
    nick = o.getAttribute('from').split('@')[1].split('/');
    if (nick.length == 2) {
        // nick[0] == 'salons.chat.frankiz.net';
        nick = nick[1];
    } else {
        nick = from;
    }
    message = o.textContent;
    get(from, nick, message);
    return true;
  }
  
  function presence_handler(o){
    from = o.getAttribute("from")
    if (!from) {
      console.log("Huh!? no from!")
      return
    }
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
    fetch_avatar(nick, hruid)
  }

  $("#join_button").click(function(){
     nickField = $("#nick_field");
     join(nickField.val());
     fetch_avatar(nickField.val(), window.jabber_hruid);
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
