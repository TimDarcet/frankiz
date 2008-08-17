<ul class="fkz_liens">
{if $smarty.session.auth < AUTH_COOKIE }
  <li class="fkz_liens"><a href="login/" accesskey="l">Se connecter</a></li>
{/if}
  <li class="fkz_liens"><a href="annonces/" accesskey="a">Annonces</a></li>
  <li class="fkz_liens"><a href="activites/">Activités</a></li>
  {if $smarty.session.auth >= AUTH_INTERNE }
  <li class="fkz_liens"><a href="tol/" accesskey="t">Trombino</a></li>
  {/if}
  <li class="fkz_liens"><a href="xshare.php" accesskey="x">Télécharger</a></li>
  <li class="fkz_liens"><a href="http://wikix.polytechnique.org" accesskey="w">WikiX</a></li>
  <li class="fkz_liens"><a href="tol/binets/" accesskey="b">Binets</a></li>
  {if est_interne() }
  <li class="fkz_liens"><a href="http://perso.frankiz/">Sites élèves</a></li>
  {else}
  <li class="fkz_liens"><a href="siteseleves.php">Sites élèves</a></li>
  {/if}
</ul>
