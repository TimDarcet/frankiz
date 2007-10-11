{foreach from=$annonces.$categorie.annonces item=annonce}
<div class="fkz_annonces_1">
  <div class="fkz_annonces_2">
    <div class="fkz_annonces_3">
      <div class="fkz_annonces_4">
        <div class="fkz_annonces_5">
          <div class="fkz_annonces_6">
            <div class="fkz_annonces">
	      <div class="fkz_annonces_titre"><b>
	        <span class="fkz_annonces_{$categorie}"></span>
		<span class="fkz_annonces_cat">({$categorie})</span>
		<span class="fkz_annonces_titre">{$annonce.titre}</span>
	      </div></b>
	      <div class="fkz_annonces_corps">
                {if $annonce.img}
                <span class="image" style="display:block;text-align:center">
		  <img src="http://frankiz/data/annonces/{$annonce.id}" alt="logo" />
		</span>
                {/if}
		{$annonce.contenu}
		<p class="fkz_signature"><a href="trombino.php?chercher&loginpoly={$annonce.eleve.login}&promo={$annonce.eleve.promo}">
		  {if $annonce.eleve.surnom}{$annonce.eleve.surnom}{else}{$annonce.eleve.prenom} {$annonce.eleve.nom}{/if}
		  {if $annonce.eleve.promo}({$annonce.eleve.promo}){/if}
		</a></p>
	      </div>
	    </div>
	  </div>
	</div>
      </div>
    </div>
  </div>
</div>
{/foreach}
