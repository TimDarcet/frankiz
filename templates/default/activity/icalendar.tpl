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
BEGIN:VCALENDAR
{display_ical name="prodid" value="-//activities-$view.frankiz.net//Plat-al//FR"}
CALSCALE:GREGORIAN
METHOD:PUBLISH
{display_ical name="x-wr-calname" value="Calendrier Frankiz"}
X-WR-TIMEZONE:Europe/Paris
{foreach from=$activities item=activity}
BEGIN:VEVENT
UID:activity-{$view}-{$activity->id()}@frankiz.net
DTSTART:{$activity->begin()|datetime:'Ymd'}T{$activity->begin()|datetime:'Hi00'}
DTEND:{$activity->end()|datetime:'Ymd'}T{$activity->end()|datetime:'Hi00'}
{display_ical name="summary" value=$activity->title()}
{if $activity->participate()}
STATUS:CONFIRMED
{else}
STATUS:TENTATIVE
{/if}
{assign var=essai value=$activity->description()|miniwiki:'title':'text'|smarty:nodefaults}
{display_ical name="description" value=$essai}
END:VEVENT
{/foreach}
END:VCALENDAR{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
