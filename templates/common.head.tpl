{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009-2013 Binet Réseau                                  *}
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

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Frankiz{if $title}: {$title}{/if}</title>
<base href="{$globals->baseurl}/" />

<link rel="alternate" type="application/rss+xml" title="Version RSS" href="rss_annonces.php" />
<link rel="glossary" title="Vocabulaire" href="vocabulaire.php" />
<link rel="help" title="Contact" href="contact.php"/>
<link rel="index" href="index.php"/>
<link rel="start" href="index.php"/>
<link href="mailto:web@fkz" rev="made" />
<link rel="icon" type="image/png" href="favicon.png" />
<!--[if IE]><link rel="shortcut icon" type="image/x-icon" href="favicon.ico" /><![endif]-->
<link rel="search" type="application/opensearchdescription+xml" href="opensearch.xml" title="Trombino" /> 

<script type="text/javascript">
    var logged = {if $smarty.session.auth >= AUTH_COOKIE}true{else}false{/if};
    var xsrf_token = "{xsrf_token}";

    {if $logged}
        {if $user->isFemale()}
            var areyousure = 'Certaine ?';
        {else}
            var areyousure = 'Certain ?';
        {/if}
    {else}
        var areyousure = 'Certain ?';
    {/if}
</script>

{js src="json2.js"}
{js src="jquery.js"}
{js src="common.js"}
{js src="H5F.js"}

{include file='../core/templates/plpage.header.tpl'}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
