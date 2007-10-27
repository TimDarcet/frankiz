{foreach from=$minimodule.activites item=activite}
{if $activite.exterieur or $session->est_authentifie(AUTH_INTERNE)}
<div style="text-align:center">
  <strong><span>{$titre}</span></strong>
</div>
<div style="text-align:center">
  {if $annonce.date neq ""}{$activite.date}{/if}
  <a href="{if $activite.url neq ""}{$activite.url}{else}activites.php{/if}">
    <img src="{$activite.image}" alt="Affiche">
    <span class="legende">{$activite.titre}</span>
  </a>
</div>
{/if}
{/foreach}
{if $minimodule.activite_etat_bob and $session->est_authentifie(AUTH_INTERNE)}
<div style="text-align:center">
  <strong><span>Le Bôb est ouvert!</span></strong>
</div>
{/if}
