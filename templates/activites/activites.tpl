<div style='text-align:center'>
  <em>
    {if $bob_ouvert}
      Le BôB est ouvert
    {else}
      Le BôB est fermé
    {/if}
  </em>
</div>
<br />
<div style='text-align:center'>
  <em>
    {if $kes_ouverte}
      La Kès est ouverte
    {else}
      La Kès est fermée
    {/if}
  </em>
</div>
{foreach from=$activites item=activites_date key=date}
<h3>{$date|date_format_humain}</h3>
{foreach from=$activites_date item=activite}
<div style='text-align:center'>
  A {$activite.date|date_format:"%H:%M"}<br />
  <a class='lien' href="{$activite.url}">
    <span class='image' style='display:block; text-align:center'>
      <img src='http://frankiz/data/affiches/{$activite.id}' alt='Affiche' />
    </span>
    <span class='legende'>
      {$activite.titre}
    </span>
  </a>
  <br />
  <p>
    {$activite.texte}
  </p>
</div>
<br />
{/foreach}
{/foreach}
