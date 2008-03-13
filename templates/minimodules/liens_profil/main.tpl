{if $session->v('sueur') }
<span class='warning'>ATTENTION, su en cours. Pour revenir à ta vraie identité, clique <a href="?logout=1">ici</a></span>
{/if}
<ul class="fkz_liens">
  <li class="fkz_liens"><a href="profil/" accesskey="p">Préférences</a></li>
  {if $session->is_admin() }
  <li class="fkz_liens"><a href="gestion/" accesskey="g">Administration</a></li>
  {/if}
  {if $session->est_auth_fort() }
  <li class="fkz_liens"><a href="?logout=1" accesskey="l">Se déconnecter</a></li>
  {/if}
</ul>
