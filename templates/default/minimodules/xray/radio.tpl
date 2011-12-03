{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet Réseau / X-Ray                               *}
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


<a href="http://x-ray.eleves.polytechnique.fr/player.php" id="xray_linklive"
  onclick="window.open('http://x-ray.eleves.polytechnique.fr/player.php','player','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=623,height=270');return false;">
  <div>
    <img src='http://x-ray.eleves.polytechnique.fr/live/xray-live.gif' alt="X-Ray en live"/>
  </div>
</a>

<div>
  {if $minimodule.xray_nowplaying.cover}
    <img src="{$minimodule.xray_nowplaying.cover}" alt="{$minimodule.xray_nowplaying.album}" class="cover" />
  {/if}
  <div>
    En ce moment à l'antenne : <br />
    {$minimodule.xray_nowplaying.title} - {$minimodule.xray_nowplaying.artist} <br />
    Prochaine émission : {$minimodule.xray_calendar.emission}
  </div>
</div>

<br class="clear" />


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
