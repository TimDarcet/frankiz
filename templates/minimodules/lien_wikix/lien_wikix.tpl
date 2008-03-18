{if $session->est_interne()}
{assign var=wikix_url value="http://frankiz.polytechnique.fr/eleves/wikix"}
{else}
{assign var=wikix_url value="http://www.polytechnique.fr/eleves/wikix"}
{/if}

<form enctype="multipart/form-data" method="post" id="lien_wiki_x" action="{$wikix_url}/Special:Search">
  <div class="formulaire">
    <input type="hidden" name="go" value="Consulter" />
    <input type="text" id="lien_wiki_xsearch" name="search" value="" />
    <input type="submit" name="ok" value="Chercher"/>
  </div>
</form>
