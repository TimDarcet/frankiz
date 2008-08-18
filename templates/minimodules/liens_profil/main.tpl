{if $smarty.session.suid }
<span class='warning'>ATTENTION, su en cours. Pour revenir à ta vraie identité, clique <a href="?logout=1">ici</a></span>
{/if}
<ul class="fkz_liens">
  <li class="fkz_liens"><a href="profil/" accesskey="p">Préférences</a></li>
  {if hasPerm('admin') }
  <li class="fkz_liens"><a href="gestion/" accesskey="g">Administration</a></li>
  {/if}
  {if $smarty.session.auth ge AUTH_MDP }
  <li class="fkz_liens"><a href="exit/" accesskey="l">Se déconnecter</a></li>
  {/if}
</ul>
