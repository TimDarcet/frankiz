<ul class="fkz_liens">
  {if !$session->est_auth() }
  <li class="fkz_liens"><a href="login.php" key="l">Se connecter</a></li>
  {/if}
  <li class="fkz_liens"><a href="index.php" key="a">Annonces</a></li>
  <li class="fkz_liens"><a href="activites.php">Activités</a></li>
  {if !$session->est_auth() or !$session->est_interne() }
  <li class="fkz_liens"><a href="trombino.php" key="t">Trombino</a></li>
  {/if}
  <li class="fkz_liens"><a href="xshare.php" key="x">Télécharger</a></li>
  <li class="fkz_liens"><a href="http://wikix.polytechnique.org" key="w">WikiX</a></li>
  <li class="fkz_liens"><a href="binets.php" key="b">Binets</a></li>
  {if !$session->est_auth() && !$session->est_interne() }
  <li class="fkz_liens"><a href="http://perso.frankiz/">Sites élèves</a></li>
  {else}
  <li class="fkz_liens"><a href="http://siteseleves.php">Sites élèves</a></li>
  {/if}
</ul>
