{if !$session->est_auth() }
<link rel="navigation" href="login/" title="Se connecter" />
{/if}
<link rel="navigation" href="annonces/" title="Annonces" />
<link rel="navigation" href="activites/" title="Activités" />
{if $session->est_auth() or $session->est_interne() }
<link rel="navigation" href="tol/" title="Trombino" />
{/if}
<link rel="navigation" href="xshare.php" title="Télécharger" />
<link rel="navigation" href="http://wikix.polytechnique.org" title="WikiX" />
<link rel="navigation" href="tol/binets/" title="Binets" />
{if !$session->est_auth() and !$session->est_interne() }
<link rel="navigation" href="http://perso.frankiz/" title="Sites élèves" />
{else}
<link rel="navigation" href="siteseleves.php" title="Sites élèves" />
{/if}

