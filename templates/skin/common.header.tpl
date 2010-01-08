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

<?xml version='1.0' encoding='UTF-8' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Frankiz : {$title}</title>
        <base href="{$globals->baseurl}/" />
        <link rel="stylesheet" type="text/css" href="css/dev.css" />
        <link rel="alternate" type="application/rss+xml" title="Version RSS" href="rss_annonces.php" />
        <link rel="glossary" title="Vocabulaire" href="vocabulaire.php" />
        <link rel="help" title="Contact" href="contact.php"/>
        <link rel="index" href="index.php"/>
        <link rel="start" href="index.php"/>
        <link href="mailto:web@fkz" rev="made" />
        <link rel="shortcut icon" href="favicon.ico" />
        <link rel="search" type="application/opensearchdescription+xml" href="opensearch.xml.php" title="Trombino" />
        {include file='../core/templates/plpage.header.tpl'}
        {if $more} {include file=$more} {/if}
    </head>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
