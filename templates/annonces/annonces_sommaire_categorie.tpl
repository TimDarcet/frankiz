{if count($annonces.$categorie.annonces)}
<div class="fkz_sommaire_titre">
  <span class="fkz_annonces_{$categorie}">{$annonces.$categorie.desc}</span>
</div>
{foreach from=$annonces.$categorie.annonces item=annonce}
<div class="fkz_sommaire_corps">
  <a href="annonce_{$annonce.id}">{$annonce.titre}</a>
</div>
{/foreach}
{/if}
