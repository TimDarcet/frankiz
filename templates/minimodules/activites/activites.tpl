{foreach from=$annonces item=annonce}
<div style="text-align:center">
  <strong><span>{$titre}</span></strong>
</div>
<div style="text-align:center">
  {if $annonce.date neq ""}{$annonce.date}{/if}
  <a href="{if $annonce.url neq ""}{$annonce.url}{else}acitivtes.php{/if}">
    <img src="{$annonce.image}" alt="Affiche">
    <span class="legende">{$annonce.titre}</span>
  </a>
</div>

<div style="text-align:center">
  <strong><span>Le Bôb est ouvert!</span></strong>
</div>
{/foreach}
