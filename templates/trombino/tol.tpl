{css_block class='fkz_trombino'}
{if isset($results)}
{if count($results) > 0}
<span class='note'>{$nbr_results} résultats trouvés</span>
{else}
<span class='warning'>Aucun résultat trouvé</span>
{/if}
{foreach from=$results item=result}
{include file=trombino/resultat.tpl eleve=$result}
{/foreach}
{/if}

{include file=trombino/recherche.tpl}
{/css_block}
