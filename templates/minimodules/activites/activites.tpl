{foreach from=$minimodule.activites item=activite}
{if $activite.exterieur or $session->est_authentifie(AUTH_INTERNE)}
<div style="text-align:center">
  <strong><span>{$activite.titre}</span></strong>
</div>
<div style="text-align:center">
  {if $activite.date neq ""}{$activite.date|date_format:"A %H:%M"}<br />{/if}
  <a href="{if $activite.url neq ""}{$activite.url}{else}activites.php{/if}">
    <img src="{$activite.image}" alt="Affiche" /><br />
    <span class="legende">{$activite.titre}</span>
  </a>
</div>
{/if}
<br />
{/foreach}
{if $minimodule.activites_etat_bob}
{* and $session->est_authentifie(AUTH_INTERNE)} *}
<div style="text-align:center">
  <strong><span>Le Bôb est ouvert!</span></strong>
</div>
{/if}
