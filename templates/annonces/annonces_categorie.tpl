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
		{$annonce.contenu|wiki_vers_html}
		<p class="fkz_signature">{print_eleve_name eleve=$annonce.eleve show_promo=1}</p>
	      </div>
	    </div>
	  </div>
	</div>
      </div>
    </div>
  </div>
</div>
{/foreach}
