{if !$session->est_auth() }
<link ref="navigation" href="login.php" title="Se connecter" />
{/if}
<link ref="navigation" href="index.php" title="Annonces" />
<link ref="navigation" href="activites.php" title="Activités" />
{if !$session->est_auth() or !$session->est_interne() }
<link ref="navigation" href="trombino.php" title="Trombino" />
{/if}
<link ref="navigation" href="xshare.php" title="Télécharger" />
<link ref="navigation" href="http://wikix.polytechnique.org" title="WikiX" />
<link ref="navigation" href="binets.php" title="Binets" />
{if !$session->est_auth() and !$session->est_interne() }
<link ref="navigation" href="http://perso.frankiz/" title="Sites élèves" />
{else}
<link ref="navigation" href="siteseleves.php" title="Sites élèves" />
{/if}

