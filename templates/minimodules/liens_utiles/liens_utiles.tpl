<ul class='fkz_liens'>
  <li class='fkz_liens'><a href="contact.php" key="c">Contacter les élèves</a></li>
  {if !$session->est_interne()}
  <li class='fkz_liens'><a href="plan.php">Venir à l'X</a></li>
  {/if}
  {if $session->est_auth()}
  <li class='fkz_liens'><a href="http://de.polytechnique.fr/index.php?page=edt">Emploi du temps</a></li>
  <li class='fkz_liens'><a href="profil/licenses.php">Licences Msdnaa</a></li>
  <li class='fkz_liens'><a href="http://poly.polytechnique.fr">Redirection des mails</a></li>
  {/if}
  {if $session->est_interne()}
  <li class='fkz_liens'><a href="http://ircserver.eleves.polytechnique.fr/">Accéder à l'IRC</a></li>
  {/if}
  <li class='fkz_liens'><a href="http://www.polytechnique.fr/eleves/binets/reseau">Docs BR</a></li>
  <li class='fkz_liens'><a href="http://www.polytechnique.fr/">Site de l'école</a></li>
  <li class='fkz_liens'><a href="http://www.edu.polytechnique.fr/">Site de la DE</a></li>
  <li class='fkz_liens'><a href="http://www.polytechnique.fr/sites/orientation4a/pages_orientation/">Orientation 4eme année</a></li>
  {if $session->est_interne() || $session->est_auth()}
  <li class='fkz_liens'><a href="http://intranet.polytechnique.fr/" key="i">Intranet</a></li>
  {/if}
  <li class='fkz_liens'><a href="http://www.polytechnique.org/" key="o">Polytechnique.org</a></li>
  <li class='fkz_liens'><a href="http://www.polytechnique.net/" key="n">Polytechnique.net</a></li>
  {if $session->est_interne() || $session->est_auth()}
  <li class='fkz_liens'><a href="partenaires.php">Partenariats</a></li>
  {/if}
</ul>
