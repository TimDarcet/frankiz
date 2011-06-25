var BOSH_SERVICE = '/jabber/http-bind';
var connection = null;

function log(o) {
    //console.log(o)
}

function onConnect(status)
{
    if (status == Strophe.Status.CONNECTING) {
        $('#chatstatus').text('Connection...')
    } else if (status == Strophe.Status.CONNFAIL) {
        $('#chatstatus').text('Connection échouée')
    } else if (status == Strophe.Status.DISCONNECTING) {
    } else if (status == Strophe.Status.DISCONNECTED) {
        $('#chatstatus').text('Pas connecté')
    } else if (status == Strophe.Status.CONNECTED) {
        $('#chatstatus').text('')
	$('#chatbody form').show()

        connection.send($pres().tree());

        Strophe._connectionPlugins['muc'].init(connection);
        nickField = $("#nick_field");
        join(nickField.val());
    }
}

window.avatars = {}
window.client_count = {}

function avatar(nick, hruid, callback){
    
    if(hruid in window.avatars) {
        if('src' in window.avatars[hruid])
            callback(window.avatars[hruid]['src'])
	else
            window.avatars[hruid]['callbacks'].push(callback);
    } else {
        window.avatars[hruid]={'callbacks': [callback]}  //Not thread safe approach!
        
        $.ajax({
            url: 'chat/ajax/avatar',
            data:{'json': '{"hruid": "'+hruid+'"}'},
            success: function(msg){
                msg = $.parseJSON(msg);
                window.avatars[hruid]['src'] = msg.src
                for (var i = 0; i < window.avatars[hruid]['callbacks'].length; i++)
                    if (window.avatars[hruid]['callbacks'][i] && $.isFunction(window.avatars[hruid]['callbacks'][i]))
                        window.avatars[hruid]['callbacks'][i](msg.src);
                
                delete window.avatars[hruid]['callbacks'];
                //$('#room img[hruid='+hruid+']').attr('src', msg.src)
            }
        });
        
    }
}

function join(pseudo) {
    cb = function(){
        Strophe._connectionPlugins['muc'].join(room,pseudo,message_handler,presence_handler,null);
        window.jabber_nick = pseudo;
        window.hruids[pseudo] = window.jabber_hruid;
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
    log('get from:'+from+' nick:'+nick+' message:'+message)
    if (from==null) {
        if (nick in window.hruids)
            from = window.hruids[nick];
        else {
            log('Strange, got message from: ' + from + ' saying ' + message);
            return;
        }
    }

    str = function(i) {
        if(i<10)
            return '0' + i;
        return '' + i;
    }
    
    if (date) {
        at = str(date.getHours()) + ':' + str(date.getMinutes()) + ':' + str(date.getSeconds())
        if ( date.toLocaleDateString() != (new Date()).toLocaleDateString() ) {
            at = str(date.getDate())+'/'+str(date.getMonth()+1)+' ' + at
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
    a.appendTo('#chatroom');
}

function isPresent(nick, hruid, so) {
    log('seen: '+nick+'('+hruid+') ? ' + so)
    if (so) {
        if(hruid in window.client_count)
            window.client_count[hruid] += 1
        else
            window.client_count[hruid] = 1
        if (window.client_count[hruid] == 1) {
            a = $('#presence_template').tmpl({sender: {image: "", hruid: hruid, label: nick}})
	    cb_g = function(){var e = a; return function(src){$("img", e).attr('src', src)}}
            cb = cb_g();
            avatar(nick, hruid, cb);
	    a.appendTo('#chatpresence')
        }
    } else {
        if (!(hruid in window.client_count) || window.client_count[hruid] <= 0) {
            log("client count bug")
            window.client_count[hruid] = 0
        } else
            window.client_count[hruid] -= 1
        if(window.client_count[hruid]==0)
            $('#chatpresence img[hruid='+hruid+']').remove()
    }
}

function message_handler(o) {
    //log('message_handler')
    //log(o)
    from = o.getAttribute("from")
    if (!from)
        return
    
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
        if(raw)
            date = new Date($('x[xmlns=jabber:x:delay]', o).attr('stamp'))
        else
            date = new Date()
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
    log('presence_handler')
    log(o)
    from = o.getAttribute("from")
    if (!from) {
        log("No from")
        return true;
    }
    
    if ( $('error[code=409]', o).length > 0 ) {
        delete window.hruids[window.jabber_nick];
	log('collision: ' + window.jabber_nick + ' -> ' + window.jabber_nick + '_')
        window.jabber_nick += '_';
        Strophe._connectionPlugins['muc'].changeNick(room, window.jabber_nick);
        window.hruids[window.jabber_nick] = window.jabber_hruid;
        return true;
    }

    var roomjid, nick, name, service
    nick = from.split('/')[1].split('@')[0]
    jid = $("item[jid]", o).attr("jid")
    if(!jid){
        text = $("error text", o).text()
        if(!text)
            log('Humpf, no jid and no text.')
        else{
            window.chat_avatar = {}
            alert(text)
        }
        return true;
    }
    hruid = jid.split('/')[0].split('@')[0]

    window.hruids[nick] = hruid

    so = true;
    if (o.getAttribute('type') && o.getAttribute('type') == 'unavailable')
      so = false;
    isPresent(nick, hruid, so);
    return true;
}
  
  
$("#join_button").click(function(){
     nickField = $("#nick_field");
     join(nickField.val());
     //fetch_avatar(nickField.val(), window.jabber_hruid);
     return false;
});
  
//$("#post_button").click(function(){
$('#toPost').keypress(function(event){
    if (event.which == '13') {
      textField = $('#toPost');
      post(textField.val());
      textField.val("");
      return false;
    }
});


$(document).ready(function () {
    connection = new Strophe.Connection(BOSH_SERVICE);
    //Strophe.log = function (level, msg) { log('LOG [' + level + ']:' + msg); };
    connection.connect(window.jabber_hruid+'@chat.frankiz.net',
                       '{COOKIE}'+window.jabber_cookie,
                       onConnect);
});

