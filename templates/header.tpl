<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Frankiz : {$title}</title>
	<base href="{$page_base}" />
	<link rel="stylesheet" type="text/css" href="{$skin->css_path}" />
	<link rel="alternate" type="application/rss+xml" title="Version RSS" href="rss_annonces.php" />
	<link rel="glossary" title="Vocabulaire" href="vocabulaire.php" />
	<link rel="help" title="Contact" href="contact.php"/>
	<link rel="index" href="index.php"/>
	<link rel="start" href="index.php"/>
	<link href="mailto:web@fkz" rev="made" />
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="search" type="application/opensearchdescription+xml" href="opensearch.xml.php" title="Trombino" />
	{foreach from=$minimodules item=mini}
	  {$mini->print_template_header()}
	{/foreach}
</head>
