	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>{$title}</title>
		<base href="{$base}" />
		<link rel="stylesheet" type="text/css" href="{$css}">
		{foreach from=$css_list item=css_alt}
		<link rel="alternate stylesheet" type="text/css" href="{$css_alt->css_path}" 
		      title="{$css_alt->css_path} ({$css_alt->description})">
		{/foreach}
		<link rel="alternate" type="application/rss+xml" title="Version RSS" href="rss_annonces.php" />
		<link rel="glossary" title="Vocabulaire" href="vocabulaire.php" />
		<link rel="help" title="Contact" href="contact.php"/>
		<link rel="index" href="index.php"/>
		<link rel="start" href="index.php"/>
		<link href="mailto:web@fkz" rev="made" />
		<link rel="shortcut icon" href="favicon.ico" />
		<link rel="search" type="application/opensearchdescription+xml" href="opensearch.xml.php" title="Trombino" />
<!--		<xsl:apply-templates select="frankiz/module[@id='liens_navigation']" mode="link"/>
		<xsl:apply-templates select="frankiz/module[@id='liens_perso']" mode="link"/> -->
	</head>
