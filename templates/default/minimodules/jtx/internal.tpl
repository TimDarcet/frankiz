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

<video controls="controls" poster="http://jtx/vdj/vdj.png{$minimodule.params}" preload="none" id="jtx_vdj">
    <source src="http://jtx/vdj/vdj.webm{$minimodule.params}" type="video/webm" />
    <source src="http://jtx/vdj/vdj.ogv{$minimodule.params}" type="video/ogg" />
    <param name="allowfullscreen" value="true">
    <p>Pour pouvoir profiter de cette vidéo, il faut installer un navigateur récent : Firefox ou Chrome.</p>
</video>
<div>
	<a href="http://jtx/vdj/vdj.webm{$minimodule.params}" 
onclick="document.getElementById('jtx_vdj').pause();window.open(this.href,'Vidé du jour','width='+screen.width+',height='+screen.height+',top=0,left=0'+',fullscreen=yes');return false;">Plein &eacute;cran</a>
</div>
{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
