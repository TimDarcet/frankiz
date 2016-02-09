{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009-2013 Binet Réseau / X-Ray                          *}
{*  http://br.binets.fr/                                                  *}
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

<a href="http://x-ray.bin/player.php" id="xray_link" onclick="window.open('http://x-ray.bin/player.php','player','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=623,height=270');return false;">
    <span id="xray_titre">{$minimodule.xray_nowplaying.title}</span><br/>
    <span id="xray_artiste">{$minimodule.xray_nowplaying.artist}</span><br/>
    <br/><br/><br/>
    <span id="xray_podcast">{$minimodule.xray_calendar.emission}</span>
</a>
