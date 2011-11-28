
var Hackes = function(hackElement){
	this.hackElement = hackElement;
	this.consoleDiv = $('<div id="console"></div>');
	this.hackElement.append(this.consoleDiv);
	this.consoleDiv.append('<span class="line"></span>');
	this.cursor = $('<span id="cursor">&nbsp;</span>');
	this.consoleDiv.append(this.cursor);

	this.currentSpan = this.consoleDiv.children()[0];

	var self = this;

	this.scroll();

	//Set up periodic events
	
	//Try to clear some span (span leaks :D)
	setInterval(function(){self.tryClear();}, 1000);

        setInterval(function(){self.blinkCursor();},500);
};

Hackes.prototype = {

	addText: function(text){
		text = text.replace(/ /g, '&nbsp;');
		//Append the text (and create a span per line)
		var lines = text.split("\n");
		for(i=0; i<lines.length - 1; i++){
			this.currentSpan.innerHTML += lines[i];
			this.cursor.before('<br/>');
			this.currentSpan = $('<span class="line"></span>')[0];
			this.cursor.before(this.currentSpan);
		}
		this.currentSpan.innerHTML += lines[i];
		this.scroll();
	},

	scroll: function(){
		//Scroll the text so that we see the end
	 	var containerBottom = this.hackElement.offset().top + this.hackElement.height();
		var consoleBottom = this.consoleDiv.offset().top + 40 + this.consoleDiv.height();

		//if(consoleBottom <= containerBottom) return;

		this.consoleDiv.css({
			'top': parseInt(this.consoleDiv.css('top')) + containerBottom - consoleBottom,
		});
	},

	blinkCursor: function(){
		this.cursor.toggleClass('inverted');
	},

	tryClear: function(){
		var spans = $("#h4ck span.line, #h4ck br");
		var toRemove = spans.filter(function(){
			return $(this).offset().top + $(this).height() < -1000;
		});

		toRemove.remove();

		this.scroll();
	}
}


var ScenarioReader = function(console, scenarios, defaultConfig){
	$.extend(this, defaultConfig);
	this.defaults = defaultConfig;
	this.scenarios = scenarios;
	this.console = console;
};

ScenarioReader.prototype = {

	random: function(max){
		return Math.floor(Math.random()*max);
	},

	start: function(){
		this.currentScenario = this.scenarios[this.random(this.scenarios.length)];
		this.currentAction = 0;
	
		var self = this;

		setTimeout(function(){self.processAction()}, /*3000*/ 1);
	},

	processAction: function(){
		if(this.currentAction >= this.currentScenario.length){
			this.start();
			return;
		}

		var actionData = this.currentScenario[this.currentAction];
		this.currentAction ++;
		var action = actionData[0];

		var self = this;

		if(action == "prompt"){
			this.console.addText(this.user + "@" + this.machine + ":" + this.pwd + this.usermode + " ");
			this.processAction();
		}else if(action == "display"){
			for(var i=1; i<actionData.length; i++){
				this.console.addText(actionData[i]);
			}
			this.processAction();
		}else if(action == "wait"){
			setTimeout(function(){self.processAction();}, actionData[1]);
		}else if(action == "type"){
			this.toType = actionData[1];
			this.typePointer = 0;
			setTimeout(function(){self.doType();}, this.typespeed);
		}else if(action == 'config'){
			if(actionData[1] == 'default'){
				$.extend(this, this.defaults);
			}else{
				$.extend(this, actionData[1]);
			}
			this.processAction();
		}
	},

	doType: function(){
		if(this.typePointer < this.toType.length){
			this.console.addText(this.toType[this.typePointer]);
			this.typePointer ++;
			var self = this;
			setTimeout(function(){self.doType();}, this.typespeed + this.random(this.typespeed_noise));
		}else{
			this.processAction();
		}
	}
};



var reader;
var hack;

$(document).ready(function(){
	hack = new Hackes($("#h4ck"));
	reader = new ScenarioReader(hack, hack_console, hack_console_default_config);
	reader.start();
});

var hack_console_default_config = {
	user: 'h4ck3r',
	machine: 'frankiz',
	pwd: '~',
	usermode: '$',
	typespeed: 50,
	typespeed_noise: 150
};

