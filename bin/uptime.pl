#!/usr/bin/perl
#
# Copyright (C) 2004 Binet Réseau
# http://www.polytechnique.fr/eleves/binets/br/
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#

$file="/home/frankiz2/cache/uptime";
open(STATUS,">$file");
@cache = split("\n", `ruptime`);
for ($i = 0; $i <= $#cache; $i++) {
	$ligne=$cache[$i];
	@champ=split(/\s+/,$ligne);
	@uptime=split(/\+/,$champ[2]);
	
	if(length($uptime[0])<length($champ[2])){
		$up="".$uptime[0]."j";
	}else{
		@uptime=split(/:+/,$champ[2]);
		$up ="".$uptime[0]."h";
	}
	$res="<serveur nom='".$champ[0]."' etat='".$champ[1]."' uptime='".$up."' />\n";
	print STATUS $res;
} ; 

close STATUS;