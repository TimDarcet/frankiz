<ul class="fkz_liens">
  {if !$session->est_auth() }
  <li class="fkz_liens"><a href="login/" accesskey="l">Se connecter</a></li>
  {/if}
  <li class="fkz_liens"><a href="annonces/" accesskey="a">Annonces</a></li>
  <li class="fkz_liens"><a href="activites/">Activités</a></li>
  {if !$session->est_auth() or !$session->est_interne() }
  <li class="fkz_liens"><a href="trombino.php" accesskey="t">Trombino</a></li>
  {/if}
  <li class="fkz_liens"><a href="xshare.php" accesskey="x">Télécharger</a></li>
  <li class="fkz_liens"><a href="http://wikix.polytechnique.org" accesskey="w">WikiX</a></li>
  <li class="fkz_liens"><a href="binets.php" accesskey="b">Binets</a></li>
  {if !$session->est_auth() && !$session->est_interne() }
  <li class="fkz_liens"><a href="http://perso.frankiz/">Sites élèves</a></li>
  {else}
  <li class="fkz_liens"><a href="siteseleves.php">Sites élèves</a></li>
  {/if}
</ul>