//Some of it was taken from manaco's hacking text (monaco is a hacker game)
var hack_console = [
	[
		['prompt'],
		['type', 'troll face'],
		['display', '\n      ,---.______________\n'],
		['display', '     /                   |_\n'],
		['display', '    /                      |\n'],
		['display', '   /                        |\n'],
		['display', ' .\'       ,--._       ___    |\n'],
		['display', '|         \'--._|  .  |,--\'    |\n'],
		['display', '|                  |          |\n'],
		['display', '|_     .__      c~./         /\n'],
		['display', '  |_   | |`--.__        /|  /\n'],
		['display', '    |   |_ |_|_|`--.__.\' |  |\n'],
		['display', '    |    `._ | |_|_|_|_|_|  |\n'],
		['display', '     `.     `.__ | | | | |   |\n'],
		['display', '       `.       `.______/    |\n'],
		['display', '         `.__                /\n'],
		['display', '             `--.__         /\n'],
		['display', '                   `--.____/\n'],
		['display', '       *P*R*O*B*L*E*M*?*\n']

	],
	[
		['prompt'],
		['type', 'rm -pd fist.*\n'],
		['wait', 350],
		['prompt'],
		['type', 'touch bite'],
		['display', '\nPermission denied.\n'],
		['wait', 500],
		['prompt'],
		['type', 'sudo apt-get moo'],
		['display', '\nThis APT has super COW powers!\n']
	],
	[
		['prompt'],
		['type', 'grep -rn1 "passwd" ecranDSI/dump/usr/root/.player/prefs.js'],
		['display', '\n33-user_pref("innes.player.proxy.ssl_port", 8080);\n'],
		['display', '34:user_pref("innes.player.webui.passwd", "Amph1th3atr31");\n'],
		['display', '35-user_pref("innes.xpf.downloader-plugnCast.enable", true);\n'],
		['prompt'],
		['wait', 400],
		['type', 'grep -rn1 "key" ecranDSI/dump/usr/root/.player/prefs.js'],
		['display', '\n17-user_pref("extensions.lastAppVersion", "2.50.76");\n'],
		['display', '18:user_pref("innes.appli.license-key", "hKmC5  -  KLFT  -  TfTp  -  g+Th  -  5ih=3");\n'],
		['display', '19-user_pref("innes.player.proxy.ftp", ""); \n']
	],
	[
		['prompt'],
		['type', 'ifconfig'],
		['display', '\neth0: flags=4163<UP,BROADCAST,RUNNING,MULTICAST>  mtu 1500  metric 1\n'],
		['display', '        inet 129.104.13.37  netmask 255.255.255.128  broadcast 129.104.201.255\n'],
		['display', '        inet6 fe80::5e26:aff:fe0f:4c73  prefixlen 64  scopeid 0x20<link>\n'],
		['display', '        ether 5c:26:0a:0f:4c:73  txqueuelen 1000  (Ethernet)\n'],
		['display', '        RX packets 103569  bytes 76980824 (73.4 MiB)\n'],
		['display', '        RX errors 0  dropped 19  overruns 0  frame 0\n'],
		['display', '        TX packets 64889  bytes 9527859 (9.0 MiB)\n'],
		['display', '        TX errors 0  dropped 0 overruns 0  carrier 0  collisions 0\n'],
		['display', '        device interrupt 20  memory 0xf6900000-f6920000  \n\n'],

		['display', 'lo: flags=73<UP,LOOPBACK,RUNNING>  mtu 16436  metric 1\n'],
		['display', '        inet 127.0.0.1  netmask 255.0.0.0\n'],
		['display', '        inet6 ::1  prefixlen 128  scopeid 0x10<host>\n'],
		['display', '        loop  txqueuelen 0  (Local Loopback)\n'],
		['display', '        RX packets 345  bytes 31218 (30.4 KiB)\n'],
		['display', '        RX errors 0  dropped 0  overruns 0  frame 0\n'],
		['display', '        TX packets 345  bytes 31218 (30.4 KiB)\n'],
		['display', '        TX errors 0  dropped 0 overruns 0  carrier 0  collisions 0\n']
	],
	[
		['prompt'],
		['type', 'ls -la'],
		['display', '\n-rw-r--r-- 1 4tt4ck3s users    0 Nov 28 00:56 all_your.kes'],
		['display', '\n-rw-r--r-- 1 4tt4ck3s users    0 Nov 28 00:56 are_belong_to.us'],
		['display', '\n-rw-r--r-- 1 4tt4ck3s users 642M Nov 28 00:59 Bergo_et_les_7_mains.avi'],
		['display', '\ndrwxr-xr-x 2 4tt4ck3s users 4.0K Nov 28 00:56 EpyxPSC/'],
		['display', '\n-rw-r--r-- 1 4tt4ck3s users  50K Nov 28 01:00 4tt4ck3s_needs_you.gif'],
		['display', '\ndrwxr-xr-x 2 4tt4ck3s users 4.0K Nov 28 00:56 h4ckDSI/'],
		['display', '\ndrwxr-xr-x 2 4tt4ck3s users 4.0K Nov 28 00:55 INF422/'],
		['display', '\ndrwxr-xr-x 2 4tt4ck3s users 4.0K Nov 28 00:56 MAT431/'],
		['display', '\n-rw-r--r-- 1 4tt4ck3s users 1.2G Nov 28 00:58 RMPD_Zaza_combat_de_boue.avi'],
		['display', '\ndrwxr-xr-x 2 4tt4ck3s users 4.0K Nov 28 00:57 SkinFrankiz/\n']

	],
	[
		['prompt'],
		['type', 'sh -c ‘sleep 36000 && mplayer http://lulz.dd/songs/never_gonna_give_you_up.mp3 --loop’\n']
	],
	[
		['prompt'],
		['type', 'cd /var/kes\n'],
		['config', {pwd: '/var/kes'}],
		['prompt'],
		['wait', 300],
		['type', 'chgrp -R us.\n'],
		['config', 'default']	
	],
	[
		['prompt'],
		['wait', 432],
		['type', 'nmap 129.104.13.37'],
		['display', '\nCommand helpfully interrupted.\n[1] Would you like help ?\n[2] Or would you prefer to h4ck this system on your own?\n ? '],
		['wait', 1000],
		['type', '2\n'],
		['prompt'],
		['wait', 400],
		['type', 'nmap 129.104.13.37'],
		['wait', 500],
		['display', '\nNo open ports detect.\n']
	],
	[
		['prompt'],
		['type', './hack.sh gatekeeper --help --not-verbose'],
		['display', '\nenable query [gateway, router, firewall]\n'],
		['prompt'],
		['wait', 399],
		['type', './hack.sh gatekeeper query firewall'],
		['display', '\nNOGO v2.0 firewall active\n'],
		['prompt'],
		['wait', 599],
		['type', './hack.sh gatekeeper services|list NOGO'],
		['display', '\nNOGO : uptime 542 days      pid 2067\n'],
		['prompt'],
		['wait', 400],
		['type', './hack.sh gatekeeper kill 2067'],
		['display', '\nNOGO service disabled. Welcome home.\n']
	],
	[
		['prompt'],
		['type', 'make me a sandwich'],
		['display', '\nPermission Denied\n'],
		['wait', 600],
		['prompt'],
		['type', 'sudo make me a sandwich'],
		['display', '\nPassword:\n'],
		['wait', 2000],
		['display', 'Error: Sandwich is in another castle\n']
	],
	[ // A first test of scenario
		['prompt'], //Shows the prompt
		['type', 'echo "Cyb3r 4tt4ck3s RULES!!§§1"\n'],//Make it type char by char the second element
		['display', 'Cyb3r 4tt4ck3s RULES!!$$1', '\n'],//Will display every element in order
		['prompt'],
		['wait', 3000], //obvious
		['type', 'exit\n']
	],
	[
		['prompt'],
		['type', 'cd /etc/\n'],
		['config', {pwd: '/etc/'}], //Change the config of the reader
		['prompt'],
		['type', 'cd -\n'],
		['config', {pwd: '~'}]
	],
	[
		['prompt'],
		['type', '/media/usbkey/virus --infect-system --root\n'],
		['config', {usermode: '#', user:'root'}],
		['prompt'],
		['wait', 1000],
		['type', '/sbin/building --lights-out\n'],
		['prompt'],
		['wait', 500],
		['type', '/sbin/security-ctl shutdown\n'],
		['prompt'],
		['wait', 650],
		['type', '/etc/init.d firewall stop\n'],
		['prompt'],
		['wait', 400],
		['type', 'ps aux|grep fire\n'],
		['display', 'root       238  0.0  0.1   2316   800 ?   S    01:09   0:00 mplayer we_didnt_start_the_fire.mp3\n' ],
		['display', 'root       374  0.0  0.0      0     0 ?   S    01:09   0:00 /usr/bin/firewall --user root --pwd qwerty\n'],
		['prompt'],
		['wait', 3000],
		['type', 'firewall -u root\n'],
		['display', 'Welcome to the firewall!\n?'],
		['wait', 300],
		['type', ' status'],
		['display', '\nFirewall active. Protecting 5 terminals online (23 days uptime)\n? '],
		['wait', 500],
		['type', 'sleep 36000'],
		['display', '\nFirewall shutting down (36000 seconds, 10h:00m:00s )\n? '],
		['wait', 500],
		['type', 'logs clean --force'],
		['display', '\nLogs erased.\n? '],
		['type', 'exit\n'],
		['config', 'default']
	]/**/
];
